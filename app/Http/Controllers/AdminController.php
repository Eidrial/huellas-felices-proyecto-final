<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\User;
use App\Models\Estancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    //MASCOTAS

    //listado de TODAS las mascotas creadas (aprobadas y pendientes)
    public function index()
    {
        $mascotas = Mascota::with('dueno')->orderByDesc('created_at')->paginate(6); //trae info del dueño
        return view('admin.mascotas', compact('mascotas'));
    }

    //formulario para editar una mascota (admin)
    public function editarMascota(Mascota $mascota)
    {
        return view('admin.edit-mascota', compact('mascota'));
    }

    //actualizar la mascota desde el admin (sin ajax)
    public function actualizarMascota(Request $request, Mascota $mascota)
    {
        //validar campos del formulario
        $request->validate([
            'nombre' => 'required|string|min:2|max:50',
            'especie' => 'required|in:perro',
            'raza' => 'required|string|min:2|max:80',
            'edad' => 'required|integer|min:1|max:25',
            'peso' => 'required|numeric|min:0.5|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048', //validar imagen
        ]);

        //actualizar datos básicos de la mascota
        $mascota->nombre = $request->nombre;
        $mascota->especie = $request->especie;
        $mascota->raza = $request->raza;
        $mascota->edad = $request->edad;
        $mascota->peso = $request->peso;

        //manejar subida de imagen si hay archivo
        if ($request->hasFile('foto')) {
            //eliminar foto anterior si existe
            if ($mascota->foto && \Storage::disk('public')->exists($mascota->foto)) {
                \Storage::disk('public')->delete($mascota->foto);
            }

            //guardar nueva foto en storage/app/public/mascotas
            $ruta = $request->file('foto')->store('mascotas', 'public');
            $mascota->foto = $ruta; //guardar path en BD
        }

        //guardar cambios en la BD
        $mascota->save();

        //redirigir al listado con mensaje de exito
        return redirect()->route('admin.mascotas.index')
            ->with('success', 'Mascota actualizada correctamente');
    }

    //aprobar / no aprobar mascota (con ajax)
    public function aprobar(Request $request, Mascota $mascota)
    {
        //validar que aprobado venga como 0 o 1
        $request->validate([
            'aprobado' => 'required|in:0,1',
        ]);

        //guardar como entero
        $mascota->aprobado = (int) $request->aprobado;
        $mascota->save();

        //si se aprueba, intentar confirmar estancias pendientes
        $confirmadasAuto = 0;
        $canceladasAuto = 0;

        if ($mascota->aprobado === 1) {
            $pendientes = Estancia::estanciasPendientes()
                ->where('mascota_id', $mascota->id)
                ->orderBy('fecha_entrada')
                ->get();

            foreach ($pendientes as $estancia) {
                if ($estancia->confirmar()) {
                    $confirmadasAuto++;
                } else {
                    //si no hay plaza, se cancela automaticamente
                    $estancia->cancelarSinCobro('admin');
                    $canceladasAuto++;
                }
            }
        } else {
            //si no se aprueba, cancelar automaticamente sus estancias pendientes sin cobrar
            $pendientes = Estancia::estanciasPendientes()
                ->where('mascota_id', $mascota->id)
                ->orderBy('fecha_entrada')
                ->get();

            foreach ($pendientes as $estancia) {
                $estancia->cancelarSinCobro('admin');
                $canceladasAuto++;
            }
        }

        //array para traducir el estado a texto y color
        //1 = aprobada, 0 = no aprobada
        $estados = [
            1 => [
                'texto' => 'Aprobada',
                'etiqueta' => 'text-[#2d5a27]',
                'punto' => 'bg-[#5a9e47]',
            ],
            0 => [
                'texto' => 'No aprobada',
                'etiqueta' => 'text-[#9b2a2a]',
                'punto' => 'bg-[#c9342e]',
            ],
        ];

        $config = $estados[$mascota->aprobado];

        if ($mascota->aprobado === 1) {
            //mensaje informativo
            $message = "Mascota aprobada.";

            $lineas = [];

            //añadir solo si hay valores
            if ($confirmadasAuto > 0) {
                $lineas[] = "Estancias confirmadas: {$confirmadasAuto}.";
            }

            if ($canceladasAuto > 0) {
                $lineas[] = "Estancias canceladas: {$canceladasAuto}.";
            }

            //si hay líneas, las añadimos debajo
            if (!empty($lineas)) {
                $message .= "\n" . implode("\n", $lineas);
            } else {
                //si no hay ninguna, mensaje alternativo
                $message .= "\nNo había estancias pendientes.";
            }
        } else {
            //mensaje informativo
            $message = "Mascota no aprobada.";

            if ($canceladasAuto > 0) {
                $message .= "\nEstancias canceladas: {$canceladasAuto}.";
            } else {
                $message .= "\nNo había estancias pendientes.";
            }
        }

        //devuelve json para que la interfaz se actualice sin recargar la página
        return response()->json([
            'success' => true,
            'aprobado' => $mascota->aprobado, //valor 0 o 1
            'texto' => $config['texto'], //texto del estado
            'etiqueta' => $config['etiqueta'], //clase css del texto
            'punto' => $config['punto'], //clase css del punto
            'message' => $message,
        ]);
    }
    
    //eliminar una mascota (con ajax)
    public function eliminarMascota(Mascota $mascota)
    {

        //no permitir borrar mascotas durante x estancias
        //mensaje específico según el estado de la estancia
        $existeEstancia = Estancia::where('mascota_id', $mascota->id)->whereIn('estado', ['pendiente', 'confirmada', 'activa', 'sin_disponibilidad'])->orderByRaw("FIELD(estado, 'activa', 'confirmada', 'pendiente', 'sin_disponibilidad')")->first();

        if ($existeEstancia) {
            if ($existeEstancia->estado == 'pendiente') {
                $msg = 'No puedes borrar esta mascota porque tiene una estancia pendiente. Cancela la estancia antes de borrarla.';
            } elseif ($existeEstancia->estado == 'confirmada') {
                $msg = 'No puedes borrar esta mascota porque tiene una estancia confirmada. Cancela la estancia antes de borrarla.';
            } elseif ($existeEstancia->estado == 'sin_disponibilidad') {
                $msg = 'No puedes borrar esta mascota porque tiene una estancia sin disponibilidad. Cancela la estancia antes de borrarla.';
            } else { //activa
                $msg = 'No puedes borrar esta mascota porque tiene una estancia activa. Espera a que finalice la estancia para poder borrarla.';
            }

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }

            return redirect()->back()->with('error', $msg);
        }

        //borrar la foto del storage si existe (disk public)
        if ($mascota->foto && \Storage::disk('public')->exists($mascota->foto)) {
            \Storage::disk('public')->delete($mascota->foto);
        }

        $mascota->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mascota eliminada correctamente',
                'id' => $mascota->id
            ]);
        }

        return redirect()->back()->with('success', 'Mascota eliminada correctamente.');
    }

    //USUARIOS

    //listado de todos los usuarios para el panel de admin
    public function usuarios()
    {
        $usuarios = User::orderBy('created_at', 'asc')->paginate(6);
        return view('admin.usuario', compact('usuarios'));
    }

    //cambiar el rol de un usuario concreto (con ajax)
    public function cambiarRol(Request $request, User $user)
    {
        //no permitir cambiar tu propio rol (el admin siempre será admin)
        if (auth()->id() == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No permitido'
            ]);
        }

        //validar que el rol sea uno permitido
        $request->validate([
            'role' => 'required|in:admin,cuidador,usuario'
        ]);

        //actualizar rol
        $user->role = $request->role;
        $user->save();

        //config visual del rol
        $roles = [
            'admin' => [
                'texto' => 'Administración',
                'etiqueta' => 'text-[#9b2a2a]',
                'punto' => 'bg-[#c9342e]',
            ],
            'cuidador' => [
                'texto' => 'Cuidador',
                'etiqueta' => 'text-[#1a4f8a]',
                'punto' => 'bg-[#3a7abf]',
            ],
            'usuario' => [
                'texto' => 'Usuario',
                'etiqueta' => 'text-[#2d5a27]',
                'punto' => 'bg-[#5a9e47]',
            ],
        ];

        $config = $roles[$user->role];

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado',
            'texto' => $config['texto'],
            'etiqueta' => $config['etiqueta'],
            'punto' => $config['punto'],
        ]);
    }

    //eliminar un usuario (con ajax)
    public function eliminarUsuario(User $user)
    {
        if (auth()->id() == $user->id) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propio usuario.']);
            }
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $tieneEstanciasAbiertas = Estancia::whereHas('mascota', function ($consulta) use ($user) {
            $consulta->where('dueno_id', $user->id);
        })
            ->whereIn('estado', ['pendiente', 'confirmada', 'activa', 'sin_disponibilidad'])
            ->exists();

        if ($tieneEstanciasAbiertas) {
            $msg = 'No puedes eliminar este usuario porque tiene estancias pendientes, confirmadas, activas o sin disponibilidad.';

            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }

            return redirect()->back()->with('error', $msg);
        }

        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente',
                'id' => $user->id
            ]);
        }

        return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
    }

    //formulario para crear usuario
    public function crearUsuario()
    {
        return view('admin.crear-usuario');
    }

    //guardar usuario nuevo (son ajax)
    public function guardarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:12|unique:users,dni',
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],  //confirmed obliga a que exista un campo para confirmar la contra y que ambos coincidan
            'role' => 'required|in:admin,cuidador,usuario',
        ]);

        User::create([
            'name' => $request->name,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente.');
    }

    //mostrar formulario
    public function editarUsuario(User $user)
    {
        return view('admin.edit-usuario', ['usuario' => $user]);
    }

    //guardar (sin ajax)
    public function actualizarUsuario(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'apellidos' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:12|unique:users,dni,' . $user->id, //sin ".$user->id" laravel pensaria que el dni actual ya existe y daria error! con esto se mantiene el dni actual
            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, //sin ".$user->id" laravel pensaria que el email actual ya existe y daria error! con esto se mantiene el email actual
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ], //la contraseña es opcional y solo se actualiza si se envia
        ]);

        $user->name = $request->name;
        $user->apellidos = $request->apellidos;
        $user->dni = $request->dni;
        $user->telefono = $request->telefono;
        $user->direccion = $request->direccion;
        $user->email = $request->email;

        //si se ha introducido una nueva contraseña, se reemplaza la anterior (si no, se mantiene la contraseña actual)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    //MASCOTAS

    //listado de TODAS las mascotas creadas (aprobadas y pendientes)
    public function index()
    {
        $mascotas = Mascota::with('dueno')->get(); //trae info del dueño
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
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|max:255',
            'raza' => 'nullable|string|max:255',
            'edad' => 'required|integer|min:0',
            'peso' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', //validar imagen
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
            if ($mascota->foto && \Storage::exists($mascota->foto)) {
                \Storage::delete($mascota->foto);
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

        //array para traducir el estado a texto y color
        //null = pendiente, 1 = aprobada, 0 = no aprobada
        $estados = [
            null => ['texto' => 'Pendiente', 'color' => 'text-yellow-600'],
            1 => ['texto' => 'Aprobada', 'color' => 'text-green-600'],
            0 => ['texto' => 'No aprobada', 'color' => 'text-red-600'],
        ];

        //devuelve json para que la interfaz se actualice sin recargar la página
        return response()->json([
            'success' => true,
            'aprobado' => $mascota->aprobado, //valor 0 o 1
            'texto' => $estados[$mascota->aprobado]['texto'],  //texto
            'color' => $estados[$mascota->aprobado]['color'],  //color
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    //eliminar una mascota (con ajax)
    public function eliminarMascota(Mascota $mascota)
    {
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
        $usuarios = User::all();
        return view('admin.usuario', compact('usuarios'));
    }

    //cambiar el rol de un usuario concreto (con ajax)
    public function cambiarRol(Request $request, User $user)
    {
        //no permitir cambiar tu propio rol (el admin siempre será admin)
        if (auth()->id() == $user->id) {
            return response()->json(['error' => 'No permitido'], 403);
        }

        //validar que el rol sea uno permitido
        $request->validate([
            'role' => 'required|in:admin,cuidador,usuario'
        ]);

        //actualizar rol
        $user->role = $request->role;
        $user->save();

        return response()->json([
            'message' => 'Rol actualizado'
        ]);
    }

    //eliminar un usuario (con ajax)
    public function eliminarUsuario(User $user)
    {
        if (auth()->id() == $user->id) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propio usuario.'], 403);
            }
            return redirect()->back()->with('error', 'No puedes eliminar tu propio usuario.');
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', //confirmed obliga a que exista un campo para confirmar la contra y que ambos coincidan
            'role' => 'required|in:admin,cuidador,usuario',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), //contraseña encriptada
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
            'email' => 'required|email|unique:users,email,' . $user->id, //sin ".$user->id" laravel pensaria que el email actual ya existe y daria error! con esto se mantiene el email actual
            'password' => 'nullable|string|min:6|confirmed', //la contraseña es opcional y solo se actualiza sis e envia
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        //si se ha introducido una nueva contraseña, se encripta y se reemplaza la anterior (si no, se mantiene la contraseña actual)
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

}

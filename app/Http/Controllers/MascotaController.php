<?php

namespace App\Http\Controllers;

use App\Models\Mascota;
use App\Models\Estancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MascotaController extends Controller
{
    //muestra el listado de mascotas del usuario logueado
    public function index()
    {
        //obtener usuario logueado actualmente
        $usuario = Auth::user();

        //obtener sus mascotas
        $mascotas = $usuario->mascotas()->with('estancias')->orderBy('created_at')->paginate(6);

        $totalMascotas = $usuario->mascotas()->count();
        $aprobadas = $usuario->mascotas()->where('aprobado', 1)->count();
        $pendientes = $usuario->mascotas()->whereNull('aprobado')->count();
        $noAprobadas = $usuario->mascotas()->where('aprobado', 0)->count();

        //enviar mascotas a la vista para amostrarlas
        return view('mascotas.index', compact(
            'mascotas',
            'totalMascotas',
            'aprobadas',
            'pendientes',
            'noAprobadas'
        ));
    }

    //miestra el formulario para añadir una mascota
    public function create()
    {
        return view('mascotas.create');
    }

    //guarda la mascota en la base de datos
    public function store(Request $request)
    {
        //validación del formulario
        $request->validate([
            'nombre' => 'required|string|min:2|max:50',
            'especie' => 'required|in:perro',
            'raza' => 'required|string|min:2|max:80',
            'edad' => 'required|integer|min:1|max:25',
            'peso' => 'required|numeric|min:0.5|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $rutaFoto = null;

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')->store('mascotas', 'public');
        }

        //crear la mascota asociada al usuario
        Mascota::create([
            'nombre' => $request->nombre,
            'especie' => $request->especie,
            'raza' => $request->raza,
            'edad' => $request->edad,
            'peso' => $request->peso,
            'foto' => $rutaFoto,
            'dueno_id' => Auth::id(),
            'aprobado' => null, //importante! siempre pendiente al crearla
        ]);

        return redirect()->route('mascotas.index')->with('success', 'Mascota registrada correctamente. Pendiente de aprobación.');
    }

    //mostrar detalles mascota
    public function show(Mascota $mascota)
    {
        if ($mascota->dueno_id != Auth::id()) {
            return redirect()->route('mascotas.index')->with('error', 'No puedes ver esta mascota.');
        }

        return view('mascotas.show', compact('mascota'));
    }

    //formulario para editar (sin ajax)
    public function edit(Mascota $mascota)
    {
        //solo el dueño
        if ($mascota->dueno_id != Auth::id()) {
            return redirect()->route('mascotas.index')->with('error', 'No puedes editar esta mascota.');
        }

        return view('mascotas.edit', compact('mascota'));
    }

    //guardar cambios
    public function update(Request $request, Mascota $mascota)
    {
        //solo el dueño
        if ($mascota->dueno_id != Auth::id()) {
            return redirect()->route('mascotas.index')->with('error', 'No puedes actualizar esta mascota.');
        }

        //validar
        $request->validate([
            'nombre' => 'required|string|min:2|max:50',
            'especie' => 'required|in:perro',
            'raza' => 'required|string|min:2|max:80',
            'edad' => 'required|integer|min:1|max:25',
            'peso' => 'required|numeric|min:0.5|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        //actualizar
        $mascota->nombre = $request->nombre;
        $mascota->especie = $request->especie;
        $mascota->raza = $request->raza;
        $mascota->edad = $request->edad;
        $mascota->peso = $request->peso;

        //si se sube una nueva imagen
        if ($request->hasFile('foto')) {

            //eliminar imagen anterior si existe
            if ($mascota->foto && \Storage::disk('public')->exists($mascota->foto)) {
                \Storage::disk('public')->delete($mascota->foto);
            }

            //guardar nueva imagen
            $ruta = $request->file('foto')->store('mascotas', 'public');
            $mascota->foto = $ruta;
        }

        //guardar cambios en BD
        $mascota->save();

        return redirect()->route('mascotas.index')->with('success', 'Mascota actualizada correctamente.');
    }

    //borrar una mascota (con ajax)
    public function destroy(Mascota $mascota)
    {
        if ($mascota->dueno_id != Auth::id()) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'No puedes borrar esta mascota.']);
            }
            return redirect()->route('mascotas.index')->with('error', 'No puedes borrar esta mascota.');
        }

        //no permitir borrar mascotas durante x estancias
        //mensaje específico segun el estado de la estancia
        //orderByRaw hace que primero se busquen las estancias activas,
        //luego las confirmadas y por ultimo las pendientes (por orden de importancia)
        $existeEstancia = Estancia::where('mascota_id', $mascota->id)
            ->whereIn('estado', ['pendiente', 'confirmada', 'activa', 'sin_disponibilidad'])
            ->orderByRaw("FIELD(estado, 'activa', 'confirmada', 'pendiente', 'sin_disponibilidad')")
            ->first();

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

            return redirect()->route('mascotas.index')->with('error', $msg);
        }

        //borrar foto del storage sie xiste
        if ($mascota->foto && \Storage::disk('public')->exists($mascota->foto)) {
            \Storage::disk('public')->delete($mascota->foto);
        }

        $mascota->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Mascota eliminada correctamente', 'id' => $mascota->id]);
        }

        return redirect()->route('mascotas.index')->with('success', 'Mascota eliminada correctamente.');
    }

}

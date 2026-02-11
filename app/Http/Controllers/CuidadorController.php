<?php

namespace App\Http\Controllers;

use App\Models\Cuidado;
use App\Models\Estancia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CuidadorController extends Controller
{

    //panel principal del cuidador
    public function index()
    {
        //obtener estancias confirmadas o activas
        $estancias = Estancia::estanciasActivas()
            ->with('mascota.dueno')
            ->orderBy('fecha_entrada')
            ->get();

        return view('cuidador.index', compact('estancias'));
    }

    //detalle de estancia
    public function show(Estancia $estancia)
    {
        //solo se ve si esta confirmada o activa
        if ($estancia->estado != 'confirmada' && $estancia->estado != 'activa') {
            return redirect()->route('cuidador.index')
                ->with('error', 'No puedes ver esta estancia.');
        }

        //cargar mascota, dueño y lista de cuidados + usuario
        $estancia->load('mascota.dueno', 'cuidados.usuario');

        return view('cuidador.show', compact('estancia'));
    }

    //crear cuidado (extra)
    public function store(Request $request)
    {
        $request->validate([
            'estancia_id' => 'required|exists:estancias,id',
            'descripcion' => 'required|string|max:255',
            'precio_extra' => 'required|numeric|min:0',
        ]);

        Cuidado::create([
            'estancia_id' => $request->estancia_id,
            'tipo' => 'extra',
            'descripcion' => $request->descripcion,
            'precio_extra' => $request->precio_extra,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Extra añadido correctamente');
    }

    //marcar cuidado como completado
    public function completar(Cuidado $cuidado)
    {
        //para saber que la estancia esta confirmada o activa
        if ($cuidado->estancia->estado != 'confirmada' && $cuidado->estancia->estado != 'activa') {
            return back()->with('error', 'No puedes completar cuidados de una estancia no activa.');
        }

        //evitar volver a completar lo mismo
        if ($cuidado->completado) {
            return back()->with('error', 'Este cuidado ya estaba marcado como realizado.');
        }

        $cuidado->update([
            'completado' => true,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Cuidado marcado como realizado');
    }
}

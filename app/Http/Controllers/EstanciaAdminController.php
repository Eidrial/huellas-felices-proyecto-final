<?php

namespace App\Http\Controllers;

use App\Models\Estancia;
use Illuminate\Http\Request;

class EstanciaAdminController extends Controller
{
    //listado de todas las estancias
    public function index()
    {
        $estancias = Estancia::with('mascota.dueno')->get();
        return view('admin.estancias', compact('estancias'));
    }

    //confirmar estancia
    public function confirmar(Estancia $estancia)
    {
        //cargar mascota por si no esta cargada
        $estancia->load('mascota');

        //comprobar que la estancia tiene mascota asociada
        if (!$estancia->mascota) {
            return back()->with('error', 'Esta estancia no tiene mascota asociada.');
        }

        //si la mascota esta pendiente, aprobarla automaticamente
        if ($estancia->mascota->aprobado === null) {
            $estancia->mascota->aprobado = 1;
            $estancia->mascota->save();
        }

        //confirmar estancia
        if (!$estancia->confirmar()) {
            return back()->with('error', 'No hay disponibilidad.');
        }

        return back()->with('success', 'Estancia confirmada correctamente.');
    }


    //iniciar estancia
    public function iniciar(Estancia $estancia)
    {
        if ($estancia->iniciar()) {
            return back()->with('success', 'Estancia iniciada.');
        }

        return back()->with('error', 'No se puede iniciar.');
    }

    //finalizar estancia
    public function finalizar(Estancia $estancia)
    {
        if ($estancia->finalizar()) {
            return back()->with('success', 'Estancia finalizada.');
        }

        return back()->with('error', 'No se puede finalizar.');
    }

    //cancelar estancia (admin)
    public function cancelar(Estancia $estancia)
    {
        if ($estancia->estado == 'finalizada' || $estancia->estado == 'cancelada') {
            return back()->with('error', 'No se puede cancelar esta estancia.');
        }

        $estancia->cancelar('admin');
        return back()->with('success', 'Estancia cancelada correctamente.');
    }
}

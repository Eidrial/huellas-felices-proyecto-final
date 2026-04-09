<?php

namespace App\Http\Controllers;

use App\Models\Estancia;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstanciaConfirmadaMail;
use App\Mail\FacturaDisponibleMail;

class EstanciaAdminController extends Controller
{
    //listado de todas las estancias
    public function index()
    {
        //obtener estancias con mascota y dueño, ordenadas primero por estado (activas, confirmadas, pendientes, finalizadas, canceladas) y dentro de cada grupo por fecha de entrada
        $estancias = Estancia::with('mascota.dueno')
            ->orderByRaw("FIELD(estado, 'activa', 'confirmada', 'pendiente', 'finalizada', 'cancelada')")
            ->orderBy('fecha_entrada', 'asc')
            ->get();
        return view('admin.estancias', compact('estancias'));
    }

    //confirmar estancia
    public function confirmar(Estancia $estancia)
    {
        //cargar mascota por si no esta cargada
        $mascota = $estancia->mascota;

        //comprobar que la estancia tiene mascota asociada
        if (!$mascota) {
            return back()->with('error', 'Esta estancia no tiene mascota asociada.');
        }

        //si la mascota esta pendiente, aprobarla automaticamente
        if ($mascota->aprobado === null) {
            $mascota->aprobado = 1;
            $mascota->save();
        }

        //intentar confirmar estancia (si no hay disponibilidad, falla)
        if (!$estancia->confirmar()) {
            return back()->with('error', 'No hay disponibilidad.');
        }

        //mandar email al dueño
        $estancia->load('mascota.dueno');

        $emailDueno = $estancia->mascota->dueno->email ?? null;
        //si hay override en .env, mandar ahi (pruebass)
        $destinatario = config('mail.to_override') ? config('mail.to_override') : $emailDueno;

        if ($destinatario) {
            Mail::to($destinatario)->send(new EstanciaConfirmadaMail($estancia));
        }

        return back()->with('success', 'Estancia confirmada correctamente.');
    }

    //iniciar estancia
    public function iniciar(Estancia $estancia)
    {
        //solo se puede iniciar si esta confrimada
        if ($estancia->estado != 'confirmada') {
            return back()->with('error', 'Solo se puede iniciar una estancia confirmada.');
        }

        //solo se puede iniciar a partir del dia de entrada
        $hoy = date('Y-m-d');
        if ($hoy < $estancia->fecha_entrada) {
            return back()->with('error', 'No puedes iniciar una estancia antes del día de entrada.');
        }

        //intentar iniciar
        if (!$estancia->iniciar()) {
            return back()->with('error', 'No se pudo iniciar la estancia.');
        }

        return back()->with('success', 'Estancia iniciada correctamente.');
    }

    //finalizar estancia
    public function finalizar(Estancia $estancia)
    {
        //solo finalizar si esta activa
        if ($estancia->estado != 'activa') {
            return back()->with('error', 'Solo se puede finalizar una estancia activa.');
        }

        //si la estancia termina antes de la fecha prevista, ajustar salida real y recalcular precio
        $hoy = now()->toDateString();
        if ($hoy < $estancia->fecha_salida) {
            $estancia->fecha_salida = $hoy;
            $estancia->calcularPrecioTotal();
            $estancia->save();
        }

        //intentar finalizar
        if (!$estancia->finalizar()) {
            return back()->with('error', 'No se pudo finalizar la estancia.');
        }

        //mandar email al dueño con aviso de factura disponible
        $estancia->load('mascota.dueno');

        $emailDueno = $estancia->mascota->dueno->email ?? null;
        //si hay override en .env, mandar ahi (pruebass)
        $destinatario = config('mail.to_override') ? config('mail.to_override') : $emailDueno;

        if ($destinatario) {
            Mail::to($destinatario)->send(new FacturaDisponibleMail($estancia));
        }

        return back()->with('success', 'Estancia finalizada correctamente.');
    }

    //cancelar estancia (admin)
    public function cancelar(Estancia $estancia)
    {
        //solo permitir cancelar si esta pendiente o confirmada
        if ($estancia->estado != 'pendiente' && $estancia->estado != 'confirmada') {
            return back()->with('error', 'Solo se pueden cancelar estancias pendientes o confirmadas.');
        }

        $estancia->cancelar('admin');

        return back()->with('success', 'Estancia cancelada correctamente.');
    }
}
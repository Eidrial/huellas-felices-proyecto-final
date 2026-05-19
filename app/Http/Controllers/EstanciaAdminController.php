<?php

namespace App\Http\Controllers;

use App\Models\Estancia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\EstanciaConfirmadaMail;
use App\Mail\FacturaDisponibleMail;

class EstanciaAdminController extends Controller
{
    //listado de todas las estancias
    public function index(Request $request)
    {
        //cancelar automaticamente pendientes y sin disponibilidad caducadas sin cobrar
        Estancia::cancelarCaducadasSinCobro();

        $vista = $request->get('vista', 'abiertas');

        //totales para las pestaña
        $totalAbiertas = Estancia::whereIn('estado', [
            'pendiente',
            'confirmada',
            'activa',
            'sin_disponibilidad'
        ])->count();

        $totalHistorial = Estancia::whereIn('estado', [
            'finalizada',
            'cancelada'
        ])->count();

        //consulta base con mascota y dueño
        $consulta = Estancia::with('mascota.dueno');

        //filtrar segun la pestañas
        if ($vista === 'historial') {
            $consulta->whereIn('estado', ['finalizada', 'cancelada']);
        } else {
            $consulta->whereIn('estado', [
                'pendiente',
                'confirmada',
                'activa',
                'sin_disponibilidad'
            ]);
        }

        //ordenar y paginar solo las estancias de esa pestaña (admin)
        $estancias = $consulta
            ->orderByRaw("FIELD(estado, 'activa', 'confirmada', 'pendiente', 'sin_disponibilidad', 'finalizada', 'cancelada')")
            ->orderBy('fecha_entrada', 'asc')
            ->paginate(6)
            ->appends(['vista' => $vista]);

        return view('admin.estancias', compact(
            'estancias',
            'vista',
            'totalAbiertas',
            'totalHistorial'
        ));
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
        $hoy = date('Y-m-d');
        $manana = date('Y-m-d', strtotime('+1 day'));

        if ($hoy < $estancia->fecha_salida) {
            $estancia->fecha_salida = $manana;
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
        //solo permitir cancelar si esta pendiente, sin disponibilidad, confirmada o activa
        if (!$estancia->esPendiente() && !$estancia->esSinDisponibilidad() && !$estancia->esConfirmada() && !$estancia->esActiva()) {
            return back()->with('error', 'No se puede cancelar esta estancia.');
        }

        $hoy = date('Y-m-d');
        $entrada = date('Y-m-d', strtotime($estancia->fecha_entrada));

        //sin disponibilidad = cancelable y sin cobrar
        if ($estancia->esSinDisponibilidad()) {
            $estancia->cancelarSinCobro('admin');

            return back()->with('success', 'Estancia cancelada correctamente.');
        }

        //pendiente = siempre cancelable y sin penalizar
        if ($estancia->esPendiente()) {
            $estancia->cancelarSinCobro('admin');

            return back()->with('success', 'Estancia cancelada correctamente.');
        }

        //confirmada = cancelable
        if ($estancia->esConfirmada()) {

            //si cancela el mismo dia de entrada, se cobra 1 dia
            if ($hoy == $entrada) {
                $estancia->aplicarCancelacionUnDia();

                $estancia->cancelar('admin');

                return back()->with('success', 'Estancia cancelada. Al ser el mismo día de entrada, se cobra 1 día.');
            }

            //si es antes del dia de entrada, se cancela normal, sin penalizar
            if ($hoy < $entrada) {
                $estancia->cancelarSinCobro('admin');

                return back()->with('success', 'Estancia cancelada correctamente.');
            }

            //si ha pasado dia de entrada, se cobra solo el tiempo real
            $estancia->aplicarCancelacionActiva();

            $estancia->cancelar('admin');

            return back()->with('success', 'Estancia cancelada. Se cobrará solo el tiempo que ha estado en la residencia.');
        }

        //activa = se cancela cobrando solo los dias que ha estado
        if ($estancia->esActiva()) {
            $estancia->aplicarCancelacionActiva();

            $estancia->cancelar('admin');

            return back()->with('success', 'Estancia cancelada. Se cobrará solo el tiempo que ha estado en la residencia.');
        }

        return back()->with('error', 'No se pudo cancelar la estancia.');
    }
}
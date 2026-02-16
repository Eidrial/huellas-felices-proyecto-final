<?php

namespace App\Http\Controllers;

use App\Models\Estancia;
use App\Models\Mascota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstanciaController extends Controller
{
    //listado de estancias del usuario logueado
    public function index()
    {
        $estancias = Auth::user()->estancias()->with('mascota')->orderBy('fecha_entrada', 'desc')->get();
        return view('estancias.index', compact('estancias'));
    }

    //formulario para crear nueva estancia
    public function create()
    {
        //incluye pendientes
        $mascotas = Auth::user()->mascotas()->get();
        return view('estancias.create', compact('mascotas'));
    }

    //guardar estancia
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'fecha_entrada' => 'required|date',
            'fecha_salida' => 'required|date|after:fecha_entrada',
        ]);

        $mascota = Mascota::find($request->mascota_id);

        if (!$mascota) {
            return back()->with('error', 'Mascota no encontrada.');
        }

        //asegurar que la mascota es del usuario
        if ($mascota->dueno_id != Auth::id()) {
            return back()->with('error', 'No puedes reservar para una mascota que no es tuya.');
        }

        //limite de estancias por mascota
        $maxPendientes = config('residencia.max_estancias_por_mascota');

        //solo contaran para el maximo las pendientes y las confirmadas, las activas no
        $abiertas = Estancia::where('mascota_id', $mascota->id)->whereIn('estado', ['pendiente', 'confirmada'])->count();

        if ($abiertas >= $maxPendientes) {
            return back()->with('error', 'Esta mascota ya tiene el máximo de estancias pendientes/confirmadas.');
        }

        //validar T+1
        if (!Estancia::fechaValida($request->fecha_entrada)) {
            return back()->with('error', 'La fecha de entrada debe ser al menos mañana.');
        }

        $entrada = new \DateTime($request->fecha_entrada);
        $salida = new \DateTime($request->fecha_salida);
        $dias = $entrada->diff($salida)->days;

        if ($dias > config('residencia.max_dias_estancia')) {
            return back()->with('error', 'La estancia no puede superar los ' . config('residencia.max_dias_estancia') . ' días.');
        }

        //comprobar si coincide con otra estancia de la misma mascota
        $otrasEstancias = Estancia::where('mascota_id', $mascota->id)->whereIn('estado', ['pendiente', 'confirmada', 'activa'])->get();

        $hayConflicto = false;

        foreach ($otrasEstancias as $e) {
            $hayConflicto = $hayConflicto || (
                $request->fecha_entrada < $e->fecha_salida &&
                $request->fecha_salida > $e->fecha_entrada
            );
        }

        if ($hayConflicto) {
            return back()->with('error', 'Esta mascota ya tiene otra estancia entre estas fechas.');
        }

        //estado por defecto
        $estado = 'pendiente';

        //si la mascota esta aprobada, intentar confirmar srgun disponibilidad
        if ($mascota->aprobado == 1 && Estancia::hayDisponibilidad($request->fecha_entrada, $request->fecha_salida)) {
            $estado = 'confirmada';
        }

        $estancia = Estancia::create([
            'mascota_id' => $mascota->id,
            'estado' => $estado,
            'fecha_entrada' => $request->fecha_entrada,
            'fecha_salida' => $request->fecha_salida,
            'precio_dia' => config('residencia.precio_dia'),
        ]);

        $estancia->calcularPrecioTotal();
        $estancia->save();

        return redirect()->route('estancias.index')->with('success', 'Estancia creada correctamente. Recuerda que se paga el primer día.');
    }

    //formulario para editar estancia
    public function edit(Estancia $estancia)
    {
        //saber que la estancia es del usuario
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes editar esta estancia.');
        }

        return view('estancias.edit', compact('estancia'));
    }

    //actualizar estancia (acortar o ampliar si hay espacio)
    public function update(Request $request, Estancia $estancia)
    {

        //saber que la estancia es del usuario
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes actualizar esta estancia.');
        }

        //no permitir editar si esta finalizada o cancelada
        if ($estancia->estado == 'finalizada' || $estancia->estado == 'cancelada') {
            return redirect()->route('estancias.index')->with('error', 'No puedes editar una estancia finalizada o cancelada.');
        }

        $request->validate([
            'fecha_salida' => 'required|date|after:' . $estancia->fecha_entrada,
        ]);

        //al ampliar debe haber disponibilidad
        if (!$estancia->puedeAmpliarse($request->fecha_salida)) {
            return back()->with('error', 'No se puede ampliar la estancia, no hay disponibilidad.');
        }

        $estancia->fecha_salida = $request->fecha_salida;
        $estancia->calcularPrecioTotal();
        $estancia->save();

        return redirect()->route('estancias.index')->with('success', 'Estancia actualizada correctamente.');
    }

    //cancelar estancia
    public function cancelar(Estancia $estancia)
    {
        //solo el dueño
        if ($estancia->mascota->dueno_id != Auth::id()) {
            return redirect()->route('estancias.index')->with('error', 'No puedes cancelar esta estancia.');
        }

        $hoy = date('Y-m-d');

        //pendiente = siempre cancelable y sin penalizar
        if ($estancia->estado == 'pendiente') {
            $estancia->cancelar('usuario');
            return redirect()->route('estancias.index')->with('success', 'Estancia cancelada correctamente.');
        }

        //confirmada = cancelable
        if ($estancia->estado == 'confirmada') {

            //si cancela el mismo dia de entrada, se cobra 1 dia
            if ($hoy == $estancia->fecha_entrada) {
                $estancia->precio_total = $estancia->precio_dia;
                $estancia->save();

                $estancia->cancelar('usuario');

                return redirect()->route('estancias.index')->with('success', 'Estancia cancelada. Al ser el mismo día de entrada, se cobra 1 día.');
            }

            //si es antes del dia de entrada, se cancela normal, sin penalizar
            if ($hoy < $estancia->fecha_entrada) {
                $estancia->cancelar('usuario');
                return redirect()->route('estancias.index')->with('success', 'Estancia cancelada correctamente.');
            }

            //si ha pasado dia de entrada entrada, no se puede cancelar normalmente
            return redirect()->route('estancias.index')->with('error', 'Ya ha pasado el día de entrada. Contacta con administración.');
        }

        //si no es pendiente ni confirmada, no se puede cancelar
        return redirect()->route('estancias.index')->with('error', 'No puedes cancelar esta estancia. Contacta con administración.');

    }
}

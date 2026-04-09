<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Estancia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\AvisoImportanteMail;

class AvisoController extends Controller
{
    //crear aviso (admin/cuidador)
    public function store(Request $request)
    {
        $request->validate([
            'estancia_id' => 'required|exists:estancias,id',
            'tipo' => 'required|in:info,importante',
            'mensaje' => 'required|string|max:1000',
        ]);

        $estancia = Estancia::with('mascota.dueno')->find($request->estancia_id);

        //solo avisos en estancias activas
        if (!$estancia || $estancia->estado != 'activa') {
            return back()->with('error', 'Solo puedes crear avisos en estancias activas.');
        }

        //si ya ha pasado la fecha de salida, no permitir crear avisos
        $hoy = now()->toDateString();

        if ($hoy > $estancia->fecha_salida) {
            return back()->with('error', 'No puedes enviar avisos en una estancia cuya fecha de salida ya ha pasado.');
        }

        //crear aviso
        $aviso = Aviso::create([
            'estancia_id' => $estancia->id,
            'user_id' => Auth::id(),
            'tipo' => $request->tipo,
            'mensaje' => $request->mensaje,
        ]);

        //si es importante, mandar email al dueño
        if ($aviso->tipo === 'importante') {
            //cargar relaciones necesarias para el email
            $aviso->load('estancia.mascota.dueno', 'usuario');

            //correo del dueño (evita errores si alguna relacion no existe)
            $emailDueno = $aviso->estancia->mascota->dueno?->email;

            //si hay override en .env, mandar ahi (pruebass)
            $destinatario = config('mail.to_override') ? config('mail.to_override') : $emailDueno;

            if ($destinatario) {
                Mail::to($destinatario)->send(new AvisoImportanteMail($aviso));
            }
        }

        return back()->with('success', 'Aviso enviado correctamente.');
    }

    //eliminar aviso (solo admin)
    public function borrarAviso(Aviso $aviso)
    {
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'Solo un administrador puede eliminar avisos.');
        }

        $aviso->delete();
        return redirect()->back()->with('success', 'Aviso eliminado.');
    }
}
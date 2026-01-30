<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ComprobarRol
{
    //comprueba que el usuario tenga el rol indicado
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        //si no está autenticado o no tiene el rol correcto
        if (!Auth::check() || Auth::user()->role !== $rol) {
            //redirige al inicio con un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esa página.');
        }

        return $next($request);
    }
}

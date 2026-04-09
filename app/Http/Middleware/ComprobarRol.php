<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ComprobarRol
{
    //comprueba que el usuario tenga el rol indicado
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        //si no está autenticado o no tiene el rol correcto
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            //redirige al inicio con un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes permiso para acceder a esa página.');
        }

        return $next($request);
    }
}

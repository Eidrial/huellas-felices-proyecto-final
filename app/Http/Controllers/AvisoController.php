<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AvisoController extends Controller
{
    //crear aviso
    public function store(Request $request)
    {
        $request->validate([
            'mascota_id' => 'required|exists:mascotas,id',
            'mensaje' => 'required|string|max:1000'
        ]);

        Aviso::create([
            'mascota_id' => $request->mascota_id,
            'user_id' => Auth::id(),
            'mensaje' => $request->mensaje,
        ]);

        return redirect()->back()->with('success', 'Aviso enviado correctamente.');
    }

    //eliminar aviso (solo admin)
    public function destroy(Aviso $aviso)
    {
        $aviso->delete();
        return redirect()->back()->with('success', 'Aviso eliminado.');
    }
}

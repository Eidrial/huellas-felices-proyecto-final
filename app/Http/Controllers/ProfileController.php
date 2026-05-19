<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Estancia;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            //desverificar el nuevo correo
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        //enviar correo de verificacion si el email ha camiado
        if ($request->user()->wasChanged('email')) {
            $request->user()->sendEmailVerificationNotification();

            return Redirect::route('profile.edit')->with('success', 'Te hemos enviado un correo de verificación a tu nueva dirección.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $tieneEstanciasAbiertas = Estancia::whereIn('estado', [
            'pendiente',
            'confirmada',
            'activa',
            'sin_disponibilidad',
        ])->whereIn('mascota_id', $user->mascotas->pluck('id'))->exists();

        if ($tieneEstanciasAbiertas) {
            return back()->with('error', 'No puedes eliminar tu cuenta porque tienes estancias abiertas o pendientes.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

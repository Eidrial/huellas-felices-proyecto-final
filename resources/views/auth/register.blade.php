@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-8">
        <h2 class="text-2xl font-bold text-center mb-2">Crear cuenta</h2>
        <p class="text-center text-gray-600 mb-6">Regístrate en Huellas Felices</p>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block font-medium mb-1">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}"
                    class="w-full border rounded p-2" required autofocus autocomplete="name">
            </div>

            <div>
                <label for="email" class="block font-medium mb-1">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="w-full border rounded p-2" required autocomplete="username">
            </div>

            <div>
                <label for="password" class="block font-medium mb-1">Contraseña</label>
                <input id="password" type="password" name="password"
                    class="w-full border rounded p-2" required autocomplete="new-password">
            </div>

            <div>
                <label for="password_confirmation" class="block font-medium mb-1">Confirmar contraseña</label>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    class="w-full border rounded p-2" required autocomplete="new-password">
            </div>

            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Registrarse
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                Inicia sesión
            </a>
        </p>
    </div>
@endsection
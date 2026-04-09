@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-8">
        <h2 class="text-2xl font-bold text-center mb-2">Iniciar sesión</h2>
        <p class="text-center text-gray-600 mb-6">Accede a tu cuenta de Huellas Felices</p>

        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block font-medium mb-1">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="w-full border rounded p-2"
                    required autofocus autocomplete="username">
            </div>

            <div>
                <label for="password" class="block font-medium mb-1">Contraseña</label>
                <input id="password" type="password" name="password" class="w-full border rounded p-2" required
                    autocomplete="current-password">
            </div>

            <div class="flex items-center justify-between">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="remember">
                    <span>Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline text-sm">
                        ¿Has olvidado tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Iniciar sesión
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
                Regístrate
            </a>
        </p>
    </div>
@endsection
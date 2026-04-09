@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-8">
        <h2 class="text-2xl font-bold text-center mb-2">Recuperar contraseña</h2>
        <p class="text-center text-gray-600 mb-6">
            Indica tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
        </p>

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

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block font-medium mb-1">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}"
                    class="w-full border rounded p-2" required autofocus autocomplete="username">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enviar enlace de recuperación
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                Volver al inicio de sesión
            </a>
        </p>
    </div>
@endsection
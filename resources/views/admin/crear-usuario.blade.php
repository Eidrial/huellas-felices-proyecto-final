@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-center">Crear nuevo usuario</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.usuarios.guardar') }}">
            @csrf

            <!-- nombre -->
            <div class="mb-4">
                <label class="block mb-1">Nombre</label>
                <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
            </div>

            <!-- email -->
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" value="{{ old('email') }}" required>
            </div>

            <!-- contraseña -->
            <div class="mb-4">
                <label class="block mb-1">Contraseña</label>
                <input type="password" name="password" class="w-full border rounded p-2" minlength="6" required>
            </div>

            <!-- confirmar contraseña -->
            <div class="mb-4">
                <label class="block mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
            </div>

            <!-- rol -->
            <div class="mb-4">
                <label class="block mb-1">Rol</label>
                <select name="role" class="w-full border rounded p-2" required>
                    <option value="usuario">Usuario</option>
                    <option value="cuidador">Cuidador</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="bg-green-600 text-black px-4 py-2 rounded hover:bg-green-700">
                Crear usuario
            </button>
        </form>

        <!-- botón para volver a la lista de usuarios -->
        <div class="mb-4 text-left">
            <a href="{{ route('admin.usuarios') }}" class="bg-gray-300 text-black px-4 py-2 rounded hover:bg-gray-400">
                Volver
            </a>
        </div>
    </div>
@endsection
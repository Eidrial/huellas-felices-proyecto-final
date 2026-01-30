@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-center">Editar usuario</h2>

        <!-- mostrar errores de validacion -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.usuarios.actualizar', ['user' => $usuario->id]) }}">
            @csrf
            @method('PUT')

            <!-- nombre -->
            <div class="mb-4">
                <label class="block mb-1">Nombre</label>
                <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name', $usuario->name) }}"
                    required>
            </div>

            <!-- email -->
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2"
                    value="{{ old('email', $usuario->email) }}" required>
            </div>

            <!-- contraseña -->
            <div class="mb-4">
                <label class="block mb-1">Contraseña (dejar vacío para no cambiar)</label>
                <input type="password" name="password" class="w-full border rounded p-2">
            </div>

            <!-- confirmar contraseña -->
            <div class="mb-4">
                <label class="block mb-1">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2">
            </div>

            <!-- botones -->
            <div class="flex justify-between items-center">
                <a href="{{ route('admin.usuarios') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Volver
                </a>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
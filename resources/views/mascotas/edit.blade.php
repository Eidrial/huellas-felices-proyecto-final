@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 shadow rounded mt-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Editar mascota</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('mascotas.update', $mascota) }}" class="bg-white shadow p-6 rounded"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>Nombre</label>
                <input type="text" name="nombre" value="{{ old('nombre', $mascota->nombre) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label>Especie</label>
                <select name="especie" class="w-full border rounded p-2">
                    <!--seleccionar especie, aunque gato está deshabilitado -->
                    <option value="gato" {{ $mascota->especie == 'gato' ? 'selected' : '' }}>Gato</option>
                    <option value="perro" {{ $mascota->especie == 'perro' ? 'selected' : '' }}>Perro</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Raza</label>
                <input type="text" name="raza" value="{{ old('raza', $mascota->raza) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label>Edad</label>
                <input type="number" name="edad" value="{{ old('edad', $mascota->edad) }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label>Peso (kg)</label>
                <input type="number" step="0.1" name="peso" value="{{ old('peso', $mascota->peso) }}" class="w-full border rounded p-2">
            </div>

            <!-- foto de la mascota -->
            <div class="mb-4">
                <label for="foto" class="block font-semibold mb-1">Foto (opcional):</label>
                <input type="file" name="foto" id="foto" class="w-full border rounded p-2" accept="image/*">
                @if($mascota->foto)
                    <!-- mostrar la imagen actual -->
                    <img src="{{ asset('storage/' . $mascota->foto) }}" alt="Foto de {{ $mascota->nombre }}"
                        class="mt-2 w-32 h-32 object-cover rounded border">
                @else
                    <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-500">
                        🐾 <!-- no hay foto -->
                    </div>
                @endif
            </div>

            <button type="submit" class="bg-blue-600 text-black px-4 py-2 rounded hover:bg-blue-700">
                Guardar cambios
            </button>

            <a href="{{ route('mascotas.index') }}"
                class="inline-block mt-2 bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </form>
    </div>
@endsection
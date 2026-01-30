@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Editar Mascota</h2>

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

        <!-- formulario de edicion de mascota -->
        <form action="{{ route('admin.mascotas.actualizar', $mascota) }}" method="POST" class="bg-white shadow p-6 rounded"
            enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- actualizacion -->

            <!-- nombre -->
            <div class="mb-4">
                <label for="nombre" class="block font-semibold mb-1">Nombre:</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $mascota->nombre) }}"
                    class="w-full border rounded p-2" required>
            </div>

            <!-- especie -->
            <div class="mb-4">
                <label for="especie" class="block font-semibold mb-1">Especie:</label>
                <input type="text" name="especie" id="especie" value="{{ old('especie', $mascota->especie) }}"
                    class="w-full border rounded p-2" required>
            </div>

            <!-- raza -->
            <div class="mb-4">
                <label for="raza" class="block font-semibold mb-1">Raza:</label>
                <input type="text" name="raza" id="raza" value="{{ old('raza', $mascota->raza) }}"
                    class="w-full border rounded p-2">
            </div>

            <!-- edad -->
            <div class="mb-4">
                <label for="edad" class="block font-semibold mb-1">Edad:</label>
                <input type="number" name="edad" id="edad" value="{{ old('edad', $mascota->edad) }}"
                    class="w-full border rounded p-2" min="0" required>
            </div>

            <!-- peso -->
            <div class="mb-4">
                <label for="peso" class="block font-semibold mb-1">Peso (kg):</label>
                <input type="number" step="0.1" name="peso" id="peso" value="{{ old('peso', $mascota->peso) }}"
                    class="w-full border rounded p-2" min="0" required>
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

            <!-- botones -->
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.mascotas.index') }}"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancelar</a>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
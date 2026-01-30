@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 shadow rounded mt-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Añadir mascota</h2>

        <!-- mensajes validación si hay -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li> <!-- cada error que viene del backend -->
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- mensaje éxito -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('mascotas.store') }}" class="bg-white shadow p-6 rounded"
            enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="block font-medium mb-1">Nombre</label>
                <!-- old en valor es el campo no se borre en el caso de que haya errores al enviar el formulario,seguirá mostrando lo que envió -->
                <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Especie</label>
                <!-- para elegir la especie -->
                <select name="especie" class="w-full border rounded p-2">
                    <option value="" selected disabled>Selecciona una especie</option>
                    <option value="gato" disabled>Gato (no disponible)</option>
                    <option value="perro">Perro</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Raza</label>
                <input type="text" name="raza" value="{{ old('raza') }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Edad</label>
                <input type="number" name="edad" value="{{ old('edad') }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Peso (kg)</label>
                <input type="number" step="0.1" name="peso" value="{{ old('peso') }}" class="w-full border rounded p-2">
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Foto (opcional)</label>
                <input type="file" name="foto" class="w-full border rounded p-2" accept="image/*">
            </div>

            <!-- botón que envía el formulario -->
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Añadir mascota
            </button>
        </form>

        <a href="{{ route('mascotas.index') }}"class="inline-block mt-4 bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
            Volver al listado de mascotas
        </a>
    </div>
@endsection
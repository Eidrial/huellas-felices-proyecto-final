@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 shadow rounded mt-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Editar estancia</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-gray-100 p-3 rounded mb-4">
            <p><strong>Mascota:</strong> {{ $estancia->mascota->nombre ?? '—' }}</p>
            <p><strong>Entrada:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}</p>
            <p><strong>Salida actual:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($estancia->estado) }}</p>

            <p class="text-sm text-gray-600 mt-1">
                Puedes acortar la estancia siempre. Para ampliarla, debe haber disponibilidad.
            </p>
        </div>

        <form method="POST" action="{{ route('estancias.update', $estancia) }}" class="bg-white shadow p-6 rounded">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-medium mb-1">Nueva fecha de salida</label>

                <input type="date" name="fecha_salida" value="{{ old('fecha_salida', $estancia->fecha_salida) }}"
                    class="w-full border rounded p-2" required>

                <p class="text-xs text-gray-500 mt-1">
                    La salida no cuenta como día de estancia.
                </p>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Guardar cambios
            </button>

            <a href="{{ route('estancias.index') }}"
                class="inline-block mt-2 bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
                Volver
            </a>
        </form>
    </div>
@endsection
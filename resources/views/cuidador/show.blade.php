@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

        <h2 class="text-2xl font-bold mb-4 text-center">
            Estancia: {{ $estancia->mascota->nombre ?? '—' }}
        </h2>

        <div class="bg-gray-100 p-4 rounded mb-6">
            <p><strong>Dueño:</strong> {{ $estancia->mascota->dueno->name ?? '—' }}</p>
            <p><strong>Entrada:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}</p>
            <p><strong>Salida:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($estancia->estado) }}</p>
        </div>

        <h3 class="text-xl font-semibold mb-3">Cuidados / tareas</h3>

        @if($estancia->cuidados->isEmpty())
            <p class="text-gray-600 mb-6">No hay cuidados registrados todavía.</p>
        @else
            <ul class="space-y-2 mb-6">
                @foreach($estancia->cuidados as $cuidado)
                    <li class="border p-3 rounded flex justify-between items-center">
                        <div>
                            <p class="font-semibold">
                                {{ ucfirst($cuidado->tipo) }}
                                @if($cuidado->tipo == 'extra' && $cuidado->precio_extra !== null)
                                    - {{ number_format($cuidado->precio_extra, 2) }} €
                                @endif
                            </p>

                            @if($cuidado->descripcion)
                                <p class="text-sm text-gray-600">{{ $cuidado->descripcion }}</p>
                            @endif

                            <p class="text-xs text-gray-500">
                                Estado:
                                @if($cuidado->completado)
                                    <span class="text-green-600 font-semibold">Hecho</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Pendiente</span>
                                @endif
                            </p>

                            @if($cuidado->user_id)
                                <p class="text-xs text-gray-500">
                                    Hecho por: {{ $cuidado->usuario->name ?? '—' }}
                                </p>
                            @endif
                        </div>

                        @if(!$cuidado->completado)
                            <form method="POST" action="{{ route('cuidador.cuidados.completar', $cuidado) }}">
                                @csrf
                                @method('PUT')
                                <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    Marcar hecho
                                </button>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        <h3 class="text-xl font-semibold mb-3">Añadir extra</h3>

        <form method="POST" action="{{ route('cuidador.cuidados.store') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="estancia_id" value="{{ $estancia->id }}">

            <div>
                <label class="block font-medium mb-1">Descripción</label>
                <input type="text" name="descripcion" class="w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block font-medium mb-1">Precio (€)</label>
                <input type="number" step="0.01" name="precio_extra" class="w-full border rounded p-2" required>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Añadir extra
            </button>
        </form>

        <a href="{{ route('cuidador.index') }}"
            class="inline-block mt-6 bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
            Volver
        </a>

    </div>
@endsection
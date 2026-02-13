@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Panel de cuidador</h2>

        @if($estancias->isEmpty())
            <p class="text-center text-gray-600">No hay estancias confirmadas o activas.</p>
        @else
            <ul class="space-y-3">
                @foreach($estancias as $estancia)
                    <li class="bg-white p-4 rounded shadow flex justify-between items-center">
                        <div>
                            <p class="font-semibold">
                                {{ $estancia->mascota->nombre ?? '—' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Dueño: {{ $estancia->mascota->dueno->name ?? '—' }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Entrada: {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                            </p>

                            <p class="text-sm text-gray-600">
                                Estado: {{ ucfirst($estancia->estado) }}
                            </p>
                        </div>

                        <a href="{{ route('cuidador.estancia.show', $estancia) }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Ver
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
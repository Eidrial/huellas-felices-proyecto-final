@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Mis mascotas</h2>

        <!-- botón para añadir nueva mascota -->
        <a href="{{ route('mascotas.create') }}" class="bg-green-600 text-black px-4 py-2 rounded mb-4 inline-block">
            Añadir mascota
        </a>

        @if($mascotas->isEmpty())
            <p class="text-gray-700">No tienes mascotas registradas actualmente.</p>
        @else
            <ul class="space-y-2">
                @foreach ($mascotas as $mascota)
                    <li class="bg-white shadow p-4 rounded flex justify-between items-center">

                        @if($mascota->foto)
                            <img src="{{ asset('storage/' . $mascota->foto) }}" class="w-16 h-16 object-cover rounded border">
                        @else
                            <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-500">
                                🐾 <!-- no hay foto -->
                            </div>
                        @endif

                        <div>
                            <strong>{{ $mascota->nombre }}</strong> ({{ $mascota->especie }})
                        </div>

                        <div class="space-x-2">
                            <a href="{{ route('mascotas.show', $mascota) }}"
                                class="bg-blue-600 text-black px-3 py-1 rounded hover:bg-blue-700">
                                Ver
                            </a>

                            <a href="{{ route('mascotas.edit', $mascota) }}"
                                class="bg-green-600 text-black px-3 py-1 rounded hover:bg-green-700">
                                Editar
                            </a>

                            <button type="button" class="btn-eliminar-mascota bg-red-600 text-white px-3 py-1 rounded"
                                data-id="{{ $mascota->id }}" data-nombre="{{ $mascota->nombre }}">
                                Borrar
                            </button>
                        </div>

                        <!-- distinguir aprobada / no aprobada / pendiente -->
                        @php
                            if ($mascota->aprobado === 1) {
                                $estadoTexto = 'Mascota aprobada.';
                                $estadoColor = 'text-green-600';
                            } elseif ($mascota->aprobado === 0) {
                                $estadoTexto = 'Mascota NO aprobada.';
                                $estadoColor = 'text-red-600';
                            } else {
                                $estadoTexto = 'Mascota pendiente de aprobar.';
                                $estadoColor = 'text-yellow-600';
                            }
                        @endphp

                        <span class="{{ $estadoColor }}">
                            {{ $estadoTexto }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endsection
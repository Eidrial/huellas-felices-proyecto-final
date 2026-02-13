@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Gestión de mascotas</h2>

        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow rounded overflow-hidden text-center">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3">Foto</th>
                        </th>
                        <th class="p-3">Nombre</th>
                        <th class="p-3">Especie</th>
                        <th class="p-3">Dueño</th>
                        <th class="p-3">Estado</th>
                        <th class="p-3">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        //array para el estado de la mascota
                        //"pendiente" = pendiente, 1 = aprobada, 0 = no aprobada
                        //texto que se mostrara y el color correspondiente
                        $estados = [
                            'pendiente' => ['texto' => 'Pendiente', 'color' => 'text-yellow-600'],
                            1 => ['texto' => 'Aprobada', 'color' => 'text-green-600'],
                            0 => ['texto' => 'No aprobada', 'color' => 'text-red-600'],
                        ];
                    @endphp

                    @foreach($mascotas as $mascota)
                        @php
                            //determinar el estado de cada mascota individualmente
                            //si "aprobado" es null, la mascota esta pendiente
                            //usamos "===" para asegurar que null se detecta correctamente, sino, puede dar errores
                            $estado = $mascota->aprobado === null ? 'pendiente' : $mascota->aprobado;
                        @endphp

                        <tr class="border-t hover:bg-gray-50">
                            <!-- foto de la mascota -->
                            <td class="p-3">
                                @if($mascota->foto)
                                    <img src="{{ asset('storage/' . $mascota->foto) }}" alt="Foto de {{ $mascota->nombre }}"
                                        class="w-16 h-16 object-cover rounded mx-auto">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-500">
                                        🐾 <!-- no hay foto --> 
                                    </div>
                                @endif
                            </td>

                            <td class="p-3">{{ $mascota->nombre }}</td>
                            <td class="p-3">{{ $mascota->especie }}</td>
                            <td class="p-3">{{ $mascota->dueno->name ?? '—' }}</td>

                            <!-- estado de la mascota (pendiente, aprobada, no aprobada) -->
                            <td class="p-3">
                                <!-- muestra el estado con color correspondiente -->
                                <span id="estado-{{ $mascota->id }}" class="{{ $estados[$estado]['color'] }}">
                                    {{ $estados[$estado]['texto'] }}
                                </span>

                                @if($mascota->aprobado === null)
                                    <div class="inline-flex gap-1 ml-2">
                                        <!-- aprobar mascota -->
                                        <button type="button" data-id="{{ $mascota->id }}" data-valor="1"
                                            class="aprobar-btn px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                            Aprobar
                                        </button>

                                        <!-- no aprobar -->
                                        <button type="button" data-id="{{ $mascota->id }}" data-valor="0"
                                            class="aprobar-btn px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                            No aprobar
                                        </button>
                                    </div>
                                @endif
                            </td>

                            <!-- acciones posibles -->
                            <td class="p-3 flex gap-2 justify-center flex-wrap">
                                <!-- editar -->
                                <a href="{{ route('admin.mascotas.editar', $mascota) }}"
                                    class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    editar
                                </a>

                                <!-- eliminar -->
                                <button type="button" class="btn-eliminar-mascota bg-red-600 text-white px-2 py-1 rounded"
                                    data-id="{{ $mascota->id }}" data-nombre="{{ $mascota->nombre }}">
                                    eliminar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
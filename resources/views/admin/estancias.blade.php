@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Gestión de estancias</h2>

        @if($estancias->isEmpty())
            <p class="text-gray-700 text-center">No hay estancias registradas.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow rounded overflow-hidden text-center">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-3">Mascota</th>
                            <th class="p-3">Dueño</th>
                            <th class="p-3">Entrada</th>
                            <th class="p-3">Salida</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Total</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($estancias as $estancia)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3">
                                    {{ $estancia->mascota->nombre ?? '—' }}
                                </td>

                                <td class="p-3">
                                    {{ $estancia->mascota->dueno->name ?? '—' }}
                                </td>

                                <td class="p-3">
                                    {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                </td>

                                <td class="p-3">
                                    {{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}
                                </td>

                                <td class="p-3">
                                    @if($estancia->estado == 'pendiente')
                                        <span class="text-yellow-600 font-semibold">Pendiente</span>
                                    @elseif($estancia->estado == 'confirmada')
                                        <span class="text-green-600 font-semibold">Confirmada</span>
                                    @elseif($estancia->estado == 'activa')
                                        <span class="text-blue-600 font-semibold">Activa</span>
                                    @elseif($estancia->estado == 'finalizada')
                                        <span class="text-gray-600 font-semibold">Finalizada</span>
                                    @elseif($estancia->estado == 'cancelada')
                                        <span class="text-red-600 font-semibold">Cancelada</span>
                                    @endif
                                </td>

                                <td class="p-3">
                                    {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                </td>

                                <td class="p-3">
                                    <div class="flex gap-2 justify-center flex-wrap">

                                        <!-- Confirmar -->
                                        @if($estancia->estado == 'pendiente')
                                            @php
                                                $msgConfirmar = '¿Seguro que quieres confirmar esta estancia?';

                                                //si la mascota no esta aprobada aun
                                                if ($estancia->mascota && $estancia->mascota->aprobado === null) {
                                                    $msgConfirmar = 'Esta mascota todavía está pendiente de aprobación. Si confirmas esta estancia, la mascota se aprobará automáticamente. ¿Continuar?';
                                                }
                                            @endphp

                                            <form id="form-confirmar-estancia-{{ $estancia->id }}" method="POST"
                                                action="{{ route('admin.estancias.confirmar', $estancia) }}" class="hidden">
                                                @csrf
                                                @method('PUT')
                                            </form>

                                            <button type="button"
                                                class="btn-confirmar-estancia-admin bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
                                                data-id="{{ $estancia->id }}" data-msg="{{ $msgConfirmar }}">
                                                Confirmar
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
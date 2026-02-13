@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Mis estancias</h2>

        <a href="{{ route('estancias.create') }}" class="bg-green-600 text-black px-4 py-2 rounded mb-4 inline-block">
            Reservar estancia
        </a>

        @if($estancias->isEmpty())
            <p class="text-gray-700">No tienes estancias registradas actualmente.</p>
        @else
            @php
                $hoy = date('Y-m-d');

                $estados = [
                    'pendiente' => ['texto' => 'Pendiente', 'color' => 'text-yellow-600'],
                    'confirmada' => ['texto' => 'Confirmada', 'color' => 'text-green-600'],
                    'activa' => ['texto' => 'Activa', 'color' => 'text-blue-600'],
                    'finalizada' => ['texto' => 'Finalizada', 'color' => 'text-gray-600'],
                    'cancelada' => ['texto' => 'Cancelada', 'color' => 'text-red-600'],
                ];
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow rounded overflow-hidden text-center">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-3">Mascota</th>
                            <th class="p-3">Entrada</th>
                            <th class="p-3">Salida</th>
                            <th class="p-3">Estado</th>
                            <th class="p-3">Total</th>
                            <th class="p-3">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($estancias as $estancia)
                            @php
                                $estado = $estados[$estancia->estado] ?? ['texto' => $estancia->estado, 'color' => 'text-gray-600'];
                                $entrada = $estancia->fecha_entrada; //viene en Y-m-d
                            @endphp

                            <tr class="border-t hover:bg-gray-50">
                                <td class="p-3">{{ $estancia->mascota->nombre ?? '—' }}</td>

                                <td class="p-3">{{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}</td>

                                <td class="p-3">{{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}</td>

                                <td class="p-3">
                                    <span class="{{ $estado['color'] }} font-semibold">
                                        {{ $estado['texto'] }}
                                    </span>
                                </td>

                                <td class="p-3">
                                    {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                </td>

                                <td class="p-3">
                                    <div class="flex gap-2 justify-center flex-wrap">

                                        <!-- EDITAR: pendiente / confirmada / activa -->
                                        @if($estancia->estado == 'pendiente' || $estancia->estado == 'confirmada' || $estancia->estado == 'activa')
                                            <a href="{{ route('estancias.edit', $estancia) }}"
                                                class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                                Editar
                                            </a>
                                        @endif

                                        <!-- CANCELAR: solo pendiente o confirmada (activa NO) -->
                                        @if($estancia->estado == 'pendiente' || $estancia->estado == 'confirmada')

                                            <!-- form oculto que se va a enviar desde el modal -->
                                            <form id="form-cancelar-{{ $estancia->id }}" method="POST"
                                                action="{{ route('estancias.cancelar', $estancia) }}" class="hidden">
                                                @csrf
                                                @method('PUT')
                                            </form>

                                            @php
                                                $msg = '¿Seguro que quieres cancelar esta estancia?';

                                                //si cancela el mismo dia de entrada y esta confirmada = se cobra un dia de estancia igualmente
                                                if ($estancia->estado == 'confirmada' && $hoy == $entrada) {
                                                    $precioDia = number_format($estancia->precio_dia, 2);
                                                    $msg = "Vas a cancelar el mismo día de entrada. Se cobrará 1 día igualmente ({$precioDia} €). ¿Continuar?";
                                                }
                                            @endphp

                                            <button type="button"
                                                class="btn-cancelar-estancia bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                                                data-id="{{ $estancia->id }}" data-msg="{{ $msg }}">
                                                Cancelar
                                            </button>
                                        @endif
                                    </div>

                                    <!-- mensajes informativos -->
                                    @if($estancia->estado == 'confirmada')
                                        <p class="text-xs text-gray-500 mt-2">
                                            RECORDATORIO: Se paga el primer día, al entregar el perro.
                                        </p>

                                        @if($hoy == $entrada)
                                            <p class="text-xs text-red-600 mt-1">
                                                Si cancelas hoy, se cobrará 1 día igualmente.
                                            </p>
                                        @endif
                                    @endif

                                    @if($estancia->estado == 'pendiente')
                                        <p class="text-xs text-gray-500 mt-2">
                                            Reserva pendiente de aprobación o disponibilidad.
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif
    </div>
@endsection
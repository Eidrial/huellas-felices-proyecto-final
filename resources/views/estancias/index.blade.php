@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>

            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                Tu cuenta
            </p>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
                <div>
                    <h2 class="font-serif text-3xl font-medium text-[#f0ede6] mb-2">
                        Mis estancias
                    </h2>

                    <p class="text-sm text-[#9fcf8e]">
                        Consulta el estado de tus reservas y accede a facturas e historial.
                    </p>
                </div>

                <a href="{{ route('estancias.create') }}"
                    class="shrink-0 inline-flex items-center gap-2 bg-[#f0ede6] text-[#2d5a27] text-sm font-medium px-5 py-3 rounded-xl hover:bg-white transition-colors duration-200">
                    <span class="w-2 h-2 rounded-full bg-[#2d5a27]"></span>
                    Reservar estancia
                </a>
            </div>
        </div>

        <!-- pestaña -->
        @php
            $vistaActual = $vista ?? request('vista', 'abiertas');

            $estanciasVista = $estancias;
        @endphp

        <div class="flex gap-1 mb-2 bg-[#f7f5f0] border border-[#d9ddd0] rounded-xl p-1 w-fit">
            <a href="{{ route('estancias.index', ['vista' => 'abiertas']) }}"
                class="text-sm px-4 py-2 rounded-lg transition-colors duration-200 {{ $vistaActual === 'abiertas' ? 'bg-white border border-[#d9ddd0] text-[#1e2e1a] font-medium shadow-sm' : 'text-[#8a8e84] hover:text-[#1e2e1a]' }}">
                Abiertas

                @if($totalAbiertas > 0)
                    <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $vistaActual === 'abiertas' ? 'bg-[#eef5e8] text-[#2d5a27]' : 'bg-[#e8e5de] text-[#8a8e84]' }}">
                        {{ $totalAbiertas }}
                    </span>
                @endif
            </a>

            <a href="{{ route('estancias.index', ['vista' => 'historial']) }}"
                class="text-sm px-4 py-2 rounded-lg transition-colors duration-200 {{ $vistaActual === 'historial' ? 'bg-white border border-[#d9ddd0] text-[#1e2e1a] font-medium shadow-sm' : 'text-[#8a8e84] hover:text-[#1e2e1a]' }}">
                Historial

                @if($totalHistorial > 0)
                    <span class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $vistaActual === 'historial' ? 'bg-[#f7f5f0] text-[#8a8e84] border border-[#d9ddd0]' : 'bg-[#e8e5de] text-[#8a8e84]' }}">
                        {{ $totalHistorial }}
                    </span>
                @endif
            </a>
        </div>

        <p class="text-xs text-[#8a8e84] mb-5">
            {{ $vistaActual === 'abiertas'
                ? 'Estancias abiertas — pendientes, confirmadas, activas y sin disponibilidad.'
                : 'Estancias finalizadas y canceladas — solo consulta.' }}
        </p>

        <!-- si no hay estancias -->
        @if($estanciasVista->isEmpty())
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-[#eef5e8] border border-[#c8d9be] rounded-2xl flex items-center justify-center text-3xl">
                    🐾
                </div>

                <p class="text-[#8a8e84] text-sm">
                    {{ $vistaActual === 'abiertas'
                        ? 'No tienes estancias abiertas actualmente.'
                        : 'No tienes estancias en el historial.' }}
                </p>
            </div>
        @else

            <!-- escritorio -->
            <div class="hidden xl:block bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b border-[#e8e5de]">
                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Mascota
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Entrada
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Salida
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Estado
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Información
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Total
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#f0ede6]">
                            @foreach($estanciasVista as $estancia)
                                @php
                                    //datos visuales del estado
                                    $estadoVisual = $estancia->getEstadoVisual();

                                    //datos de fechas y cuidados
                                    $diasParaEntrada = $estancia->diasParaEntrada();
                                    $diasActiva = $estancia->diasActiva();

                                    //si hay, poner cantidad, si no, 0
                                    $pendientes = $pendientesHoy[$estancia->id] ?? 0;
                                @endphp

                                <tr class="hover:bg-[#fafaf8] transition-colors duration-150">

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-[#1e2e1a]">
                                            {{ $estancia->mascota->nombre ?? '—' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5 text-[#1e2e1a] whitespace-nowrap">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </td>

                                    <td class="px-5 py-3.5 text-[#1e2e1a] whitespace-nowrap">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $estadoVisual['punto'] }}"></span>

                                            <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                                {{ $estadoVisual['texto'] }}
                                            </span>
                                        </div>

                                        @if($estancia->esCancelada() && $estancia->cancelada_por)
                                            <p class="text-xs text-[#8a8e84] mt-0.5">
                                                por {{ $estancia->cancelada_por == 'admin' ? 'Administración' : 'Usuario' }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <div class="space-y-1">
                                            @if($estancia->esConfirmada())
                                                @if($diasParaEntrada > 1)
                                                    <p class="text-xs text-[#8a8e84]">Empieza en {{ $diasParaEntrada }} días</p>
                                                @elseif($diasParaEntrada == 1)
                                                    <p class="text-xs text-[#8a8e84]">Empieza mañana</p>
                                                @else
                                                    <p class="text-xs font-medium text-[#1a4f8a]">Hoy entra en residencia</p>
                                                @endif

                                                <p class="text-xs text-[#8a8e84]">Recordatorio: se paga al entregar el perro.</p>

                                                @if($estancia->entraHoy())
                                                    <p class="text-xs text-[#9b2a2a]">Si cancelas hoy, se cobrará 1 día igualmente.</p>
                                                @endif
                                            @endif

                                            @if($estancia->esPendiente())
                                                <p class="text-xs text-[#8a8e84]">Reserva pendiente de aprobación.</p>

                                                @if($diasParaEntrada == 1)
                                                    <p class="text-xs font-medium text-[#9b2a2a]">PENDIENTE URGENTE</p>
                                                @endif
                                            @endif

                                            @if($estancia->esSinDisponibilidad())
                                                <p class="text-xs text-[#9b2a2a]">No hay plazas disponibles para estas fechas.</p>
                                                <p class="text-xs text-[#8a8e84]">Puedes modificar las fechas o cancelar esta solicitud.</p>
                                                <p class="text-xs text-[#8a8e84]">Reserva pendiente de disponibilidad.</p>
                                            @endif

                                            @if($estancia->esActiva())
                                                <p class="text-xs text-[#8a8e84]">
                                                    En residencia desde hace {{ $diasActiva }} {{ $diasActiva == 1 ? 'día' : 'días' }}
                                                </p>

                                                <p class="text-xs text-[#8a8e84]">
                                                    @if($pendientes == 0)
                                                        Sin cuidados pendientes hoy
                                                    @elseif($pendientes == 1)
                                                        Queda 1 cuidado pendiente hoy
                                                    @else
                                                        Hoy quedan {{ $pendientes }} cuidados pendientes
                                                    @endif
                                                </p>
                                            @endif

                                            @if($estancia->esCancelada())
                                                <p class="text-xs text-[#8a8e84]">
                                                    Cancelada el {{ date('d/m/Y H:i', strtotime($estancia->updated_at)) }}
                                                </p>

                                                @if($estancia->precio_total > 0)
                                                    <p class="text-xs text-[#8a8e84]">Cancelada con cobro aplicado — la factura sigue disponible.</p>
                                                @else
                                                    <p class="text-xs text-[#8a8e84]">Cancelada sin coste.</p>
                                                @endif
                                            @endif

                                            @if($estancia->esFinalizada())
                                                <p class="text-xs text-[#8a8e84]">
                                                    Finalizada el {{ date('d/m/Y H:i', strtotime($estancia->updated_at)) }}
                                                </p>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-3.5 font-medium text-[#1e2e1a] whitespace-nowrap">
                                        {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($estancia->esActiva() || $estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                                <a href="{{ route('estancias.factura', $estancia) }}"
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                    Factura
                                                </a>
                                            @endif

                                            @if($estancia->esActiva() || $estancia->esFinalizada())
                                                <a href="{{ route('estancias.historial', $estancia) }}"
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                                    Historial
                                                </a>

                                                <a href="{{ route('estancias.avisos', $estancia) }}"
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                                    Avisos
                                                </a>
                                            @endif

                                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                                <a href="{{ route('estancias.edit', $estancia) }}"
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                                    Editar
                                                </a>
                                            @endif

                                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esSinDisponibilidad())
                                                <form id="form-cancelar-{{ $estancia->id }}" method="POST"
                                                    action="{{ route('estancias.cancelar', $estancia) }}" class="hidden">
                                                    @csrf
                                                    @method('PUT')
                                                </form>

                                                <button type="button"
                                                    class="btn-cancelar-estancia text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                    data-id="{{ $estancia->id }}"
                                                    data-msg="{{ $estancia->mensajeCancelacion() }}">
                                                    Cancelar
                                                </button>
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

            <!-- tablet -->
            <div class="hidden md:grid xl:hidden grid-cols-2 gap-4">
                @foreach($estanciasVista as $estancia)
                    @php
                        //datos visuales del estado
                        $estadoVisual = $estancia->getEstadoVisual();

                        //datos de fechas y cuidados
                        $diasParaEntrada = $estancia->diasParaEntrada();
                        $diasActiva = $estancia->diasActiva();

                        //si hay, poner cantidad, si no, 0
                        $pendientes = $pendientesHoy[$estancia->id] ?? 0;
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                        <div class="p-5 flex-1">

                            <!-- cabecera de la tarjeta -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-[#1e2e1a] truncate">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5 truncate">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }} →
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>
                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">{{ $estadoVisual['texto'] }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">Entrada</p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">Salida</p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>
                            </div>

                            <!-- info segun estado -->
                            <div class="mb-3 space-y-1">
                                @if($estancia->esConfirmada())
                                    @if($diasParaEntrada > 1)
                                        <p class="text-xs text-[#8a8e84]">Empieza en {{ $diasParaEntrada }} días</p>
                                    @elseif($diasParaEntrada == 1)
                                        <p class="text-xs text-[#8a8e84]">Empieza mañana</p>
                                    @else
                                        <p class="text-xs font-medium text-[#1a4f8a]">Hoy entra en residencia</p>
                                    @endif

                                    @if($estancia->entraHoy())
                                        <p class="text-xs text-[#9b2a2a]">Si cancelas hoy, se cobrará 1 día igualmente.</p>
                                    @endif
                                @endif

                                @if($estancia->esPendiente())
                                    <p class="text-xs text-[#8a8e84]">Reserva pendiente de aprobación.</p>

                                    @if($diasParaEntrada == 1)
                                        <p class="text-xs font-medium text-[#9b2a2a]">PENDIENTE URGENTE</p>
                                    @endif
                                @endif

                                @if($estancia->esSinDisponibilidad())
                                    <p class="text-xs text-[#9b2a2a]">No hay plazas disponibles para estas fechas.</p>
                                    <p class="text-xs text-[#8a8e84]">Reserva pendiente de disponibilidad.</p>
                                @endif

                                @if($estancia->esActiva())
                                    <p class="text-xs text-[#8a8e84]">
                                        @if($pendientes == 0)
                                            Sin cuidados pendientes hoy
                                        @elseif($pendientes == 1)
                                            Queda 1 cuidado pendiente hoy
                                        @else
                                            Hoy quedan {{ $pendientes }} cuidados pendientes
                                        @endif
                                    </p>
                                @endif

                                @if($estancia->esCancelada() && $estancia->cancelada_por)
                                    <p class="text-xs text-[#8a8e84]">
                                        Cancelada por {{ $estancia->cancelada_por == 'admin' ? 'Administración' : 'Usuario' }}
                                    </p>
                                @endif
                            </div>

                            <p class="text-sm font-medium text-[#1e2e1a]">
                                {{ number_format($estancia->precio_total ?? 0, 2) }} €
                            </p>

                        </div>

                        <!-- total + acciones -->
                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2 flex-wrap">
                            @if($estancia->esActiva() || $estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                <a href="{{ route('estancias.factura', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Factura
                                </a>
                            @endif

                            @if($estancia->esActiva() || $estancia->esFinalizada())
                                <a href="{{ route('estancias.historial', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                    Historial
                                </a>

                                <a href="{{ route('estancias.avisos', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                    Avisos
                                </a>
                            @endif

                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                <a href="{{ route('estancias.edit', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                    Editar
                                </a>
                            @endif

                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esSinDisponibilidad())
                                <form id="form-cancelar-{{ $estancia->id }}" method="POST"
                                    action="{{ route('estancias.cancelar', $estancia) }}" class="hidden">
                                    @csrf
                                    @method('PUT')
                                </form>

                                <button type="button"
                                    class="btn-cancelar-estancia text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                    data-id="{{ $estancia->id }}"
                                    data-msg="{{ $estancia->mensajeCancelacion() }}">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- movil -->
            <div class="md:hidden space-y-3">
                @foreach($estanciasVista as $estancia)
                    @php
                        //datos visuales del estado
                        $estadoVisual = $estancia->getEstadoVisual();

                        //datos de fechas y cuidados
                        $diasParaEntrada = $estancia->diasParaEntrada();
                        $diasActiva = $estancia->diasActiva();

                        //si hay, poner cantidad, si no, 0
                        $pendientes = $pendientesHoy[$estancia->id] ?? 0;
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                        <div class="p-4">

                            <!-- cabecera de la tarjeta -->
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div>
                                    <p class="font-medium text-[#1e2e1a]">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }} →
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>
                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">{{ $estadoVisual['texto'] }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">Entrada</p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">Salida</p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>
                            </div>

                            <!-- info segun estado -->
                            <div class="mb-3 space-y-1">
                                @if($estancia->esConfirmada())
                                    @if($diasParaEntrada > 1)
                                        <p class="text-xs text-[#8a8e84]">Empieza en {{ $diasParaEntrada }} días</p>
                                    @elseif($diasParaEntrada == 1)
                                        <p class="text-xs text-[#8a8e84]">Empieza mañana</p>
                                    @else
                                        <p class="text-xs font-medium text-[#1a4f8a]">Hoy entra en residencia</p>
                                    @endif

                                    @if($estancia->entraHoy())
                                        <p class="text-xs text-[#9b2a2a]">Si cancelas hoy, se cobrará 1 día igualmente.</p>
                                    @endif
                                @endif

                                @if($estancia->esPendiente())
                                    <p class="text-xs text-[#8a8e84]">Reserva pendiente de aprobación.</p>

                                    @if($diasParaEntrada == 1)
                                        <p class="text-xs font-medium text-[#9b2a2a]">PENDIENTE URGENTE</p>
                                    @endif
                                @endif

                                @if($estancia->esSinDisponibilidad())
                                    <p class="text-xs text-[#9b2a2a]">No hay plazas disponibles para estas fechas.</p>
                                    <p class="text-xs text-[#8a8e84]">Reserva pendiente de disponibilidad.</p>
                                @endif

                                @if($estancia->esActiva())
                                    <p class="text-xs text-[#8a8e84]">
                                        @if($pendientes == 0)
                                            Sin cuidados pendientes hoy
                                        @elseif($pendientes == 1)
                                            Queda 1 cuidado pendiente hoy
                                        @else
                                            Hoy quedan {{ $pendientes }} cuidados pendientes
                                        @endif
                                    </p>
                                @endif

                                @if($estancia->esCancelada() && $estancia->cancelada_por)
                                    <p class="text-xs text-[#8a8e84]">
                                        Cancelada por {{ $estancia->cancelada_por == 'admin' ? 'Administración' : 'Usuario' }}
                                    </p>
                                @endif
                            </div>

                            <p class="text-sm font-medium text-[#1e2e1a]">
                                {{ number_format($estancia->precio_total ?? 0, 2) }} €
                            </p>

                        </div>

                        <!-- total + acciones -->
                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2 flex-wrap">
                            @if($estancia->esActiva() || $estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                <a href="{{ route('estancias.factura', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Factura
                                </a>
                            @endif

                            @if($estancia->esActiva() || $estancia->esFinalizada())
                                <a href="{{ route('estancias.historial', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                    Historial
                                </a>

                                <a href="{{ route('estancias.avisos', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                    Avisos
                                </a>
                            @endif

                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                <a href="{{ route('estancias.edit', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                    Editar
                                </a>
                            @endif

                            @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esSinDisponibilidad())
                                <form id="form-cancelar-{{ $estancia->id }}" method="POST"
                                    action="{{ route('estancias.cancelar', $estancia) }}" class="hidden">
                                    @csrf
                                    @method('PUT')
                                </form>

                                <button type="button"
                                    class="btn-cancelar-estancia text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                    data-id="{{ $estancia->id }}"
                                    data-msg="{{ $estancia->mensajeCancelacion() }}">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- paginacion -->
        <div class="mt-8">
            {{ $estancias->links() }}
        </div>

    </div>
@endsection
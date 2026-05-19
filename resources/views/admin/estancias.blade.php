@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>

            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                Administración
            </p>

            <h2 class="font-serif text-3xl font-medium text-[#f0ede6]">
                Gestión de estancias
            </h2>
        </div>

        <!-- pestaña -->
        @php
            $vistaActual = $vista ?? request('vista', 'abiertas');
        @endphp

        <div class="flex gap-1 mb-2 bg-[#f7f5f0] border border-[#d9ddd0] rounded-xl p-1 w-fit">
            <a href="{{ route('admin.estancias.index', ['vista' => 'abiertas']) }}"
                class="text-sm px-4 py-2 rounded-lg transition-colors duration-200 {{ $vistaActual === 'abiertas' ? 'bg-white border border-[#d9ddd0] text-[#1e2e1a] font-medium shadow-sm' : 'text-[#8a8e84] hover:text-[#1e2e1a]' }}">
                Abiertas

                @if($totalAbiertas > 0)
                    <span
                        class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $vistaActual === 'abiertas' ? 'bg-[#eef5e8] text-[#2d5a27]' : 'bg-[#e8e5de] text-[#8a8e84]' }}">
                        {{ $totalAbiertas }}
                    </span>
                @endif
            </a>

            <a href="{{ route('admin.estancias.index', ['vista' => 'historial']) }}"
                class="text-sm px-4 py-2 rounded-lg transition-colors duration-200 {{ $vistaActual === 'historial' ? 'bg-white border border-[#d9ddd0] text-[#1e2e1a] font-medium shadow-sm' : 'text-[#8a8e84] hover:text-[#1e2e1a]' }}">
                Historial

                @if($totalHistorial > 0)
                    <span
                        class="ml-1.5 text-xs px-1.5 py-0.5 rounded-full {{ $vistaActual === 'historial' ? 'bg-[#f7f5f0] text-[#8a8e84] border border-[#d9ddd0]' : 'bg-[#e8e5de] text-[#8a8e84]' }}">
                        {{ $totalHistorial }}
                    </span>
                @endif
            </a>
        </div>

        <p class="text-xs text-[#8a8e84] mb-5">
            {{ $vistaActual === 'abiertas'
        ? 'Estancias abiertas — pendientes, confirmadas, activas y sin disponibilidad. Las pendientes urgentes aparecen avisadas el día anterior a la entrada.'
        : 'Estancias finalizadas y canceladas — solo consulta.' }}
        </p>

        @if($estancias->isEmpty())

            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-12 text-center">
                <p class="text-[#8a8e84] text-sm">
                    {{ $vistaActual === 'abiertas'
                ? 'No hay estancias abiertas actualmente.'
                : 'No hay estancias en el historial.' }}
                </p>
            </div>

        @elseif($vistaActual === 'abiertas')

            <!-- VISTA ABIERTAS-->

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
                                    Avisos
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Dueño
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
                                    Total
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#f0ede6]">
                            @foreach($estancias as $estancia)
                                @php
                                    $estadoVisual = $estancia->getEstadoVisual();
                                    $avisosAdmin = $estancia->getAvisosAdmin();
                                @endphp

                                <tr class="hover:bg-[#fafaf8] transition-colors duration-150">

                                    <!-- mascota -->
                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-[#1e2e1a]">
                                            {{ $estancia->mascota->nombre ?? '—' }}
                                        </p>
                                    </td>

                                    <!-- avisos -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($avisosAdmin as $aviso)
                                                <span class="px-2 py-0.5 text-xs rounded-full {{ $aviso['clase'] }}">
                                                    {{ $aviso['texto'] }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-[#c0bdb8]">—</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- dueño -->
                                    <td class="px-5 py-3.5 text-[#1e2e1a]">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
                                    </td>

                                    <!-- fechas -->
                                    <td class="px-5 py-3.5 text-[#1e2e1a] whitespace-nowrap">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </td>

                                    <td class="px-5 py-3.5 text-[#1e2e1a] whitespace-nowrap">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </td>

                                    <!-- estado -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $estadoVisual['punto'] }}"></span>

                                            <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                                {{ $estadoVisual['texto'] }}
                                            </span>
                                        </div>

                                        @if($estancia->pendienteIniciar())
                                            <p class="text-xs text-[#7a4e10] mt-0.5">
                                                pendiente de iniciar
                                            </p>
                                        @endif

                                        @if($estancia->pendienteFinalizar())
                                            <p class="text-xs text-[#7a3a10] mt-0.5">
                                                pendiente de finalizar
                                            </p>
                                        @endif

                                        @if($estancia->esSinDisponibilidad())
                                            <p class="text-xs text-[#9b2a2a] mt-0.5">
                                                sin plazas disponibles
                                            </p>
                                        @endif
                                    </td>

                                    <!-- total -->
                                    <td class="px-5 py-3.5 font-medium text-[#1e2e1a] whitespace-nowrap">
                                        {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                    </td>

                                    <!-- acciones -->
                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-col gap-2">

                                            <!-- acciones de gestion -->
                                            <div class="flex flex-wrap gap-1.5">

                                                @if($estancia->esPendiente())
                                                    <form id="form-confirmar-estancia-{{ $estancia->id }}" method="POST"
                                                        action="{{ route('admin.estancias.confirmar', $estancia) }}" class="hidden">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>

                                                    <button type="button"
                                                        class="btn-confirmar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200"
                                                        data-id="{{ $estancia->id }}"
                                                        data-msg="{{ $estancia->mensajeConfirmacionAdmin() }}">
                                                        Confirmar
                                                    </button>
                                                @endif

                                                @if($estancia->pendienteIniciar())
                                                    <form id="form-iniciar-estancia-{{ $estancia->id }}" method="POST"
                                                        action="{{ route('admin.estancias.iniciar', $estancia) }}" class="hidden">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>

                                                    <button type="button"
                                                        class="btn-iniciar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cef0] text-[#1a4f8a] hover:bg-[#e6f0fb] transition-colors duration-200"
                                                        data-id="{{ $estancia->id }}"
                                                        data-msg="¿Seguro que quieres iniciar esta estancia? (Se considera como entrada oficial en la residencia)">
                                                        Iniciar
                                                    </button>
                                                @endif

                                                @if($estancia->esActiva())
                                                    <form id="form-finalizar-estancia-{{ $estancia->id }}" method="POST"
                                                        action="{{ route('admin.estancias.finalizar', $estancia) }}" class="hidden">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>

                                                    <button type="button"
                                                        class="btn-finalizar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:bg-[#f7f5f0] transition-colors duration-200"
                                                        data-id="{{ $estancia->id }}"
                                                        data-msg="{{ $estancia->mensajeFinalizacionAdmin() }}">
                                                        Finalizar
                                                    </button>
                                                @endif

                                                @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                                    <form id="form-cancelar-estancia-{{ $estancia->id }}" method="POST"
                                                        action="{{ route('admin.estancias.cancelar', $estancia) }}" class="hidden">
                                                        @csrf
                                                        @method('PUT')
                                                    </form>

                                                    <button type="button"
                                                        class="btn-cancelar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeCancelacion() }}">
                                                        Cancelar
                                                    </button>
                                                @endif

                                                @if($estancia->esActiva())
                                                    <a href="{{ route('cuidados.show', $estancia) }}"
                                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200">
                                                        Cuidados
                                                    </a>
                                                @endif
                                            </div>

                                            <!-- enlaces de consulta -->
                                            @if($estancia->esActiva())
                                                <div class="flex flex-wrap gap-1.5 pt-2 border-t border-[#f0ede6]">

                                                    <a href="{{ route('estancias.historial', $estancia) }}"
                                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                                        Historial
                                                    </a>

                                                    <a href="{{ route('estancias.avisos', $estancia) }}"
                                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                                        Avisos
                                                    </a>

                                                    <a href="{{ route('estancias.factura', $estancia) }}"
                                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                        Factura
                                                    </a>

                                                </div>
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
                @foreach($estancias as $estancia)
                    @php
                        $estadoVisual = $estancia->getEstadoVisual();
                        $avisosAdmin = $estancia->getAvisosAdmin();
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                        <div class="p-5 flex-1">

                            <div class="flex items-start justify-between gap-3 mb-3">

                                <div class="min-w-0">
                                    <p class="font-medium text-[#1e2e1a] truncate">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5 truncate">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>

                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                        {{ $estadoVisual['texto'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Entrada
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Salida
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                            </div>

                            <div class="flex items-center justify-between gap-2">

                                <p class="text-sm font-medium text-[#1e2e1a]">
                                    {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                </p>

                                <div class="flex flex-wrap gap-1 justify-end">
                                    @forelse($avisosAdmin as $aviso)
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $aviso['clase'] }}">
                                            {{ $aviso['texto'] }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[#c0bdb8]">—</span>
                                    @endforelse
                                </div>

                            </div>

                            @if($estancia->pendienteIniciar())
                                <p class="text-xs text-[#7a4e10] mt-1.5">
                                    pendiente de iniciar
                                </p>
                            @endif

                            @if($estancia->pendienteFinalizar())
                                <p class="text-xs text-[#7a3a10] mt-1">
                                    pendiente de finalizar
                                </p>
                            @endif

                            @if($estancia->esSinDisponibilidad())
                                <p class="text-xs text-[#9b2a2a] mt-1">
                                    sin plazas disponibles
                                </p>
                            @endif

                        </div>

                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3">

                            <!-- acciones de gestion -->
                            <div class="flex flex-wrap gap-2">

                                @if($estancia->esPendiente())
                                    <form id="form-confirmar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.confirmar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-confirmar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeConfirmacionAdmin() }}">
                                        Confirmar
                                    </button>
                                @endif

                                @if($estancia->pendienteIniciar())
                                    <form id="form-iniciar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.iniciar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-iniciar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cef0] text-[#1a4f8a] hover:bg-[#e6f0fb] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="¿Seguro que quieres iniciar esta estancia?">
                                        Iniciar
                                    </button>
                                @endif

                                @if($estancia->esActiva())
                                    <form id="form-finalizar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.finalizar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-finalizar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:bg-[#f7f5f0] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeFinalizacionAdmin() }}">
                                        Finalizar
                                    </button>
                                @endif

                                @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                    <form id="form-cancelar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.cancelar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-cancelar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeCancelacion() }}">
                                        Cancelar
                                    </button>
                                @endif

                                @if($estancia->esActiva())
                                    <a href="{{ route('cuidados.show', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200">
                                        Cuidados
                                    </a>
                                @endif

                            </div>

                            <!-- enlaces de consulta -->
                            @if($estancia->esActiva())
                                <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t border-[#e8e5de]">

                                    <a href="{{ route('estancias.historial', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                        Historial
                                    </a>

                                    <a href="{{ route('estancias.avisos', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                        Avisos
                                    </a>

                                    <a href="{{ route('estancias.factura', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                        Factura
                                    </a>

                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- movil -->
            <div class="md:hidden space-y-3">
                @foreach($estancias as $estancia)
                    @php
                        $estadoVisual = $estancia->getEstadoVisual();
                        $avisosAdmin = $estancia->getAvisosAdmin();
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                        <div class="p-4">

                            <div class="flex items-start justify-between gap-3 mb-3">

                                <div>
                                    <p class="font-medium text-[#1e2e1a]">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>

                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                        {{ $estadoVisual['texto'] }}
                                    </span>
                                </div>

                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Entrada
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Salida
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                            </div>

                            <div class="flex items-center justify-between gap-2">

                                <p class="text-sm font-medium text-[#1e2e1a]">
                                    {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                </p>

                                <div class="flex flex-wrap gap-1 justify-end">
                                    @forelse($avisosAdmin as $aviso)
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $aviso['clase'] }}">
                                            {{ $aviso['texto'] }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-[#c0bdb8]">—</span>
                                    @endforelse
                                </div>

                            </div>

                            @if($estancia->pendienteIniciar())
                                <p class="text-xs text-[#7a4e10] mt-1.5">
                                    pendiente de iniciar
                                </p>
                            @endif

                            @if($estancia->pendienteFinalizar())
                                <p class="text-xs text-[#7a3a10] mt-1">
                                    pendiente de finalizar
                                </p>
                            @endif

                            @if($estancia->esSinDisponibilidad())
                                <p class="text-xs text-[#9b2a2a] mt-1">
                                    sin plazas disponibles
                                </p>
                            @endif

                        </div>

                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3">

                            <!-- acciones de gestion -->
                            <div class="flex flex-wrap gap-2">

                                @if($estancia->esPendiente())
                                    <form id="form-confirmar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.confirmar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-confirmar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeConfirmacionAdmin() }}">
                                        Confirmar
                                    </button>
                                @endif

                                @if($estancia->pendienteIniciar())
                                    <form id="form-iniciar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.iniciar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-iniciar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#b0cef0] text-[#1a4f8a] hover:bg-[#e6f0fb] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="¿Seguro que quieres iniciar esta estancia?">
                                        Iniciar
                                    </button>
                                @endif

                                @if($estancia->esActiva())
                                    <form id="form-finalizar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.finalizar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-finalizar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:bg-[#f7f5f0] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeFinalizacionAdmin() }}">
                                        Finalizar
                                    </button>
                                @endif

                                @if($estancia->esPendiente() || $estancia->esConfirmada() || $estancia->esActiva() || $estancia->esSinDisponibilidad())
                                    <form id="form-cancelar-estancia-{{ $estancia->id }}" method="POST"
                                        action="{{ route('admin.estancias.cancelar', $estancia) }}" class="hidden">
                                        @csrf
                                        @method('PUT')
                                    </form>

                                    <button type="button"
                                        class="btn-cancelar-estancia-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                        data-id="{{ $estancia->id }}" data-msg="{{ $estancia->mensajeCancelacion() }}">
                                        Cancelar
                                    </button>
                                @endif

                                @if($estancia->esActiva())
                                    <a href="{{ route('cuidados.show', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#b0cc9e] text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200">
                                        Cuidados
                                    </a>
                                @endif

                            </div>

                            <!-- enlaces de consulta -->
                            @if($estancia->esActiva())
                                <div class="flex flex-wrap gap-2 pt-2 mt-2 border-t border-[#e8e5de]">

                                    <a href="{{ route('estancias.historial', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                        Historial
                                    </a>

                                    <a href="{{ route('estancias.avisos', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                        Avisos
                                    </a>

                                    <a href="{{ route('estancias.factura', $estancia) }}"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#8a8e84] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                        Factura
                                    </a>

                                </div>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

        @else

            <!-- VISTA HISTORIAL -->

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
                                    Dueño
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
                                    Total
                                </th>

                                <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                    Consulta
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-[#f0ede6]">
                            @foreach($estancias as $estancia)
                                @php
                                    $estadoVisual = $estancia->getEstadoVisual();
                                @endphp

                                <tr class="hover:bg-[#fafaf8] transition-colors duration-150">

                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-[#1e2e1a]">
                                            {{ $estancia->mascota->nombre ?? '—' }}
                                        </p>
                                    </td>

                                    <td class="px-5 py-3.5 text-[#1e2e1a]">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
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
                                                por {{ $estancia->cancelada_por === 'admin' ? 'Admin' : 'Usuario' }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-5 py-3.5 font-medium text-[#1e2e1a] whitespace-nowrap">
                                        {{ number_format($estancia->precio_total ?? 0, 2) }} €
                                    </td>

                                    <td class="px-5 py-3.5">
                                        <div class="flex flex-wrap gap-1.5">

                                            <a href="{{ route('estancias.historial', $estancia) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                                Historial
                                            </a>

                                            <a href="{{ route('estancias.avisos', $estancia) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                                Avisos
                                            </a>

                                            @if($estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                                <a href="{{ route('estancias.factura', $estancia) }}"
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                    Factura
                                                </a>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

            <!-- tablet historial -->
            <div class="hidden md:grid xl:hidden grid-cols-2 gap-4">
                @foreach($estancias as $estancia)
                    @php
                        $estadoVisual = $estancia->getEstadoVisual();
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                        <div class="p-5 flex-1">

                            <div class="flex items-start justify-between gap-3 mb-3">

                                <div class="min-w-0">
                                    <p class="font-medium text-[#1e2e1a] truncate">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5 truncate">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>

                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                        {{ $estadoVisual['texto'] }}
                                    </span>
                                </div>

                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Entrada
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Salida
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                            </div>

                            <p class="text-sm font-medium text-[#1e2e1a]">
                                {{ number_format($estancia->precio_total ?? 0, 2) }} €
                            </p>

                            @if($estancia->esCancelada() && $estancia->cancelada_por)
                                <p class="text-xs text-[#8a8e84] mt-1">
                                    Cancelada por {{ $estancia->cancelada_por === 'admin' ? 'Administración' : 'Usuario' }}
                                </p>
                            @endif

                        </div>

                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex flex-wrap gap-2">

                            <a href="{{ route('estancias.historial', $estancia) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                Historial
                            </a>

                            <a href="{{ route('estancias.avisos', $estancia) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                Avisos
                            </a>

                            @if($estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                <a href="{{ route('estancias.factura', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Factura
                                </a>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- movil historial -->
            <div class="md:hidden space-y-3">
                @foreach($estancias as $estancia)
                    @php
                        $estadoVisual = $estancia->getEstadoVisual();
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                        <div class="p-4">

                            <div class="flex items-start justify-between gap-3 mb-3">

                                <div>
                                    <p class="font-medium text-[#1e2e1a]">
                                        {{ $estancia->mascota->nombre ?? '—' }}
                                    </p>

                                    <p class="text-xs text-[#8a8e84] mt-0.5">
                                        {{ $estancia->mascota->dueno->name ?? '—' }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>

                                    <span class="text-sm {{ $estadoVisual['etiqueta'] }}">
                                        {{ $estadoVisual['texto'] }}
                                    </span>
                                </div>

                            </div>

                            <div class="grid grid-cols-2 gap-2 mb-3">

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Entrada
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Salida
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </p>
                                </div>

                            </div>

                            <p class="text-sm font-medium text-[#1e2e1a]">
                                {{ number_format($estancia->precio_total ?? 0, 2) }} €
                            </p>

                            @if($estancia->esCancelada() && $estancia->cancelada_por)
                                <p class="text-xs text-[#8a8e84] mt-1">
                                    Cancelada por {{ $estancia->cancelada_por === 'admin' ? 'Administración' : 'Usuario' }}
                                </p>
                            @endif

                        </div>

                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex flex-wrap gap-2">

                            <a href="{{ route('estancias.historial', $estancia) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#3a7abf] hover:text-[#1a4f8a] transition-colors duration-200">
                                Historial
                            </a>

                            <a href="{{ route('estancias.avisos', $estancia) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                                Avisos
                            </a>

                            @if($estancia->esFinalizada() || ($estancia->esCancelada() && $estancia->precio_total > 0))
                                <a href="{{ route('estancias.factura', $estancia) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Factura
                                </a>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

        @endif

        <!-- paginacion -->
        @if($estancias->hasPages())
            <div class="mt-8">
                {{ $estancias->links() }}
            </div>
        @endif

    </div>
@endsection
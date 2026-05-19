@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="mb-6">
            <a href="{{ route('cuidados.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> Volver al panel
            </a>

            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                {{ $estancia->mascota->nombre ?? '—' }}
            </h2>

            <p class="text-sm text-[#8a8e84]">
                {{ $estancia->estado == 'activa' ? 'Estancia en curso' : 'Estancia confirmada pendiente de inicio' }}
            </p>
        </div>

        <!-- datos de la estancia -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 mb-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">

                <div class="bg-[#f7f5f0] rounded-xl p-3">
                    <p class="text-xs text-[#8a8e84] mb-1">Dueño</p>
                    <p class="text-sm font-medium text-[#1e2e1a]">{{ $estancia->mascota->dueno->name ?? '—' }}</p>
                </div>

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

                <div class="bg-[#f7f5f0] rounded-xl p-3">
                    <p class="text-xs text-[#8a8e84] mb-1">Estado</p>
                    <p class="text-sm font-medium text-[#1e2e1a]">
                        {{ ucfirst($estancia->estado) }}
                    </p>
                </div>

                @if(auth()->user()->role == 'admin')
                    <div class="bg-[#f7f5f0] rounded-xl p-3">
                        <p class="text-xs text-[#8a8e84] mb-1">Total sin extras</p>
                        <p class="text-sm font-medium text-[#1e2e1a]">
                            {{ number_format($estancia->precio_total ?? 0, 2) }} €
                        </p>
                    </div>

                    <div class="bg-[#f7f5f0] rounded-xl p-3">
                        <p class="text-xs text-[#8a8e84] mb-1">Total con extras</p>
                        <p class="text-sm font-medium text-[#1e2e1a]">
                            {{ number_format($estancia->totalConExtras(), 2) }} €
                        </p>
                    </div>
                @endif

            </div>
        </div>

        <!-- contador de tareas atrasadas -->
        @if($totalAtrasadas > 0)
            <div
                class="bg-[#fceaea] border border-[#e8b4b4] rounded-xl px-5 py-3.5 mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-sm font-medium text-[#9b2a2a]">
                    Hay {{ $totalAtrasadas }} {{ $totalAtrasadas == 1 ? 'tarea atrasada' : 'tareas atrasadas' }}
                    pendiente{{ $totalAtrasadas == 1 ? '' : 's' }}.
                </p>

                <a href="{{ route('cuidados.show', $estancia) }}?filtro=atrasadas"
                    class="text-xs px-3 py-1.5 rounded-lg bg-[#c9342e] text-white hover:bg-[#9b2a2a] transition-colors duration-200 text-center">
                    Ver atrasadas
                </a>
            </div>
        @else
            <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-xl px-5 py-3.5 mb-5">
                <p class="text-sm font-medium text-[#2d5a27]">
                    No hay tareas atrasadas.
                </p>
            </div>
        @endif

        <!-- accesos rapidos -->
        <div class="flex gap-2 mb-5 flex-wrap">
            <a href="#seccion-extras"
                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                Ir a extras
            </a>

            <a href="#seccion-avisos"
                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#c9821a] hover:text-[#7a4e10] transition-colors duration-200">
                Ir a avisos
            </a>
        </div>

        <!-- filtros -->
        <div class="flex gap-2 mb-6 flex-wrap">
            @foreach($filtrosVisuales as $clave => $filtroVisual)
                <a href="{{ route('cuidados.show', $estancia) }}?filtro={{ $clave }}"
                    class="text-xs px-4 py-1.5 rounded-lg border transition-colors duration-200 {{ $filtro == $clave ? $filtroVisual['activo'] : $filtroVisual['inactivo'] }}">
                    {{ $filtroVisual['texto'] }}
                </a>
            @endforeach
        </div>

        <!-- REALIZADOS -->
        @if($filtro === 'realizados')
            <div class="mb-2 flex items-center justify-between">
                <h3 class="font-serif text-xl font-medium text-[#1e2e1a]">
                    Realizados
                    <span class="text-[#8a8e84] text-base font-normal">({{ $totalRealizados }})</span>
                </h3>
            </div>

            <!-- si realizados por dia es mayor que 0 -->
            @if(count($realizadosPorDia) > 0)
                <div class="space-y-4">
                    @foreach($realizadosPorDia as $fecha => $items)
                        <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                            <div class="h-[3px] bg-[#5a9e47]"></div>

                            <div class="px-5 py-3 border-b border-[#f0ede6]">
                                <p class="text-xs font-medium text-[#2d5a27]">
                                    {{ date('d/m/Y', strtotime($fecha)) }} · {{ $items->count() }} cuidados
                                </p>
                            </div>

                            <ul class="divide-y divide-[#f0ede6]">
                                @foreach($items as $cuidado)
                                    <li class="px-5 py-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                        <div>
                                            <p class="text-sm font-medium text-[#1e2e1a]">
                                                {{ ucfirst($cuidado->tipo) }}
                                                @if($cuidado->horaCorta())
                                                    · {{ $cuidado->horaCorta() }}
                                                @endif
                                                @if($cuidado->tipo == 'extra' && $cuidado->precio_extra !== null)
                                                    · {{ number_format($cuidado->precio_extra, 2) }} €
                                                @endif
                                            </p>

                                            @if($cuidado->descripcion)
                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    {{ $cuidado->descripcion }}
                                                </p>
                                            @endif

                                            @if($cuidado->usuario)
                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    por {{ $cuidado->usuario->name }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="shrink-0 flex flex-row sm:flex-col items-start sm:items-end gap-2">
                                            <span
                                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e]">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#5a9e47]"></span>
                                                Realizado
                                            </span>

                                            @if(auth()->user()->role == 'admin' && $cuidado->tipo == 'extra')
                                                <form id="form-borrar-extra-{{ $cuidado->id }}" method="POST"
                                                    action="{{ route('cuidados.borrarExtra', $cuidado) }}" class="hidden">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                                <button type="button"
                                                    class="btn-borrar-extra-admin text-xs px-2.5 py-1 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                    data-id="{{ $cuidado->id }}"
                                                    data-msg="¿Seguro que quieres borrar este extra? Esta acción corregirá el registro y dejará de contarse en la estancia.">
                                                    Borrar extra
                                                </button>
                                            @endif
                                        </div>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>

                <!-- paginacion realizados -->
                @if($realizados && $realizados->hasPages())
                    <div class="mt-6">
                        {{ $realizados->links() }}
                    </div>
                @endif
            @else
                <p class="text-sm text-[#8a8e84]">
                    Todavía no hay cuidados realizados en esta estancia.
                </p>
            @endif
        @endif

        <!-- ATRASADAS -->
        @if($filtro == 'atrasadas')
            <div class="mb-4">
                <h3 class="font-serif text-xl font-medium text-[#1e2e1a]">
                    Atrasadas
                    <span class="text-[#8a8e84] text-base font-normal">
                        ({{ count($atrasadas) }})
                    </span>
                </h3>
            </div>

            <!-- si atrasadas es mayor que 0 -->
            @if(count($atrasadas) > 0)
                <div class="space-y-4">
                    @foreach($atrasadasPorDia as $fecha => $items)
                        <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                            <div class="h-[3px] bg-[#c9342e]"></div>

                            <div class="px-5 py-3 border-b border-[#f0ede6]">
                                <p class="text-xs font-medium text-[#9b2a2a]">
                                    {{ date('d/m/Y', strtotime($fecha)) }} · {{ $items->count() }} tareas
                                </p>
                            </div>

                            <ul class="divide-y divide-[#f0ede6]">
                                @foreach($items as $cuidado)
                                    <li class="px-5 py-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                        <div>
                                            <p class="text-sm font-medium text-[#1e2e1a]">
                                                {{ ucfirst($cuidado->tipo) }}
                                                @if($cuidado->horaCorta())
                                                    · {{ $cuidado->horaCorta() }}
                                                @endif
                                            </p>

                                            @if($cuidado->descripcion)
                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    {{ $cuidado->descripcion }}
                                                </p>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('cuidados.completar', $cuidado) }}" class="shrink-0">
                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                class="w-full sm:w-auto text-xs px-3 py-1.5 rounded-lg bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] transition-colors duration-200">
                                                Marcar hecho
                                            </button>
                                        </form>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-[#8a8e84]">
                    No hay tareas atrasadas.
                </p>
            @endif
        @endif

        <!-- HOY -->
        @if($filtro == 'hoy')
            <div class="mb-4">
                <h3 class="font-serif text-xl font-medium text-[#1e2e1a]">
                    Hoy
                    <span class="text-[#8a8e84] text-base font-normal">
                        ({{ date('d/m/Y', strtotime($hoy)) }} · {{ count($pendientesHoy) }} tareas)
                    </span>
                </h3>
            </div>

            <!-- si pendientes hoy es mayor que 0 -->
            @if(count($pendientesHoy) > 0)
                <div class="space-y-4">
                    @foreach($pendientesHoyPorDia as $fecha => $items)
                        <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                            <div class="h-[3px] bg-[#3a7abf]"></div>

                            <ul class="divide-y divide-[#f0ede6]">
                                @foreach($items as $cuidado)
                                    @php
                                        $noSePuedeAun = $cuidado->noSePuedeMarcar($hoy, $ahoraMas15);
                                        $esAtrasadaHoy = $cuidado->esAtrasadoHoy($hoy, $ahoraHora);
                                    @endphp

                                    <li class="px-5 py-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                        <div>
                                            <p class="text-sm font-medium text-[#1e2e1a]">
                                                {{ ucfirst($cuidado->tipo) }}
                                                @if($cuidado->horaCorta())
                                                    · {{ $cuidado->horaCorta() }}
                                                @endif
                                            </p>

                                            @if($cuidado->descripcion)
                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    {{ $cuidado->descripcion }}
                                                </p>
                                            @endif

                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                @if($esAtrasadaHoy)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#c9342e]"></span>
                                                        Atrasada
                                                    </span>
                                                @endif
                                            </div>

                                            @if($noSePuedeAun && $cuidado->disponibleDesde())
                                                <p class="text-xs text-[#8a8e84] mt-1">
                                                    Disponible a las {{ $cuidado->disponibleDesde() }}
                                                </p>
                                            @endif
                                        </div>

                                        <form method="POST" action="{{ route('cuidados.completar', $cuidado) }}" class="shrink-0">
                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                class="w-full sm:w-auto text-xs px-3 py-1.5 rounded-lg transition-colors duration-200 {{ !$noSePuedeAun ? 'bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6]' : 'bg-[#eae8e3] text-[#a8a49f] cursor-not-allowed' }}"
                                                {{ $noSePuedeAun ? 'disabled' : '' }}>
                                                Marcar hecho
                                            </button>
                                        </form>

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-[#8a8e84]">
                    No hay tareas pendientes para hoy.
                </p>
            @endif
        @endif

        <!-- EXTRAS + AVISOS -->
        <div class="mt-10 grid grid-cols-1 xl:grid-cols-2 gap-6">

            <!-- columna extras -->
            <div id="seccion-extras">

                <!-- titulo -->
                <h3 class="font-serif text-xl font-medium text-[#1e2e1a] mb-4">
                    Extras de la estancia
                </h3>

                <!-- formulario extras -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-6 mb-5">
                    <form method="POST" action="{{ route('cuidados.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="estancia_id" value="{{ $estancia->id }}">
                        <input type="hidden" name="tipo" value="extra">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                    Hora
                                </label>

                                <input type="time" name="hora"
                                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                    required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                    Precio (€)
                                </label>

                                <input type="number" step="0.01" name="precio_extra" min="0"
                                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                    required>
                            </div>

                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Descripción
                            </label>

                            <input type="text" name="descripcion"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                placeholder="Ej: baño, sesión de adiestramiento..." required>
                        </div>

                        <p class="text-xs text-[#8a8e84]">
                            El extra se registra directamente como realizado y se añadirá al total de la estancia.
                        </p>

                        <button type="submit"
                            class="w-full sm:w-auto text-sm px-5 py-2.5 rounded-xl bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] font-medium transition-colors duration-200">
                            Añadir extra
                        </button>
                    </form>
                </div>

                <!-- listado extras -->
                <div>
                    @if($extras->count() > 0)
                        <div class="space-y-3">
                            @foreach($extras as $extra)
                                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                                    <div class="h-[3px] bg-[#5a9e47]"></div>

                                    <div class="p-4">

                                        <div class="flex items-start justify-between gap-3 mb-2">

                                            <div>
                                                <p class="text-sm font-medium text-[#1e2e1a]">
                                                    {{ $extra->descripcion }}
                                                </p>

                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    {{ $extra->fecha ? date('d/m/Y', strtotime($extra->fecha)) : '' }}
                                                    @if($extra->horaCorta())
                                                        · {{ $extra->horaCorta() }}
                                                    @endif
                                                </p>
                                            </div>

                                            <span
                                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e] shrink-0">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#5a9e47]"></span>
                                                {{ number_format($extra->precio_extra ?? 0, 2) }} €
                                            </span>

                                        </div>

                                        @if($extra->usuario)
                                            <p class="text-xs text-[#8a8e84]">
                                                Registrado por {{ $extra->usuario->name }}
                                            </p>
                                        @endif

                                        @if(auth()->user()->role == 'admin')
                                            <form id="form-borrar-extra-{{ $extra->id }}" method="POST"
                                                action="{{ route('cuidados.borrarExtra', $extra) }}" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button type="button"
                                                class="btn-borrar-extra-admin mt-3 text-xs px-2.5 py-1 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                data-id="{{ $extra->id }}"
                                                data-msg="¿Seguro que quieres borrar este extra? Esta acción corregirá el registro y dejará de contarse en la estancia.">
                                                Borrar extra
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- paginacion extras -->
                        @if($extras->hasPages())
                            <div class="mt-5">
                                {{ $extras->links() }}
                            </div>
                        @endif

                    @else
                        <p class="text-sm text-[#8a8e84]">
                            Todavía no hay extras registrados en esta estancia.
                        </p>
                    @endif
                </div>
            </div>

            <!-- columna avisos -->
            <div id="seccion-avisos">

                <!-- titulo -->
                <h3 class="font-serif text-xl font-medium text-[#1e2e1a] mb-4">
                    Avisos de la estancia
                </h3>

                <!-- formulario avisos -->
                <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-6 mb-5">
                    <form method="POST" action="{{ route('avisos.store') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="estancia_id" value="{{ $estancia->id }}">

                        <div>
                            <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Tipo
                            </label>

                            <select name="tipo"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                required>
                                <option value="info">Info</option>
                                <option value="importante">Importante</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Mensaje
                            </label>

                            <textarea name="mensaje" rows="3" maxlength="1000"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200 resize-none"
                                placeholder="Ej: hoy ha comido poco, diarrea, medicación dada..." required></textarea>
                        </div>

                        <div class="text-xs text-[#8a8e84] space-y-1">
                            <p>
                                Si marcas el aviso como <strong>Importante</strong>, también se enviará al correo del dueño.
                            </p>

                            <p>
                                Si marcas el aviso como <strong>Info</strong>, solo se mostrará en su sección de avisos.
                            </p>
                        </div>

                        <button type="submit"
                            class="w-full sm:w-auto text-sm px-5 py-2.5 rounded-xl bg-[#b87a1a] hover:bg-[#7a4e10] text-white font-medium transition-colors duration-200">
                            Enviar aviso
                        </button>
                    </form>
                </div>

                <!-- listado avisos -->
                <div>
                    @if(isset($avisos) && $avisos->count() > 0)
                        <div class="space-y-3">
                            @foreach($avisos as $aviso)
                                @php
                                    $avisoVisual = $aviso->getAvisoVisual();
                                @endphp

                                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                                    <div class="h-[3px] {{ $avisoVisual['barra'] }}"></div>

                                    <div class="p-4">

                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">

                                            <div class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $avisoVisual['punto'] }}"></span>

                                                <span class="text-xs font-medium {{ $avisoVisual['etiqueta'] }}">
                                                    {{ $avisoVisual['texto'] }}
                                                </span>
                                            </div>

                                            <span class="text-xs text-[#8a8e84]">
                                                {{ $aviso->created_at ? $aviso->created_at->format('d/m/Y H:i') : '' }}
                                            </span>

                                        </div>

                                        <p class="text-sm text-[#1e2e1a] leading-relaxed">
                                            {{ $aviso->mensaje }}
                                        </p>

                                        <p class="text-xs text-[#8a8e84] mt-2">
                                            Creado por: {{ $aviso->usuario ? ucfirst($aviso->usuario->role) : '—' }}
                                        </p>

                                        @if(auth()->user()->role == 'admin')
                                            <form id="form-borrar-aviso-{{ $aviso->id }}" method="POST"
                                                action="{{ route('avisos.borrarAviso', $aviso) }}" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            <button type="button"
                                                class="btn-borrar-aviso-admin mt-3 text-xs px-2.5 py-1 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                data-id="{{ $aviso->id }}"
                                                data-msg="¿Seguro que quieres borrar este aviso? Esta acción no se puede deshacer.">
                                                Borrar aviso
                                            </button>
                                        @endif

                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- paginacion avisos -->
                        @if($avisos->hasPages())
                            <div class="mt-5">
                                {{ $avisos->links() }}
                            </div>
                        @endif

                    @else
                        <p class="text-sm text-[#8a8e84]">
                            Todavía no hay avisos en esta estancia.
                        </p>
                    @endif
                </div>
            </div>

        </div>

    </div>
@endsection
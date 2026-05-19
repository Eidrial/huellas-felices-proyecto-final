@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-5 sm:px-7 py-7 sm:py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                {{ auth()->user()->role == 'admin' ? 'Administración' : 'Cuidados' }}
            </p>
            <h2 class="font-serif text-3xl font-medium text-[#f0ede6]">Panel de cuidados</h2>
        </div>

        @if($estancias->isEmpty())
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-8 sm:p-12 text-center">
                <div
                    class="w-16 h-16 mx-auto mb-4 bg-[#eef5e8] border border-[#c8d9be] rounded-2xl flex items-center justify-center text-3xl">
                    🐾
                </div>
                <p class="text-[#8a8e84] text-sm">No hay estancias confirmadas o activas.</p>
            </div>
        @else

            <!-- avisos de manana -->
            @if($totalEntradasManana > 0 || $totalSalidasManana > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-5">
                    @if($totalEntradasManana > 0)
                        <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-xl px-5 py-3.5">
                            <p class="text-sm font-medium text-[#7a4e10] mb-1.5">
                                Mañana
                                {{ $totalEntradasManana == 1 ? 'entra ' . $totalEntradasManana . ' perro' : 'entran ' . $totalEntradasManana . ' perros' }}
                            </p>
                            <ul class="space-y-0.5">
                                @foreach($entradasManana as $e)
                                    <li class="text-xs text-[#7a4e10]">
                                        · {{ $e->mascota->nombre ?? '—' }}
                                        <span class="text-[#a07040]">(Dueño: {{ $e->mascota->dueno->name ?? '—' }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($totalSalidasManana > 0)
                        <div class="bg-[#e6f0fb] border border-[#b0cef0] rounded-xl px-5 py-3.5">
                            <p class="text-sm font-medium text-[#1a4f8a] mb-1.5">
                                Mañana
                                {{ $totalSalidasManana == 1 ? 'sale ' . $totalSalidasManana . ' perro' : 'salen ' . $totalSalidasManana . ' perros' }}
                            </p>
                            <ul class="space-y-0.5">
                                @foreach($salidasManana as $e)
                                    <li class="text-xs text-[#1a4f8a]">
                                        · {{ $e->mascota->nombre ?? '—' }}
                                        <span class="text-[#4070a0]">(Dueño: {{ $e->mascota->dueno->name ?? '—' }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <!-- lista de estancias -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($estancias as $estancia)
                    @php
                        //resumen de los cuidados de esta estancia
                        $resumenCuidados = $estancia->getResumenCuidados($resumen);
                        $r = $resumenCuidados['data'];
                        $proxima = $r['proxima'];
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">
                        <div class="h-[3px] {{ $resumenCuidados['barra'] }}"></div>

                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex-1">

                                <!-- nombre + dueño -->
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="font-medium text-[#1e2e1a] text-base leading-snug">
                                            {{ $estancia->mascota->nombre ?? '—' }}
                                        </p>
                                        <p class="text-xs text-[#8a8e84] mt-0.5">
                                            {{ $estancia->mascota->dueno->name ?? '—' }}
                                        </p>
                                    </div>

                                    <!-- estado -->
                                    <span class="shrink-0 text-xs px-2.5 py-1 rounded-full border
                                        {{ $estancia->estado == 'activa'
                                            ? 'bg-[#eef5e8] text-[#2d5a27] border-[#b0cc9e]'
                                            : 'bg-[#f7f5f0] text-[#8a8e84] border-[#d9ddd0]' }}">
                                        {{ ucfirst($estancia->estado) }}
                                    </span>
                                </div>

                                <!-- fechas -->
                                <div class="flex items-center gap-1.5 mt-2.5">
                                    <span class="text-xs text-[#8a8e84]">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </span>
                                    <span class="text-xs text-[#c8ccbf]">→</span>
                                    <span class="text-xs text-[#8a8e84]">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </span>
                                </div>

                                <!-- resumen de tareas -->
                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    @if($r['pendientesAtrasadas'] > 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#c9342e]"></span>
                                            Atrasadas: {{ $r['pendientesAtrasadas'] }}
                                        </span>
                                    @endif

                                    @if($r['pendientesHoy'] > 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#e6f0fb] text-[#1a4f8a] border border-[#b0cef0]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#3a7abf]"></span>
                                            Hoy: {{ $r['pendientesHoy'] }}
                                        </span>
                                    @endif

                                    @if($r['pendientesProximas'] > 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#f7f5f0] text-[#8a8e84] border border-[#d9ddd0]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#8a8e84]"></span>
                                            Próximas: {{ $r['pendientesProximas'] }}
                                        </span>
                                    @endif

                                    @if($r['extrasHoy'] > 0)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#5a9e47]"></span>
                                            Extras hoy: {{ $r['extrasHoy'] }}
                                        </span>
                                    @endif

                                    @if($resumenCuidados['sinTareas'])
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e]">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#5a9e47]"></span>
                                            Sin tareas pendientes
                                        </span>
                                    @endif
                                </div>

                                <!-- siguiente tarea -->
                                @if($proxima)
                                    <div class="mt-3 bg-[#f7f5f0] rounded-xl px-3 py-2.5">
                                        <p class="text-xs text-[#8a8e84] leading-relaxed">
                                            <span class="text-[#5a6e54] font-medium">Siguiente · </span>
                                            <span class="text-[#1e2e1a]">{{ date('d/m/Y', strtotime($proxima->fecha)) }}</span>
                                            @if($proxima->hora)
                                                <span class="text-[#1e2e1a]"> {{ substr($proxima->hora, 0, 5) }}</span>
                                            @endif
                                            · {{ ucfirst($proxima->tipo) }}
                                            @if(!empty($proxima->descripcion))
                                                — {{ $proxima->descripcion }}
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- acciones -->
                            <div class="mt-5 pt-4 border-t border-[#f0ede6] flex items-center justify-between gap-3 flex-wrap">
                                <a href="{{ route('cuidados.show', $estancia) }}"
                                    class="text-xs px-4 py-2 rounded-lg bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] font-medium transition-colors duration-200">
                                    Gestionar
                                </a>

                                @if($r['pendientesAtrasadas'] > 0)
                                    <a href="{{ route('cuidados.show', $estancia) }}?filtro=atrasadas"
                                        class="text-xs text-[#9b2a2a] hover:underline">
                                        Ver atrasadas
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        @endif
    </div>
@endsection
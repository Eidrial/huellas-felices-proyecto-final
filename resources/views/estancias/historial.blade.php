@extends('layouts.app')

@section('content')
    @php
        $usuario = auth()->user();

        $volverRuta = $usuario->role == 'admin'
            ? route('admin.estancias.index')
            : ($usuario->role == 'cuidador'
                ? route('cuidador.estancias')
                : route('estancias.index'));

        $volverTexto = $usuario->role == 'usuario'
            ? 'Volver a mis estancias'
            : 'Volver a estancias';
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">

            <!-- cabecera -->
            <div class="mb-7">

                <a href="{{ $volverRuta }}"
                   class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                    <span>←</span>
                    {{ $volverTexto }}
                </a>

                <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                    Historial de cuidados
                </h2>

                <p class="text-sm text-[#8a8e84]">
                    {{ $estancia->mascota->nombre ?? '—' }}
                    · {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                    —
                    {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                </p>

            </div>

            <!-- PENDIENTES HOY -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden mb-4">
                <div class="h-[3px] bg-[#3a7abf]"></div>

                <details open>
                    <summary class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 cursor-pointer select-none list-none">

                        <span class="text-sm font-medium text-[#1e2e1a]">
                            Pendientes de hoy
                            <span class="text-[#8a8e84] font-normal">
                                ({{ date('d/m/Y', strtotime($hoy)) }})
                            </span>
                        </span>

                        <span class="shrink-0 inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#e6f0fb] text-[#1a4f8a] border border-[#b0cef0]">
                            {{ $totalPendientesHoy }}
                        </span>

                    </summary>

                    <div class="border-t border-[#f0ede6] px-4 sm:px-5 py-4">

                        @if ($totalPendientesHoy == 0)
                            <p class="text-sm text-[#8a8e84]">
                                No hay cuidados pendientes para hoy.
                            </p>
                        @else
                            <div class="space-y-3">

                                @foreach ($pendientesHoy as $fecha => $cuidados)
                                    <div class="space-y-2">

                                        <p class="text-xs font-medium text-[#1a4f8a]">
                                            {{ date('d/m/Y', strtotime($fecha)) }}
                                        </p>

                                        <ul class="space-y-2">

                                            @foreach ($cuidados as $cuidado)
                                                <li class="bg-[#f7f5f0] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                                    <div class="min-w-0">

                                                        <p class="text-sm font-medium text-[#1e2e1a] break-words">
                                                            {{ ucfirst($cuidado->tipo) }}

                                                            @if ($cuidado->hora)
                                                                · {{ substr($cuidado->hora, 0, 5) }}
                                                            @endif

                                                            @if ($cuidado->tipo == 'extra' && $cuidado->precio_extra !== null)
                                                                · {{ number_format($cuidado->precio_extra, 2) }} €
                                                            @endif
                                                        </p>

                                                        @if ($cuidado->descripcion)
                                                            <p class="text-xs text-[#8a8e84] mt-0.5 break-words">
                                                                {{ $cuidado->descripcion }}
                                                            </p>
                                                        @endif

                                                    </div>

                                                    <span class="shrink-0 w-fit inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#e6f0fb] text-[#1a4f8a] border border-[#b0cef0]">
                                                        Pendiente
                                                    </span>

                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>
                                @endforeach

                            </div>
                        @endif

                    </div>
                </details>
            </div>

            <!-- ATRASADAS -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden mb-4">
                <div class="h-[3px] bg-[#c9342e]"></div>

                <details>
                    <summary class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 cursor-pointer select-none list-none">

                        <span class="text-sm font-medium text-[#1e2e1a]">
                            Atrasadas
                        </span>

                        <span class="shrink-0 inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]">
                            {{ $totalAtrasadas }}
                        </span>

                    </summary>

                    <div class="border-t border-[#f0ede6] px-4 sm:px-5 py-4">

                        @if ($totalAtrasadas == 0)
                            <p class="text-sm text-[#8a8e84]">
                                No hay cuidados atrasados.
                            </p>
                        @else

                            <div class="space-y-3">

                                @foreach ($atrasadas as $fecha => $cuidados)
                                    <div class="space-y-2">

                                        <p class="text-xs font-medium text-[#9b2a2a]">
                                            {{ date('d/m/Y', strtotime($fecha)) }}
                                        </p>

                                        <ul class="space-y-2">

                                            @foreach ($cuidados as $cuidado)
                                                <li class="bg-[#f7f5f0] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                                    <div class="min-w-0">

                                                        <p class="text-sm font-medium text-[#1e2e1a] break-words">
                                                            {{ ucfirst($cuidado->tipo) }}

                                                            @if ($cuidado->hora)
                                                                · {{ substr($cuidado->hora, 0, 5) }}
                                                            @endif

                                                            @if ($cuidado->tipo == 'extra' && $cuidado->precio_extra !== null)
                                                                · {{ number_format($cuidado->precio_extra, 2) }} €
                                                            @endif
                                                        </p>

                                                        @if ($cuidado->descripcion)
                                                            <p class="text-xs text-[#8a8e84] mt-0.5 break-words">
                                                                {{ $cuidado->descripcion }}
                                                            </p>
                                                        @endif

                                                    </div>

                                                    <span class="shrink-0 w-fit inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4]">
                                                        Atrasado
                                                    </span>

                                                </li>
                                            @endforeach

                                        </ul>
                                    </div>
                                @endforeach

                            </div>
                        @endif

                    </div>
                </details>
            </div>

            <!-- REALIZADOS -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden mb-6">
                <div class="h-[3px] bg-[#5a9e47]"></div>

                <details>
                    <summary class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4 cursor-pointer select-none list-none">

                        <span class="text-sm font-medium text-[#1e2e1a]">
                            Realizados
                        </span>

                        <span class="shrink-0 inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e]">
                            {{ $totalRealizados }}
                        </span>

                    </summary>

                    <div class="border-t border-[#f0ede6] px-4 sm:px-5 py-4">

                        @if ($realizados->count() == 0)
                            <p class="text-sm text-[#8a8e84]">
                                Todavía no hay cuidados marcados como realizados.
                            </p>
                        @else

                            <ul class="space-y-2">

                                @foreach ($realizados as $cuidado)
                                    <li class="bg-[#f7f5f0] rounded-xl px-4 py-3 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                        <div class="min-w-0">

                                            <p class="text-xs text-[#8a8e84] mb-1">
                                                {{ date('d/m/Y', strtotime($cuidado->fecha)) }}
                                            </p>

                                            <p class="text-sm font-medium text-[#1e2e1a] break-words">
                                                {{ ucfirst($cuidado->tipo) }}

                                                @if ($cuidado->hora)
                                                    · {{ substr($cuidado->hora, 0, 5) }}
                                                @endif

                                                @if ($cuidado->tipo == 'extra' && $cuidado->precio_extra !== null)
                                                    · {{ number_format($cuidado->precio_extra, 2) }} €
                                                @endif
                                            </p>

                                            @if ($cuidado->descripcion)
                                                <p class="text-xs text-[#8a8e84] mt-0.5 break-words">
                                                    {{ $cuidado->descripcion }}
                                                </p>
                                            @endif

                                            @if ($cuidado->usuario)
                                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                                    por {{ ucfirst($cuidado->usuario->role) }}
                                                </p>
                                            @endif

                                        </div>

                                        <span class="shrink-0 w-fit inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e]">
                                            Realizado
                                        </span>

                                    </li>
                                @endforeach

                            </ul>

                            <div class="mt-4">
                                {{ $realizados->links() }}
                            </div>

                        @endif

                    </div>
                </details>
            </div>

        </div>
    </div>

@endsection
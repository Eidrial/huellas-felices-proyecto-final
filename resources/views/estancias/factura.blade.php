@extends('layouts.app')

@section('content')

    @php
        $volverRuta = auth()->user()->role == 'admin' ? route('admin.estancias.index') : (auth()->user()->role == 'cuidador' ? route('cuidador.estancias') : route('estancias.index'));

        $volverTexto = auth()->user()->role == 'usuario' ? 'Volver a mis estancias' : 'Volver a estancias';
    @endphp

    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="max-w-2xl mx-auto">

            <!-- cabecera -->
            <div class="mb-7">
                <a href="{{ $volverRuta }}"
                    class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                    <span>←</span> {{ $volverTexto }}
                </a>
                <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">Resumen de estancia</h2>
                <p class="text-sm text-[#8a8e84]">{{ $estancia->mascota->nombre }}</p>
            </div>

            <!-- datos generales -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7 mb-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-[#f7f5f0] rounded-xl p-3">
                        <p class="text-xs text-[#8a8e84] mb-1">Entrada</p>
                        <p class="text-sm font-medium text-[#1e2e1a]">{{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
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
                        <p class="text-sm font-medium text-[#1e2e1a]">{{ ucfirst($estancia->estado) }}</p>
                    </div>
                    @if($estancia->estado == 'cancelada' && $estancia->cancelada_por)
                        <div class="bg-[#f7f5f0] rounded-xl p-3 sm:col-span-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Cancelada por</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">
                                {{ $estancia->cancelada_por == 'admin' ? 'Administración' : 'Usuario' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- aviso cancelacion mismo dia -->
            @if($estancia->esCancelacionUnDia())
                <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-xl px-4 sm:px-5 py-3.5 mb-5">
                    <p class="text-sm text-[#7a4e10]">
                        <span class="font-medium">Nota:</span> Esta estancia se cancelo el mismo dia de entrada, por lo que se cobra
                        1 dia de estancia.
                    </p>
                </div>
            @endif

            <!-- factura -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden mb-6">
                <div class="h-[3px] bg-[#5a9e47]"></div>
                <div class="px-5 sm:px-6 py-4 border-b border-[#e8e5de]">
                    <p class="text-xs uppercase tracking-[0.15em] text-[#8a8e84] font-medium">Factura</p>
                </div>

                <!-- lineas de factura -->
                <div class="divide-y divide-[#f0ede6]">

                    <!-- dias de estancia -->
                    <div class="px-5 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1.5">
                        <p class="text-sm text-[#1e2e1a]">
                            {{ $estancia->diasFacturados() }} dias × {{ number_format($estancia->precio_dia, 2) }} €/dia
                        </p>
                        <p class="text-sm font-medium text-[#1e2e1a] whitespace-nowrap">{{ number_format($estancia->precio_total, 2) }} €</p>
                    </div>

                    <!-- extras -->
                    @if($estancia->cuidados->where('tipo', 'extra')->count() > 0)
                        @foreach($estancia->cuidados->where('tipo', 'extra') as $extra)
                            <div class="px-5 sm:px-6 py-3.5 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1.5">
                                <p class="text-sm text-[#1e2e1a] break-words">
                                    Extra: {{ $extra->descripcion }}
                                </p>
                                <p class="text-sm font-medium text-[#1e2e1a] whitespace-nowrap">{{ number_format($extra->precio_extra, 2) }} €</p>
                            </div>
                        @endforeach
                    @else
                        <div class="px-5 sm:px-6 py-3.5">
                            <p class="text-sm text-[#8a8e84]">Sin cuidados extras registrados.</p>
                        </div>
                    @endif

                </div>

                <!-- total -->
                <div class="px-5 sm:px-6 py-4 bg-[#f7f5f0] border-t border-[#e8e5de] flex justify-between items-center gap-3">
                    <p class="font-medium text-[#1e2e1a] uppercase tracking-wider text-xs">Total</p>
                    <p class="text-xl font-medium text-[#1e2e1a] whitespace-nowrap">{{ number_format($estancia->totalConExtras(), 2) }} €</p>
                </div>
            </div>

        </div>

    </div>
@endsection
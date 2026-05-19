@extends('layouts.app')

@section('content')

    @php
        $volverRuta = auth()->user()->role == 'admin' ? route('admin.estancias.index') : (auth()->user()->role == 'cuidador' ? route('cuidador.estancias') : route('estancias.index'));

        $volverTexto = auth()->user()->role == 'usuario' ? 'Volver a mis estancias' : 'Volver a estancias';
    @endphp

    <div class="max-w-3xl mx-auto px-4 py-8 md:py-10">

        <!-- cabecera -->
        <div class="mb-7">
            <a href="{{ $volverRuta }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> {{ $volverTexto }}
            </a>
            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">Avisos de la estancia</h2>
            <p class="text-sm text-[#8a8e84]">
                {{ $estancia->mascota->nombre ?? '—' }} ·
                {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }} —
                {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
            </p>
        </div>

        <!-- si no hay avisos -->
        @if($avisos->count() == 0)
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-10 sm:p-12 text-center">
                <p class="text-sm text-[#8a8e84]">No hay avisos registrados para esta estancia.</p>
            </div>
        <!-- si hay avisos-->
        @else
            <div class="space-y-3">
                @foreach($avisos as $aviso)
                
                    @php 
                    $esImportante = $aviso->tipo === 'importante'; 
                    @endphp

                    <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                        <!-- rojo si es importante, azul si no lo es -->
                        <div class="h-[3px] {{ $esImportante ? 'bg-[#c9342e]' : 'bg-[#3a7abf]' }}"></div>
                        <div class="p-4 sm:p-5">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                <div class="flex items-center gap-1.5">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full {{ $esImportante ? 'bg-[#c9342e]' : 'bg-[#3a7abf]' }}"></span>
                                    <span class="text-xs font-medium {{ $esImportante ? 'text-[#9b2a2a]' : 'text-[#1a4f8a]' }}">
                                        {{ $esImportante ? 'Importante' : 'Info' }}
                                    </span>
                                </div>
                                <span class="text-xs text-[#8a8e84]">
                                    {{ $aviso->created_at ? $aviso->created_at->format('d/m/Y H:i') : '' }}
                                </span>
                            </div>
                            <p class="text-sm text-[#1e2e1a] leading-relaxed break-words">{{ $aviso->mensaje }}</p>
                            <p class="text-xs text-[#8a8e84] mt-2">
                                Creado por: {{ $aviso->usuario ? ucfirst($aviso->usuario->role) : '—' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="mb-6">
            <a href="{{ route('cuidador.mascotas') }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> Volver a mascotas
            </a>

            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                {{ $mascota->nombre }}
            </h2>

            <p class="text-sm text-[#8a8e84]">
                Ficha de la mascota
            </p>
        </div>

        <!-- ficha -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

            <div class="p-5 sm:p-6 flex flex-col md:flex-row gap-5">
                <div class="flex md:block justify-center shrink-0">
                    @if($mascota->foto)
                        <img src="{{ asset('storage/' . $mascota->foto) }}" alt="{{ $mascota->nombre }}"
                            class="w-32 h-32 md:w-28 md:h-28 object-cover rounded-2xl border border-[#d9ddd0]">
                    @else
                        <div
                            class="w-32 h-32 md:w-28 md:h-28 bg-[#eef5e8] border border-[#c8d9be] rounded-2xl flex items-center justify-center text-4xl">
                            🐾
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-xs uppercase tracking-[0.2em] text-[#8a8e84] mb-2 text-center md:text-left">Datos generales</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Nombre</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ $mascota->nombre }}</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Dueño</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ $mascota->dueno->name ?? '—' }}</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Especie</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ ucfirst($mascota->especie) }}</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Raza</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ $mascota->raza }}</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Edad</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ $mascota->edad }} años</p>
                        </div>

                        <div class="bg-[#f7f5f0] rounded-xl p-3">
                            <p class="text-xs text-[#8a8e84] mb-1">Peso</p>
                            <p class="text-sm font-medium text-[#1e2e1a]">{{ $mascota->peso }} kg</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
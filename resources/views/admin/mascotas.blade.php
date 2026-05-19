@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>
            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                {{ auth()->user()->role == 'admin' ? 'Administración' : 'Panel cuidador' }}
            </p>
            <h2 class="font-serif text-3xl font-medium text-[#f0ede6]">
                {{ auth()->user()->role == 'admin' ? 'Gestión de mascotas' : 'Mascotas' }}
            </h2>
        </div>

        <!-- vista escritorio (tabla) -->
        <div class="hidden lg:block bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#e8e5de]">
                            <th
                                class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider w-14">
                            </th>
                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Mascota</th>
                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Dueño</th>
                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Estado</th>
                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#f0ede6]">
                        @foreach($mascotas as $mascota)
                            @php
                                //estado visual reutilizable desde el modelo
                                $estadoVisual = $mascota->getEstadoVisual();

                                $tieneEstancia = $mascota->estancias
                                    ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
                                    ->count() > 0;
                            @endphp

                            <tr class="hover:bg-[#fafaf8] transition-colors duration-150">

                                <!-- foto de la mascota-->
                                <td class="px-5 py-3.5">
                                    @if($mascota->foto)
                                        <img src="{{ asset('storage/' . $mascota->foto) }}" alt="{{ $mascota->nombre }}"
                                            class="w-10 h-10 object-cover rounded-xl border border-[#d9ddd0]">
                                    @else
                                        <div
                                            class="w-10 h-10 bg-[#eef5e8] border border-[#c8d9be] rounded-xl flex items-center justify-center text-base">
                                            🐾
                                        </div>
                                    @endif
                                </td>

                                <!-- nombre + especie/raza -->
                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-[#1e2e1a]">{{ $mascota->nombre }}</p>
                                    <p class="text-xs text-[#8a8e84] mt-0.5">
                                        {{ ucfirst($mascota->especie) }} · {{ $mascota->raza }}
                                    </p>
                                </td>

                                <!-- dueño -->
                                <td class="px-5 py-3.5 text-[#1e2e1a]">
                                    {{ $mascota->dueno->name ?? '—' }}
                                </td>

                                <!-- estado de la mascota (pendiente, aprobada, no aprobada) -->
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 mb-0.5">

                                        <!-- punto del estado -->
                                        <span id="punto-{{ $mascota->id }}"
                                            class="w-1.5 h-1.5 rounded-full shrink-0 {{ $estadoVisual['punto'] }}"></span>

                                        <!-- texto del estado -->
                                        <span id="estado-{{ $mascota->id }}"
                                            class="text-sm {{ $estadoVisual['etiqueta'] ?? '' }}">
                                            {{ $estadoVisual['texto'] }}
                                        </span>
                                    </div>

                                    @if(auth()->user()->role == 'admin' && $mascota->aprobado === null)
                                        <div class="flex gap-1.5 mt-2">

                                            <!-- aprobar mascota -->
                                            <button type="button" data-id="{{ $mascota->id }}" data-valor="1"
                                                data-nombre="{{ $mascota->nombre }}"
                                                class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e] hover:bg-[#ddf0d0] transition-colors duration-200">
                                                Aprobar
                                            </button>

                                            <!-- no aprobar -->
                                            <button type="button" data-id="{{ $mascota->id }}" data-valor="0"
                                                data-nombre="{{ $mascota->nombre }}"
                                                class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] hover:bg-[#f5d0d0] transition-colors duration-200">
                                                No aprobar
                                            </button>

                                        </div>
                                    @endif
                                </td>

                                <!-- acciones posibles -->
                                <td class="px-5 py-3.5">
                                    <div class="flex gap-1.5 flex-wrap">

                                        @if(auth()->user()->role == 'admin')
                                            <!-- editar -->
                                            <a href="{{ route('admin.mascotas.editar', $mascota) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                Editar
                                            </a>

                                            <!-- eliminar -->
                                            @if($tieneEstancia)
                                                <button disabled
                                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#e8e5de] text-[#c0bdb8] cursor-not-allowed">
                                                    Borrar
                                                </button>
                                            @else
                                                <button type="button"
                                                    class="btn-eliminar-mascota-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                    data-id="{{ $mascota->id }}" data-nombre="{{ $mascota->nombre }}">
                                                    Borrar
                                                </button>
                                            @endif
                                      @else
                                            <a href="{{ route('cuidador.mascotas.show', $mascota) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                Ver ficha
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

        <!-- vista tablet -->
        <div class="hidden md:grid lg:hidden grid-cols-2 gap-4">
            @foreach($mascotas as $mascota)
                @php
                    //estado visual reutilizable desde el modelo
                    $estadoVisual = $mascota->getEstadoVisual();

                    $tieneEstancia = $mascota->estancias
                        ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
                        ->count() > 0;
                @endphp

                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">
                    <div class="p-5 flex-1">

                        <div class="flex items-start gap-3 mb-4">

                            <!-- foto de la mascota-->
                            @if($mascota->foto)
                                <img src="{{ asset('storage/' . $mascota->foto) }}" alt="{{ $mascota->nombre }}"
                                    class="w-14 h-14 object-cover rounded-xl border border-[#d9ddd0] shrink-0">
                            @else
                                <div
                                    class="w-14 h-14 bg-[#eef5e8] border border-[#c8d9be] rounded-xl flex items-center justify-center text-xl shrink-0">
                                    🐾
                                </div>
                            @endif

                            <!-- nombre + especie/raza -->
                            <div class="min-w-0">
                                <p class="font-medium text-[#1e2e1a] truncate">{{ $mascota->nombre }}</p>
                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                    {{ ucfirst($mascota->especie) }} · {{ $mascota->raza }}
                                </p>
                                <p class="text-xs text-[#8a8e84] mt-1">
                                    Dueño: {{ $mascota->dueno->name ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- estado de la mascota (pendiente, aprobada, no aprobada) -->
                        <div class="border-t border-[#f0ede6] pt-3">
                            <div class="flex items-center gap-1.5 mb-0.5">

                                <!-- punto del estado -->
                                <span id="punto-{{ $mascota->id }}"
                                    class="w-1.5 h-1.5 rounded-full shrink-0 {{ $estadoVisual['punto'] }}"></span>

                                <!-- texto del estado -->
                                <span id="estado-{{ $mascota->id }}"
                                    class="text-sm {{ $estadoVisual['etiqueta'] ?? '' }}">
                                    {{ $estadoVisual['texto'] }}
                                </span>
                            </div>

                            @if(auth()->user()->role == 'admin' && $mascota->aprobado === null)
                                <div class="flex gap-1.5 mt-2">

                                    <!-- aprobar mascota -->
                                    <button type="button" data-id="{{ $mascota->id }}" data-valor="1"
                                        data-nombre="{{ $mascota->nombre }}"
                                        class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e] hover:bg-[#ddf0d0] transition-colors duration-200">
                                        Aprobar
                                    </button>

                                    <!-- no aprobar -->
                                    <button type="button" data-id="{{ $mascota->id }}" data-valor="0"
                                        data-nombre="{{ $mascota->nombre }}"
                                        class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] hover:bg-[#f5d0d0] transition-colors duration-200">
                                        No aprobar
                                    </button>

                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- acciones posibles -->
                    <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2 flex-wrap">

                        @if(auth()->user()->role == 'admin')
                            <!-- editar -->
                            <a href="{{ route('admin.mascotas.editar', $mascota) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar
                            </a>

                            <!-- eliminar -->
                            @if($tieneEstancia)
                                <button disabled
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#e8e5de] text-[#c0bdb8] cursor-not-allowed">
                                    Borrar
                                </button>
                            @else
                                <button type="button"
                                    class="btn-eliminar-mascota-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                    data-id="{{ $mascota->id }}" data-nombre="{{ $mascota->nombre }}">
                                    Borrar
                                </button>
                            @endif
                        @else
                            @if(Route::has('mascotas.show'))
                                <a href="{{ route('mascotas.show', $mascota) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Ver ficha
                                </a>
                            @else
                                <span class="text-xs px-3 py-1.5 rounded-lg border border-[#e8e5de] text-[#8a8e84]">
                                    Solo consulta
                                </span>
                            @endif
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        <!-- vista movil -->
        <div class="md:hidden space-y-3">
            @foreach($mascotas as $mascota)
                @php
                    //estado visual reutilizable desde el modelo
                    $estadoVisual = $mascota->getEstadoVisual();

                    $tieneEstancia = $mascota->estancias
                        ->whereIn('estado', ['pendiente', 'confirmada', 'activa'])
                        ->count() > 0;
                @endphp

                <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">
                    <div class="p-4">

                        <div class="flex items-start gap-3 mb-4">

                            <!-- foto de la mascota-->
                            @if($mascota->foto)
                                <img src="{{ asset('storage/' . $mascota->foto) }}" alt="{{ $mascota->nombre }}"
                                    class="w-12 h-12 object-cover rounded-xl border border-[#d9ddd0] shrink-0">
                            @else
                                <div
                                    class="w-12 h-12 bg-[#eef5e8] border border-[#c8d9be] rounded-xl flex items-center justify-center text-lg shrink-0">
                                    🐾
                                </div>
                            @endif

                            <!-- nombre + especie/raza -->
                            <div class="min-w-0">
                                <p class="font-medium text-[#1e2e1a]">{{ $mascota->nombre }}</p>
                                <p class="text-xs text-[#8a8e84] mt-0.5">
                                    {{ ucfirst($mascota->especie) }} · {{ $mascota->raza }}
                                </p>
                                <p class="text-xs text-[#8a8e84] mt-1">
                                    Dueño: {{ $mascota->dueno->name ?? '—' }}
                                </p>
                            </div>
                        </div>

                        <!-- estado de la mascota (pendiente, aprobada, no aprobada) -->
                        <div class="border-t border-[#f0ede6] pt-3">
                            <div class="flex items-center gap-1.5 mb-0.5">

                                <!-- punto del estado -->
                                <span id="punto-{{ $mascota->id }}"
                                    class="w-1.5 h-1.5 rounded-full shrink-0 {{ $estadoVisual['punto'] }}"></span>

                                <!-- texto del estado -->
                                <span id="estado-{{ $mascota->id }}"
                                    class="text-sm {{ $estadoVisual['etiqueta'] ?? '' }}">
                                    {{ $estadoVisual['texto'] }}
                                </span>
                            </div>

                            @if(auth()->user()->role == 'admin' && $mascota->aprobado === null)
                                <div class="flex gap-1.5 mt-2">

                                    <!-- aprobar mascota -->
                                    <button type="button" data-id="{{ $mascota->id }}" data-valor="1"
                                        data-nombre="{{ $mascota->nombre }}"
                                        class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#eef5e8] text-[#2d5a27] border border-[#b0cc9e] hover:bg-[#ddf0d0] transition-colors duration-200">
                                        Aprobar
                                    </button>

                                    <!-- no aprobar -->
                                    <button type="button" data-id="{{ $mascota->id }}" data-valor="0"
                                        data-nombre="{{ $mascota->nombre }}"
                                        class="aprobar-btn text-xs px-2.5 py-1 rounded-lg bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] hover:bg-[#f5d0d0] transition-colors duration-200">
                                        No aprobar
                                    </button>

                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- acciones posibles -->
                    <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2 flex-wrap">

                        @if(auth()->user()->role == 'admin')
                            <!-- editar -->
                            <a href="{{ route('admin.mascotas.editar', $mascota) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar
                            </a>

                            <!-- eliminar -->
                            @if($tieneEstancia)
                                <button disabled
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#e8e5de] text-[#c0bdb8] cursor-not-allowed">
                                    Borrar
                                </button>
                            @else
                                <button type="button"
                                    class="btn-eliminar-mascota-admin text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                    data-id="{{ $mascota->id }}" data-nombre="{{ $mascota->nombre }}">
                                    Borrar
                                </button>
                            @endif
                        @else
                            @if(Route::has('mascotas.show'))
                                <a href="{{ route('mascotas.show', $mascota) }}"
                                    class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                    Ver ficha
                                </a>
                            @else
                                <span class="text-xs px-3 py-1.5 rounded-lg border border-[#e8e5de] text-[#8a8e84]">
                                    Solo consulta
                                </span>
                            @endif
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        <!-- paginacion -->
        <div class="mt-8">
            {{ $mascotas->links() }}
        </div>

    </div>
@endsection
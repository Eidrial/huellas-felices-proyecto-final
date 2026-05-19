@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera principal -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">

            <!-- huellas para decorar -->
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">
                🐾
            </div>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">

                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                        Residencia canina · Huellas Felices
                    </p>

                    <h2 class="font-serif text-3xl font-medium text-[#f0ede6] mb-2">
                        Mis mascotas
                    </h2>

                    <p class="text-sm text-[#9fcf8e]">
                        Consulta su ficha, revisa su estado de aprobación y prepáralas para futuras estancias en la residencia.
                    </p>
                </div>

                <a href="{{ route('mascotas.create') }}"
                    class="shrink-0 inline-flex items-center gap-2 bg-[#f0ede6] text-[#2d5a27] font-medium text-sm px-5 py-3 rounded-xl hover:bg-white transition-colors duration-200">
                    <span class="w-2 h-2 rounded-full bg-[#2d5a27] inline-block"></span>
                    Registrar mascota
                </a>

            </div>
        </div>

        <!-- resumen de los estados de las mascotas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">

            <div class="bg-white rounded-xl border border-[#d9ddd0] p-4">
                <p class="text-xs text-[#8a8e84] mb-1.5">
                    Registradas
                </p>

                <p id="contador-total-mascotas"
                    class="text-3xl font-medium text-[#1e2e1a]">
                    {{ $totalMascotas }}
                </p>
            </div>

            <div class="bg-white rounded-xl border border-[#d9ddd0] p-4">
                <p class="text-xs text-[#8a8e84] mb-1.5">
                    Aprobadas
                </p>

                <p id="contador-aprobadas"
                    class="text-3xl font-medium text-[#3a7a2e]">
                    {{ $aprobadas }}
                </p>
            </div>

            <div class="bg-white rounded-xl border border-[#d9ddd0] p-4">
                <p class="text-xs text-[#8a8e84] mb-1.5">
                    Pendientes
                </p>

                <p id="contador-pendientes"
                    class="text-3xl font-medium text-[#b87a1a]">
                    {{ $pendientes }}
                </p>
            </div>

            <div class="bg-white rounded-xl border border-[#d9ddd0] p-4">
                <p class="text-xs text-[#8a8e84] mb-1.5">
                    No aprobadas
                </p>

                <p id="contador-no-aprobadas"
                    class="text-3xl font-medium text-[#b53030]">
                    {{ $noAprobadas }}
                </p>
            </div>

        </div>

        <!-- informacion sobre la reserva de estancias -->
        <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-xl px-5 py-4 mb-6">
            <h3 class="text-sm font-medium text-[#2d5a27] mb-1">
                Importante antes de reservar
            </h3>

            <p class="text-sm text-[#3d5c38] leading-relaxed">
                Las mascotas aprobadas pueden solicitar estancia con normalidad. Si una mascota sigue pendiente de revisión,
                la reserva podrá registrarse, pero quedará a la espera de validación por parte de administración.
            </p>
        </div>

        <!-- si el usuario no tiene mascotas registradas -->
        @if($mascotas->isEmpty())

            <div class="bg-white rounded-2xl border border-[#d9ddd0] p-12 text-center">

                <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-[#eef5e8] border border-[#c8d9be] flex items-center justify-center text-4xl">
                    🐶
                </div>

                <h3 class="text-xl font-medium text-[#1e2e1a] mb-2">
                    Todavía no has registrado ninguna mascota
                </h3>

                <p class="text-sm text-[#8a8e84] max-w-sm mx-auto mb-7 leading-relaxed">
                    Cuando registres la primera, podrás consultar su estado, editar su ficha y solicitar futuras estancias en Huellas Felices.
                </p>

                <a href="{{ route('mascotas.create') }}"
                    class="inline-flex items-center gap-2 bg-[#3a7a2e] text-[#f0ede6] text-sm font-medium px-5 py-3 rounded-xl hover:bg-[#2d5a27] transition-colors duration-200">
                    <span class="w-2 h-2 rounded-full bg-[#9fcf8e] inline-block"></span>
                    Registrar mascota
                </a>

            </div>

        <!-- cards de las mascotas -->
        @else

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                @foreach ($mascotas as $mascota)

                    @php
                        $tieneEstancia = $mascota->estancias
                            ->whereIn('estado', ['pendiente', 'confirmada', 'activa', 'sin_disponibilidad'])
                            ->count() > 0;

                        $estadoVisual = $mascota->getEstadoVisual();
                    @endphp

                    <div data-id="{{ $mascota->id }}"
                        data-aprobado="{{ $mascota->estadoAprobacionTexto() }}"
                        class="mascota-card bg-white rounded-2xl border border-[#d9ddd0] overflow-hidden h-full flex flex-col">

                        <!-- barra de acento superior (color segun estado) -->
                        <div class="h-[3px] {{ $estadoVisual['barra'] }}"></div>

                        <!-- cuerpo de la tarejeta -->
                        <div class="p-5 sm:p-6 flex-1">

                            <div class="flex items-start gap-4">

                               <!-- foto -->
                                @if($mascota->foto)

                                     <img src="{{ asset('storage/' . $mascota->foto) }}"
                                        alt="Foto de {{ $mascota->nombre }}"
                                        class="w-[72px] h-[72px] object-cover rounded-xl border border-[#d9ddd0] shrink-0">

                                @else

                                    <div class="w-[72px] h-[72px] rounded-xl bg-[#eef5e8] border border-[#c8d9be] flex items-center justify-center text-3xl shrink-0">
                                        🐾
                                    </div>

                                @endif

                                <div class="min-w-0 flex-1">

                                    <h3 class="text-xl font-medium text-[#1e2e1a] leading-tight">
                                        {{ $mascota->nombre }}
                                    </h3>

                                    <p class="text-sm text-[#8a8e84] mt-0.5 mb-3">
                                        {{ ucfirst($mascota->especie) }} · {{ $mascota->raza }}
                                    </p>

                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border {{ $estadoVisual['insignia'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $estadoVisual['punto'] }}"></span>
                                        {{ $estadoVisual['texto'] }}
                                    </span>

                                </div>

                            </div>

                           <!-- datos de las mascotas -->
                            <div class="grid grid-cols-2 gap-2.5 mt-5">

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Edad
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ $mascota->edad }} años
                                    </p>
                                </div>

                                <div class="bg-[#f7f5f0] rounded-xl p-3">
                                    <p class="text-xs text-[#8a8e84] mb-1">
                                        Peso
                                    </p>

                                    <p class="text-sm font-medium text-[#1e2e1a]">
                                        {{ $mascota->peso }} kg
                                    </p>
                                </div>

                            </div>

                            <!-- aviso estancias -->
                            <div class="mt-4">

                                @if($tieneEstancia)

                                    <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-xl px-4 py-2.5 text-xs text-[#7a4e10] leading-relaxed">
                                        Tiene una estancia pendiente, confirmada, activa o sin disponibilidad — la ficha se puede consultar y editar, pero no eliminar.
                                    </div>

                                @else

                                    <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-xl px-4 py-2.5 text-xs text-[#2d5a27]">
                                        Sin estancias abiertas actualmente.
                                    </div>

                                @endif

                            </div>

                        </div>

                        <!-- acciones card mascota -->
                        <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-5 sm:px-6 py-3.5">

                            <div class="flex flex-wrap gap-2">

                                <!-- ver ficha detallada -->
                                <a href="{{ route('mascotas.show', $mascota) }}"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-[#b0cc9e] text-[#2d5a27] text-sm font-medium hover:bg-[#eef5e8] transition-colors duration-200">
                                    Ver ficha
                                </a>

                                <!-- editar ficha del animal -->
                                <a href="{{ route('mascotas.edit', $mascota) }}"
                                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#3a7a2e] text-[#f0ede6] text-sm font-medium hover:bg-[#2d5a27] transition-colors duration-200">
                                    Editar
                                </a>

                                <!-- si tiene estancia -->
                                @if($tieneEstancia)

                                    <!-- no poder borrar mascota -->
                                    <button
                                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#eae8e3] text-[#a8a49f] text-sm font-medium cursor-not-allowed"
                                        disabled>
                                        Borrar
                                    </button>

                                <!-- si no tiene -->
                                @else

                                    <!-- poder borrar mascota -->
                                    <button type="button"
                                        class="btn-eliminar-mascota inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#c9342e] text-white text-sm font-medium hover:bg-[#a82a25] transition-colors duration-200"
                                        data-id="{{ $mascota->id }}"
                                        data-nombre="{{ $mascota->nombre }}">
                                        Borrar
                                    </button>

                                @endif

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

            <!-- paginacion -->
            <div class="mt-8">
                {{ $mascotas->links() }}
            </div>

        @endif

    </div>
@endsection
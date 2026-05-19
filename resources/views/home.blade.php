@extends('layouts.app')

@section('fullwidth')
    <div class="relative w-full min-h-[100svh] overflow-hidden bg-[#1a2e17]">

        <!-- imagen de fondo -->
        <img src="{{ asset('images/principal.png') }}" alt="Huellas Felices"
            class="absolute inset-0 w-full h-full object-cover object-center opacity-70">

        <!-- degradado inferior para legibilidad del texto -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/20 to-black/75"></div>

        <!-- contenido encima de la imagen -->
        <div class="relative z-10 flex items-end min-h-[100svh]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 md:py-20 w-full text-center sm:text-left">

                <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-4">
                    Residencia canina · Huellas Felices
                </p>

                <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-medium text-[#f0ede6] leading-tight mb-3 max-w-3xl mx-auto sm:mx-0">
                    Tu mejor amigo
                </h1>
                <h1 class="font-serif text-4xl sm:text-5xl md:text-6xl font-medium text-[#f0ede6] leading-tight mb-4 max-w-3xl">
                    merece lo mejor
                </h1>

                <p class="text-[#c8dfc0] text-base md:text-lg leading-relaxed mb-8 max-w-xl mx-auto sm:mx-0">
                    Cuidamos de tu perro como si fuera parte de nuestra familia. Estancias seguras, cariñosas y adaptadas a
                    cada mascota.
                </p>

                <!-- botones segun si el usuario esta logueado o no -->
                <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 justify-center sm:justify-start">

                    @auth

                        @if(auth()->user()->role === 'usuario')
                            <a href="{{ route('estancias.create') }}"
                                class="inline-flex items-center justify-center gap-2 bg-[#2d5a27] hover:bg-[#4a8a38] text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200">
                                <span class="w-2 h-2 rounded-full bg-[#9fcf8e] inline-block"></span>
                                Reservar estancia
                            </a>

                            <a href="{{ route('mascotas.index') }}"
                                class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200 backdrop-blur-sm">
                                Mis mascotas
                            </a>
                        @endif

                        @if(auth()->user()->role === 'cuidador')
                            <a href="{{ route('cuidados.index') }}"
                                class="inline-flex items-center justify-center gap-2 bg-[#2d5a27] hover:bg-[#4a8a38] text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200">
                                <span class="w-2 h-2 rounded-full bg-[#9fcf8e] inline-block"></span>
                                Ver cuidados
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.estancias.index') }}"
                                class="inline-flex items-center justify-center gap-2 bg-[#2d5a27] hover:bg-[#4a8a38] text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200">
                                <span class="w-2 h-2 rounded-full bg-[#9fcf8e] inline-block"></span>
                                Panel de administración
                            </a>

                            <a href="{{ route('admin.usuarios') }}"
                                class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200 backdrop-blur-sm">
                                Gestionar usuarios
                            </a>
                        @endif

                    @else
                        <!-- invitado -->
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center gap-2 bg-[#2d5a27] hover:bg-[#4a8a38] text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200">
                            <span class="w-2 h-2 rounded-full bg-[#9fcf8e] inline-block"></span>
                            Iniciar sesión
                        </a>

                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 text-[#f0ede6] text-sm font-medium px-6 py-3 rounded-xl transition-colors duration-200 backdrop-blur-sm">
                            Registrarse
                        </a>
                    @endauth

                </div>

            </div>
        </div>

    </div>
@endsection
<nav class="sticky top-0 z-50 bg-[#2d5a27] border-b border-[#1e3d1a]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between min-h-14 gap-4 xl:gap-6">

            <!-- logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Huellas Felices" class="h-8 w-auto">
                <span class="text-[#f0ede6] font-serif text-base font-medium tracking-wide whitespace-nowrap">
                    Huellas Felices
                </span>
            </a>

            <!-- enlaces escritorio / tablet grande -->
            <div class="hidden lg:flex items-center justify-end gap-1 flex-wrap py-2">
                @auth

                    @if(auth()->user()->role == 'usuario')
                        <a href="{{ route('profile.edit') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Mi perfil
                        </a>

                        <a href="{{ route('mascotas.index') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Mis mascotas
                        </a>

                        <a href="{{ route('estancias.index') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Mis estancias
                        </a>

                        <a href="{{ route('estancias.create') }}"
                            class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38] text-sm px-4 py-1.5 rounded-lg font-medium transition-colors duration-200 ml-1">
                            Reservar estancia
                        </a>
                    @endif

                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('admin.usuarios') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Usuarios
                        </a>

                        <a href="{{ route('admin.mascotas.index') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Mascotas
                        </a>

                        <a href="{{ route('admin.estancias.index') }}"
                            class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Estancias
                        </a>

                        <a href="{{ route('admin.usuarios.crear') }}"
                            class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38] text-sm px-4 py-1.5 rounded-lg font-medium transition-colors duration-200 ml-1">
                            Crear usuario
                        </a>
                    @endif

                    @if(auth()->user()->role == 'cuidador')
                        <a href="{{ route('cuidador.mascotas') }}" class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e]
                            text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Mascotas
                        </a>

                        <a href="{{ route('cuidador.estancias') }}" class="text-[#c8e0b8] hover:text-[#f0ede6] bg-[#1a3d15]
                            hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200">
                            Estancias
                        </a>
                    @endif

                    @if(auth()->user()->role == 'cuidador' || auth()->user()->role == 'admin')
                            <a href="{{ route('cuidados.index') }}"
                        class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg
                        transition-colors duration-200">
                        Panel de cuidados
                        </a>
                    @endif

                    <a href="{{ route('ayuda') }}" class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm
                        px-3 py-1.5 rounded-lg transition-colors duration-200">
                        Ayuda
                    </a>

                    <!-- separador -->
                    <span class="w-px h-5 bg-[#3a7a2e] mx-1"></span>

                    <!-- cerrar sesion -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-[#f5a8a8] hover:text-white hover:bg-[#7a2020] text-sm px-3 py-1.5 rounded-lg transition-colors duration-200 cursor-pointer bg-transparent border-none">
                            Cerrar sesión
                        </button>
                    </form>

                @endauth

                @guest
                        <a href="{{ route('ayuda') }}"
                    class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-1.5 rounded-lg
                    transition-colors duration-200">
                    Ayuda
                    </a>

                    <a href="{{ route('login') }}" class="text-[#c8e0b8] hover:text-[#f0ede6] hover:bg-[#3a7a2e] text-sm
                        px-3 py-1.5 rounded-lg transition-colors duration-200">
                        Iniciar sesión
                    </a>

                    <a href="{{ route('register') }}" class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38] text-sm px-4
                        py-1.5 rounded-lg font-medium transition-colors duration-200">
                        Registrarse
                    </a>
                @endguest
                </div> <!-- boton hamburguesa (tablet/movil) -->
                <button id="boton-menu"
                    class="lg:hidden flex flex-col justify-center items-center w-9 h-9 gap-[5px] rounded-lg hover:bg-[#3a7a2e] transition-colors duration-200"
                    aria-label="Abrir menú">

                    <span
                        class="linea-menu block w-5 h-[1.5px] bg-[#f0ede6] transition-all duration-300 origin-center"></span>

                    <span class="linea-menu block w-5 h-[1.5px] bg-[#f0ede6] transition-all duration-300"></span>

                    <span
                        class="linea-menu block w-5 h-[1.5px] bg-[#f0ede6] transition-all duration-300 origin-center"></span>
                </button>
            </div>
        </div>

        <!-- menu tablet/movil -->
        <div id="menu-movil" class="lg:hidden hidden border-t border-[#1e3d1a] bg-[#2d5a27]">
            <div class="max-w-7xl mx-auto px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-1">

                @auth

                    <!-- nombre del usuario -->
                    <p class="text-xs text-[#9fcf8e] uppercase tracking-widest px-3 py-1 mb-1 sm:col-span-2">
                        {{ auth()->user()->name }}
                        <span class="normal-case tracking-normal ml-1 opacity-60">
                        · {{ auth()->user()->role }}
                        </span> </p>

                        @if(auth()->user()->role == 'usuario')
                                <a href="{{ route('profile.edit') }}"
                            class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg transition-colors
                            duration-200">
                            Mi perfil
                            </a>

                            <a href="{{ route('mascotas.index') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5
                                rounded-lg transition-colors duration-200">
                                Mis mascotas
                            </a>

                            <a href="{{ route('estancias.index') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3
                                py-2.5 rounded-lg transition-colors duration-200">
                                Mis estancias
                            </a>

                            <a href="{{ route('estancias.create') }}" class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38]
                                text-sm px-3 py-2.5 rounded-lg font-medium transition-colors duration-200 sm:col-span-2">
                                Reservar estancia
                            </a>
                        @endif

                        @if(auth()->user()->role == 'admin')
                                <a href="{{ route('admin.usuarios') }}"
                            class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg transition-colors
                            duration-200">
                            Usuarios
                            </a>

                            <a href="{{ route('admin.mascotas.index') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3
                                py-2.5 rounded-lg transition-colors duration-200">
                                Mascotas
                            </a>

                            <a href="{{ route('admin.estancias.index') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3
                                py-2.5 rounded-lg transition-colors duration-200">
                                Estancias
                            </a>

                            <a href="{{ route('admin.usuarios.crear') }}" class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38]
                                text-sm px-3 py-2.5 rounded-lg font-medium transition-colors duration-200">
                                Crear usuario
                            </a>
                        @endif

                        @if(auth()->user()->role == 'cuidador')
                                <a href="{{ route('cuidador.mascotas') }}"
                            class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg transition-colors
                            duration-200">
                            Mascotas
                            </a>

                            <a href="{{ route('cuidador.estancias') }}" class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#3a7a2e] text-sm px-3
                                py-2.5 rounded-lg transition-colors duration-200">
                                Estancias
                            </a>
                        @endif

                        @if(auth()->user()->role == 'cuidador' || auth()->user()->role == 'admin')
                                <a href="{{ route('cuidados.index') }}"
                            class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg transition-colors
                            duration-200">
                            Panel de cuidados
                            </a>
                        @endif

                        <a href="{{ route('ayuda') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5
                            rounded-lg transition-colors duration-200">
                            Ayuda
                        </a>

                    <div class="h-px bg-[#3a7a2e] my-1 sm:col-span-2"></div>

                    <form method="POST" action="{{ route('logout') }}" class="sm:col-span-2">
                        @csrf

                        <button type="submit"
                            class="w-full text-left text-[#f5a8a8] hover:bg-[#7a2020] hover:text-white text-sm px-3 py-2.5 rounded-lg transition-colors duration-200 cursor-pointer bg-transparent border-none">
                            Cerrar sesión
                        </button>
                    </form>

                @endauth

                @guest
                        <a href="{{ route('ayuda') }}"
                    class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg transition-colors duration-200">
                    Ayuda
                    </a>

                    <a href="{{ route('login') }}" class="text-[#f0ede6] hover:bg-[#3a7a2e] text-sm px-3 py-2.5 rounded-lg
                        transition-colors duration-200">
                        Iniciar sesión
                    </a>

                    <a href="{{ route('register') }}" class="text-[#f0ede6] bg-[#1a3d15] hover:bg-[#4a8a38] text-sm px-3
                        py-2.5 rounded-lg font-medium transition-colors duration-200 sm:col-span-2">
                        Registrarse
                    </a>
                @endguest
                </div> </div>
</nav>
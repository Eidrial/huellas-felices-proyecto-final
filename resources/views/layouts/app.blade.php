<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <!-- proteger aplicacion contra ataques CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Huellas Felices</title>

    <!-- css y js global de la aplicación -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- .js especifico segun el rol del usuario logueado -->
    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'cuidador']))
        <!-- solo rol admin y cuidador usa este .js -->
        @vite('resources/js/admin.js')
    @elseif(auth()->check() && auth()->user()->role == 'usuario')
        <!-- solo rol usuario usa este .js -->
        @vite('resources/js/usuario.js')
    @endif
</head>

<body class="bg-[#f7f5f0] font-sans min-h-screen flex flex-col">

    <!-- MODAL GLOBAL (para acciones), asi no hay que ponerlo en cada archivo -->
    <div id="modal-confirmacion"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 px-4">
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-lg w-full max-w-sm">
            <h3 id="modal-titulo" class="text-lg font-medium text-[#1e2e1a] mb-4"></h3>
            <p id="modal-texto" class="text-sm text-[#8a8e84] mb-6"></p>

            <div class="flex justify-end gap-2">
                <button id="modal-cancelar" type="button"
                    class="px-4 py-2 text-sm rounded-xl border border-[#d9ddd0] text-[#8a8e84] hover:bg-[#f7f5f0] transition-colors duration-200">
                    Cancelar
                </button>

                <button id="modal-confirmar" type="button"
                    class="px-4 py-2 text-sm rounded-xl bg-[#3a7a2e] text-[#f0ede6] hover:bg-[#2d5a27] transition-colors duration-200">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- menu de navegacion segun rol -->
    @include('components.menu')

    <!-- contenido a ancho completo (sin contenedor, para el home y paginas similares) -->
    @yield('fullwidth')

    <!-- contenido principal con contenedor -->
    <main class="w-full max-w-7xl mx-auto px-4 flex-grow">

        <!-- mensajes globales de error -->
        @if(session('error'))
            <div class="mensaje-sesion bg-red-100 text-red-700 p-3 mb-4 rounded-xl text-center text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- mensajes globales de exito -->
        @if(session('success'))
            <div class="mensaje-sesion bg-[#eef5e8] text-[#2d5a27] p-3 mb-4 rounded-xl text-center text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- mensajes globales de estado -->
        @if(session('status') === 'verification-link-sent')
            <div class="mensaje-sesion bg-[#eef5e8] text-[#2d5a27] p-3 mb-4 rounded-xl text-center text-sm">
                Se ha enviado un nuevo enlace de verificación. Puede tardar unos minutos en llegar.
            </div>
        @endif

        <!-- MENSAJE AJAX GLOBAL -->
        <div id="mensaje-ajax" class="mensaje-sesion hidden whitespace-pre-line"></div>

        <!-- seccion donde se cargará el contenido especifico de cada vista -->
        @yield('content')

    </main>

    <!-- footer -->
    <footer class="bg-[#1e2e1a] text-[#f0ede6] mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- logo y frase -->
                <div class="flex flex-col items-center md:items-start md:justify-self-start">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Huellas Felices" class="h-8 w-auto">
                        <span class="text-[#f0ede6] font-serif text-base font-medium tracking-wide">
                            Huellas Felices
                        </span>
                    </a>
                    <p class="text-sm text-[#9fcf8e] max-w-xs text-center md:text-left">
                        Residencia canina donde cuidamos de tu mascota como si fuera parte de nuestra familia.
                    </p>
                </div>

                <!-- enlaces segun el rol -->
                <div class="text-sm text-center md:text-left md:justify-self-center">
                    <p class="mb-2 text-[#9fcf8e]">
                        @auth
                            @if(auth()->user()->role === 'admin')
                                Administración
                            @elseif(auth()->user()->role === 'cuidador')
                                Cuidados
                            @else
                                Tu cuenta
                            @endif
                        @else
                            Acceso
                        @endauth
                    </p>
                    <ul class="space-y-1">
                        <li><a href="{{ route('home') }}"
                                class="hover:text-white transition-colors duration-200">Inicio</a></li>
                        @auth
                            @if(auth()->user()->role === 'usuario')
                                <li><a href="{{ route('mascotas.index') }}"
                                        class="hover:text-white transition-colors duration-200">Mis mascotas</a></li>
                                <li><a href="{{ route('estancias.index') }}"
                                        class="hover:text-white transition-colors duration-200">Mis estancias</a></li>
                            @endif
                            @if(auth()->user()->role === 'cuidador')
                                <li><a href="{{ route('cuidados.index') }}"
                                        class="hover:text-white transition-colors duration-200">Panel de cuidados</a></li>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                <li><a href="{{ route('admin.estancias.index') }}"
                                        class="hover:text-white transition-colors duration-200">Panel admin</a></li>
                                <li><a href="{{ route('admin.usuarios') }}"
                                        class="hover:text-white transition-colors duration-200">Usuarios</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}"
                                    class="hover:text-white transition-colors duration-200">Acceder</a></li>
                            <li><a href="{{ route('register') }}"
                                    class="hover:text-white transition-colors duration-200">Registro</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- contacto -->
                <div class="text-sm text-center md:text-left md:justify-self-end">
                    <p class="mb-2 text-[#9fcf8e]">Contacto</p>
                    <p>Email: residenciahuellasfelices@gmail.com</p>
                    <p>Tel: 722 72 72 72</p>
                </div>

            </div>

            <div class="border-t border-[#2d5a27] mt-6 pt-4 text-center text-xs text-[#9fcf8e]">
                © {{ date('Y') }} Huellas Felices · Proyecto de fin de ciclo
            </div>
        </div>
    </footer>

</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- proteger aplicacion contra ataques CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Huellas Felices</title>

    <!-- css y js global de la aplicación -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- .js especifico segun el rol del usuario logueado -->
    @if(auth()->check() && auth()->user()->role == 'admin')
        <!-- solo rol admin usa este .js -->
        @vite('resources/js/admin.js')
    @elseif(auth()->check() && auth()->user()->role == 'usuario')
        <!-- solo rol usuario usa este .js -->
        @vite('resources/js/usuario.js')
    @endif
</head>

<body class="bg-gray-100 font-sans min-h-screen">

    <!-- MODAL GLOBAL (para acciones), asi no hay que ponerlo en cada archivo -->
    <div id="modal-confirmacion"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-80">
            <h3 id="modal-titulo" class="text-lg font-bold mb-4"></h3>
            <p id="modal-texto" class="mb-6"></p>

            <div class="flex justify-end gap-2">
                <button id="modal-cancelar" type="button" class="px-4 py-2 bg-red-600 rounded hover:bg-red-300">
                    Cancelar
                </button>

                <button id="modal-confirmar" type="button"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-300">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- cabecera comun -->
    <header class="bg-white shadow p-4 mb-6">
        <h1 class="text-xl font-bold">Residencia animal Huellas Felices</h1>

        <!-- menu de navegacion segun rol -->
        @include('components.menu')
    </header>

    <!-- contenido principal -->
    <main class="container mx-auto px-4">

        <!-- mensajes globales de error -->
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded text-center">
                {{ session('error') }}
            </div>
        @endif

        <!-- mensajes globales de exito -->
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 mb-4 rounded text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- MENSAJE AJAX GLOBAL -->
        <div id="mensaje-ajax" class="hidden p-3 mb-4 rounded text-center font-semibold"></div>

        <!-- seccion donde se cargará el contenido especifico de cada vista -->
        @yield('content')
    </main>

</body>

</html>
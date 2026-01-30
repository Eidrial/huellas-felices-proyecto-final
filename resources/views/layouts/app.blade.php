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

        <!-- seccion donde se cargará el contenido especifico de cada vista -->
        @yield('content')
    </main>

</body>

</html>
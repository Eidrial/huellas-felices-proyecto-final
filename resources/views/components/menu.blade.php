<nav class="flex gap-4 items-center">
    @auth
        <!-- enlaces solo para usuarios -->
        @if(auth()->user()->role == 'usuario')
            <a href="{{ route('mascotas.index') }}" class="text-blue-600 hover:underline">
                Mis mascotas
            </a>

            <a href="{{ route('mascotas.create') }}" class="text-green-600 hover:underline">
                Añadir mascota
            </a>
        @endif

        <!-- enlaces extra solo para admins -->
        @if(auth()->user()->role == 'admin')
            <a href="{{ route('admin.usuarios') }}" class="text-purple-600 hover:underline">
                Gestionar usuarios
            </a>

            <a href="{{ route('admin.mascotas.index') }}" class="text-purple-600 hover:underline">
                Gestionar mascotas
            </a>

            <a href="{{ route('admin.usuarios.crear') }}" class="bg-green-600 text-black px-4 py-2 rounded hover:bg-green-700">
                Crear nuevo usuario
            </a>
        @endif

        <!-- botón para cerrar sesión -->
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:underline bg-transparent border-none p-0 cursor-pointer">
                Cerrar sesión
            </button>
        </form>
    @endauth

    @guest
        <!-- visibles solo si el usuario NO está logueado -->
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
            Iniciar sesión
        </a>

        <a href="{{ route('register') }}" class="text-blue-600 hover:underline">
            Registrarse
        </a>
    @endguest
</nav>
@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">
                🐾
            </div>

            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                Administración
            </p>

            <h2 class="font-serif text-3xl font-medium text-[#f0ede6]">
                Gestión de usuarios
            </h2>
        </div>

        <!-- escritorio -->
        <div class="hidden xl:block bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="border-b border-[#e8e5de]">
                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Nombre
                            </th>

                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Email
                            </th>

                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Rol
                            </th>

                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Cambiar rol
                            </th>

                            <th class="text-left px-5 py-3.5 text-xs font-medium text-[#8a8e84] uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody id="usuarios-tbody" class="divide-y divide-[#f0ede6]">
                        @foreach($usuarios as $usuario)
                            @php
                                $roles = [
                                    'admin' => [
                                        'punto' => 'bg-[#c9342e]',
                                        'texto' => 'text-[#9b2a2a]',
                                    ],
                                    'cuidador' => [
                                        'punto' => 'bg-[#3a7abf]',
                                        'texto' => 'text-[#1a4f8a]',
                                    ],
                                    'usuario' => [
                                        'punto' => 'bg-[#5a9e47]',
                                        'texto' => 'text-[#2d5a27]',
                                    ],
                                ];

                                $rol = $roles[$usuario->role] ?? $roles['usuario'];
                            @endphp

                            <tr data-id="{{ $usuario->id }}" class="hover:bg-[#fafaf8] transition-colors duration-150">

                                <td class="px-5 py-3.5">
                                    <p class="font-medium text-[#1e2e1a]">
                                        {{ $usuario->name }}
                                    </p>

                                    @if(auth()->id() == $usuario->id)
                                        <p class="text-xs text-[#8a8e84] mt-0.5">
                                            Tu cuenta
                                        </p>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-[#8a8e84]">
                                    {{ $usuario->email }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-1.5 rol-text">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $rol['punto'] }}"></span>

                                        <span class="{{ $rol['texto'] }} text-sm">
                                            {{ ucfirst($usuario->role) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-3.5">
                                    @if(auth()->id() != $usuario->id)
                                        <select
                                            class="cambiar-rol border border-[#d9ddd0] bg-[#fafaf8] rounded-lg px-3 pr-8 py-1.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] transition-colors duration-200"
                                            data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">

                                            <option value="usuario" @selected($usuario->role == 'usuario')>
                                                Usuario
                                            </option>

                                            <option value="cuidador" @selected($usuario->role == 'cuidador')>
                                                Cuidador
                                            </option>

                                            <option value="admin" @selected($usuario->role == 'admin')>
                                                Admin
                                            </option>
                                        </select>
                                    @else
                                        <span class="text-xs text-[#c0bdb8]">
                                            No disponible.
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex gap-1.5 flex-wrap">
                                        @if(auth()->id() == $usuario->id)
                                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                Editar perfil
                                            </a>
                                        @else
                                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                                Editar
                                            </a>

                                            <button
                                                class="btn-eliminar text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                                data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">
                                                Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

        <!-- tablet -->
        <div class="hidden md:grid xl:hidden grid-cols-2 gap-4">
            @foreach($usuarios as $usuario)
                @php
                    $roles = [
                        'admin' => [
                            'punto' => 'bg-[#c9342e]',
                            'texto' => 'text-[#9b2a2a]',
                        ],
                        'cuidador' => [
                            'punto' => 'bg-[#3a7abf]',
                            'texto' => 'text-[#1a4f8a]',
                        ],
                        'usuario' => [
                            'punto' => 'bg-[#5a9e47]',
                            'texto' => 'text-[#2d5a27]',
                        ],
                    ];

                    $rol = $roles[$usuario->role] ?? $roles['usuario'];
                @endphp

                <div data-id="{{ $usuario->id }}"
                    class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                    <div class="p-5 flex-1">
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <p class="font-medium text-[#1e2e1a] truncate">
                                    {{ $usuario->name }}
                                </p>

                                <p class="text-xs text-[#8a8e84] mt-0.5 break-all">
                                    {{ $usuario->email }}
                                </p>

                                <div class="flex items-center gap-1.5 mt-2 rol-text">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $rol['punto'] }}"></span>

                                    <span class="{{ $rol['texto'] }} text-sm">
                                        {{ ucfirst($usuario->role) }}
                                    </span>
                                </div>

                                @if(auth()->id() == $usuario->id)
                                    <p class="text-xs text-[#8a8e84] mt-1">
                                        Tu cuenta
                                    </p>
                                @endif
                            </div>

                            @if(auth()->id() != $usuario->id)
                                <select
                                    class="cambiar-rol border border-[#d9ddd0] bg-[#fafaf8] rounded-lg px-2 pr-8 py-1.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] transition-colors duration-200"
                                    data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">

                                    <option value="usuario" @selected($usuario->role == 'usuario')>
                                        Usuario
                                    </option>

                                    <option value="cuidador" @selected($usuario->role == 'cuidador')>
                                        Cuidador
                                    </option>

                                    <option value="admin" @selected($usuario->role == 'admin')>
                                        Admin
                                    </option>
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2 flex-wrap">
                        @if(auth()->id() == $usuario->id)
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar perfil
                            </a>
                        @else
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar
                            </a>

                            <button
                                class="btn-eliminar text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">
                                Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- movil -->
        <div class="md:hidden space-y-3">
            @foreach($usuarios as $usuario)
                @php
                    $roles = [
                        'admin' => [
                            'punto' => 'bg-[#c9342e]',
                            'texto' => 'text-[#9b2a2a]',
                        ],
                        'cuidador' => [
                            'punto' => 'bg-[#3a7abf]',
                            'texto' => 'text-[#1a4f8a]',
                        ],
                        'usuario' => [
                            'punto' => 'bg-[#5a9e47]',
                            'texto' => 'text-[#2d5a27]',
                        ],
                    ];

                    $rol = $roles[$usuario->role] ?? $roles['usuario'];
                @endphp

                <div data-id="{{ $usuario->id }}" class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden">

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2">

                            <div class="min-w-0">
                                <p class="font-medium text-[#1e2e1a]">
                                    {{ $usuario->name }}
                                </p>

                                <p class="text-xs text-[#8a8e84] mt-0.5 break-all">
                                    {{ $usuario->email }}
                                </p>

                                <div class="flex items-center gap-1.5 mt-2 rol-text">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $rol['punto'] }}"></span>

                                    <span class="{{ $rol['texto'] }} text-sm">
                                        {{ ucfirst($usuario->role) }}
                                    </span>
                                </div>

                                @if(auth()->id() == $usuario->id)
                                    <p class="text-xs text-[#8a8e84] mt-0.5">
                                        Tu cuenta
                                    </p>
                                @endif
                            </div>

                            @if(auth()->id() != $usuario->id)
                                <select
                                    class="cambiar-rol border border-[#d9ddd0] bg-[#fafaf8] rounded-lg px-2 pr-8 py-1.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] transition-colors duration-200"
                                    data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">

                                    <option value="usuario" @selected($usuario->role == 'usuario')>
                                        Usuario
                                    </option>

                                    <option value="cuidador" @selected($usuario->role == 'cuidador')>
                                        Cuidador
                                    </option>

                                    <option value="admin" @selected($usuario->role == 'admin')>
                                        Admin
                                    </option>
                                </select>
                            @endif
                        </div>
                    </div>

                    <div class="bg-[#fafaf8] border-t border-[#e8e5de] px-4 py-3 flex gap-2">
                        @if(auth()->id() == $usuario->id)
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar perfil
                            </a>
                        @else
                            <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                class="text-xs px-3 py-1.5 rounded-lg border border-[#d9ddd0] text-[#1e2e1a] hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                                Editar
                            </a>

                            <button
                                class="btn-eliminar text-xs px-3 py-1.5 rounded-lg border border-[#e8b4b4] text-[#9b2a2a] hover:bg-[#fceaea] transition-colors duration-200"
                                data-id="{{ $usuario->id }}" data-nombre="{{ $usuario->name }}">
                                Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- paginacion -->
        <div class="mt-8">
            {{ $usuarios->links() }}
        </div>

    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-4 text-center">Gestión de usuarios</h2>

        <!-- tabla única de todos los usuarios -->
        <table class="w-full bg-white shadow rounded text-center mb-4">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">Nombre</th>
                    <th class="p-2">Email</th>
                    <th class="p-2">Rol</th>
                    <th class="p-2">Cambiar rol</th>
                    <th class="p-2">Otras acciones</th>
                </tr>
            </thead>

            <tbody id="usuarios-tbody">
                @foreach($usuarios as $usuario)
                    <tr data-id="{{ $usuario->id }}" class="border-t hover:bg-gray-50">
                        <!-- mostrar nombre y email -->
                        <td class="p-2">{{ $usuario->name }}</td>
                        <td class="p-2">{{ $usuario->email }}</td>

                        <!-- mostrar rol actual -->
                        <td class="p-2 rol-text">{{ ucfirst($usuario->role) }}</td>

                        <!-- cambiar rol -->
                        <td class="p-2">
                            @if(auth()->id() != $usuario->id)
                                <select class="cambiar-rol border rounded p-1" data-id="{{ $usuario->id }}"
                                    data-nombre="{{ $usuario->name }}">
                                    <option value="usuario" @selected($usuario->role == 'usuario')>Usuario</option>
                                    <option value="cuidador" @selected($usuario->role == 'cuidador')>Cuidador</option>
                                    <option value="admin" @selected($usuario->role == 'admin')>Admin</option>
                                </select>
                            @endif
                        </td>

                        <!-- acciones -->
                        <td class="p-2 flex justify-center gap-2">
                            <!-- editar usuario -->
                            @if(auth()->id() != $usuario->id)
                                <a href="{{ route('admin.usuarios.editar', $usuario) }}"
                                    class="bg-yellow-500 text-black px-2 py-1 rounded hover:bg-yellow-600">
                                    Editar
                                </a>
                            @endif

                            <!-- eliminar usuario -->
                            @if(auth()->id() != $usuario->id)
                                <button class="btn-eliminar bg-red-600 text-white px-2 py-1 rounded" data-id="{{ $usuario->id }}"
                                    data-nombre="{{ $usuario->name }}">
                                    Eliminar
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
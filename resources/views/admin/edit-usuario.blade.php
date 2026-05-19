@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto px-4 py-8 md:py-10">

        <!-- cabecera -->
        <div class="mb-7">
            <a href="{{ route('admin.usuarios') }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> Volver a usuarios
            </a>

            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                {{ auth()->id() == $usuario->id ? 'Editar mi perfil' : 'Editar usuario' }}
            </h2>

            <p class="text-sm text-[#8a8e84]">
                {{ auth()->id() == $usuario->id ? 'Actualiza tus datos de administrador' : 'Actualiza los datos de ' . $usuario->name }}
            </p>
        </div>

        <!-- errores de validación -->
        @if ($errors->any())
            <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">
            <form method="POST" action="{{ route('admin.usuarios.actualizar', ['user' => $usuario->id]) }}"
                class="space-y-5">
                @csrf
                @method('PUT')

                <!-- nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Nombre
                    </label>

                    <input type="text" id="name" name="name"
                        value="{{ old('name', $usuario->name) }}"
                        required
                        minlength="2"
                        maxlength="255"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="Nombre completo">
                </div>

                @if($usuario->role != 'admin')
                    <!-- datos personales -->
                    <div class="space-y-5">

                        <!-- apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Apellidos
                                <span class="text-[#8a8e84] font-normal">(opcional)</span>
                            </label>

                            <input type="text" id="apellidos" name="apellidos"
                                value="{{ old('apellidos', $usuario->apellidos) }}"
                                minlength="2"
                                maxlength="255"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                placeholder="Apellidos">
                        </div>

                        <!-- dni -->
                        <div>
                            <label for="dni" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                DNI / NIE
                                <span class="text-[#8a8e84] font-normal">(opcional)</span>
                            </label>

                            <input type="text" id="dni" name="dni"
                                value="{{ old('dni', $usuario->dni) }}"
                                minlength="9"
                                maxlength="12"
                                pattern="^([0-9]{8}[A-Za-z]|[XYZxyz][0-9]{7}[A-Za-z])$"
                                title="Introduce un DNI o NIE válido. Ejemplo: 12345678A o X1234567L"
                                class="w-full uppercase border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                placeholder="12345678A">
                        </div>

                        <!-- telefono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Teléfono
                                <span class="text-[#8a8e84] font-normal">(opcional)</span>
                            </label>

                            <input type="tel" id="telefono" name="telefono"
                                value="{{ old('telefono', $usuario->telefono) }}"
                                minlength="9"
                                maxlength="15"
                                inputmode="numeric"
                                pattern="[0-9+ ]{9,15}"
                                title="Introduce un teléfono válido"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                placeholder="600123123">
                        </div>

                        <!-- direccion -->
                        <div>
                            <label for="direccion" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Dirección
                                <span class="text-[#8a8e84] font-normal">(opcional)</span>
                            </label>

                            <input type="text" id="direccion" name="direccion"
                                value="{{ old('direccion', $usuario->direccion) }}"
                                maxlength="255"
                                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                placeholder="Calle, número, ciudad">
                        </div>

                    </div>
                @endif

                <!-- email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Correo electrónico
                    </label>

                    <input type="email" id="email" name="email"
                        value="{{ old('email', $usuario->email) }}"
                        required
                        maxlength="255"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="correo@ejemplo.com">
                </div>

                <!-- contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Nueva contraseña <span class="text-[#8a8e84] font-normal">(opcional)</span>
                    </label>

                    <div class="relative">
                        <input type="password" id="password" name="password"
                            minlength="8"
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            placeholder="Mínimo 8 caracteres, letras y números">

                        <button type="button"
                            onclick="mostrarContra('password', 'ojo-admin-edit1-abierto', 'ojo-admin-edit1-cerrado')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#2d5a27] transition-colors duration-200">

                            <!-- ojo abierto -->
                            <svg id="ojo-admin-edit1-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- ojo cerrado -->
                            <svg id="ojo-admin-edit1-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-xs text-[#8a8e84] mt-1.5">
                        Déjala en blanco si no quieres cambiarla. Si la cambias, debe tener mínimo 8 caracteres, una letra y
                        un número.
                    </p>
                </div>

                <!-- confirmar contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Confirmar nueva contraseña
                    </label>

                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            placeholder="Repite la nueva contraseña">

                        <button type="button"
                            onclick="mostrarContra('password_confirmation', 'ojo-admin-edit2-abierto', 'ojo-admin-edit2-cerrado')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#2d5a27] transition-colors duration-200">

                            <!-- ojo abierto -->
                            <svg id="ojo-admin-edit2-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- ojo cerrado -->
                            <svg id="ojo-admin-edit2-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- botones -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 pt-1">
                    <a href="{{ route('admin.usuarios') }}"
                        class="text-sm px-4 py-2.5 rounded-xl border border-[#d9ddd0] text-[#8a8e84] hover:bg-[#f7f5f0] transition-colors duration-200 text-center">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="text-sm px-5 py-2.5 rounded-xl bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] font-medium transition-colors duration-200">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>

    </div>
@endsection
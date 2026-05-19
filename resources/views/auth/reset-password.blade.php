@extends('layouts.app')

@section('content')
    <div class="max-w-md md:max-w-xl mx-auto px-4 my-8 md:my-12">

        <!-- cabecera -->
        <div class="text-center mb-8">
            <h2 class="font-serif text-3xl md:text-4xl font-medium text-[#1e2e1a] mb-2">
                Restablecer contraseña
            </h2>
            <p class="text-sm md:text-base text-[#8a8e84]">
                Introduce tu nueva contraseña
            </p>
        </div>

        <!-- errores de validacion -->
        @if ($errors->any())
            <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- formulario -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <!-- email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Correo electrónico
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 md:py-3 text-sm md:text-base text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="tu@email.com" required autocomplete="username">
                </div>

                <!-- nueva contrasena -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Nueva contraseña
                    </label>

                    <div class="relative">
                        <input id="password" type="password" name="password"
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 md:py-3 pr-10 text-sm md:text-base text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            placeholder="Mínimo 8 caracteres, letras y números" required autocomplete="new-password">
                        <p class="text-xs text-[#8a8e84] mt-1.5">
                            Debe tener mínimo 8 caracteres, al menos una letra y un número.
                        </p>

                        <button type="button"
                            onclick="mostrarContra('password', 'ojo-reset1-abierto', 'ojo-reset1-cerrado')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#2d5a27] transition-colors duration-200">

                            <!-- ojo abierto -->
                            <svg id="ojo-reset1-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- ojo cerrado -->
                            <svg id="ojo-reset1-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- confirmar contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Confirmar nueva contraseña
                    </label>

                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 md:py-3 pr-10 text-sm md:text-base text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            placeholder="Repite la contraseña" required autocomplete="new-password">

                        <button type="button"
                            onclick="mostrarContra('password_confirmation', 'ojo-reset2-abierto', 'ojo-reset2-cerrado')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#2d5a27] transition-colors duration-200">

                            <!-- ojo abierto -->
                            <svg id="ojo-reset2-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>

                            <!-- ojo cerrado -->
                            <svg id="ojo-reset2-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- boton -->
                <button type="submit"
                    class="w-full bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm md:text-base font-medium py-3 rounded-xl transition-colors duration-200">
                    Restablecer contraseña
                </button>
            </form>
        </div>

        <!-- enlace login -->
        <p class="text-center text-sm text-[#8a8e84] mt-5">
            ¿Ya recuerdas tu contraseña?
            <a href="{{ route('login') }}"
                class="text-[#5a9e47] hover:text-[#2d5a27] font-medium transition-colors duration-200">
                Inicia sesión
            </a>
        </p>

    </div>
@endsection
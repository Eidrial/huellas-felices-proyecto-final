<section>

    <header class="mb-5">
        <p class="text-sm text-[#8a8e84] mt-1">
            Usa una contraseña segura y distinta a la anterior para proteger tu cuenta.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <!-- contraseña actual -->
        <div>
            <label for="current_password" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Contraseña actual
            </label>

            <div class="relative">

                <input id="current_password" name="current_password" type="password" required
                    autocomplete="current-password"
                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#3a7abf] focus:bg-white transition-colors duration-200">

                <button type="button"
                    onclick="mostrarContra('current_password', 'ojo-current-password-abierto', 'ojo-current-password-cerrado')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#1a4f8a] transition-colors duration-200">

                    <!-- ojo abierto -->
                    <svg id="ojo-current-password-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <!-- ojo cerrado -->
                    <svg id="ojo-current-password-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                    </svg>

                </button>

            </div>

            @error('current_password', 'updatePassword')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- nueva contraseña -->
        <div>

            <label for="password" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Nueva contraseña
            </label>

            <div class="relative">

                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#3a7abf] focus:bg-white transition-colors duration-200"
                    placeholder="Mínimo 8 caracteres, letras y números">

                <button type="button"
                    onclick="mostrarContra('password', 'ojo-password-abierto', 'ojo-password-cerrado')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#1a4f8a] transition-colors duration-200">

                    <!-- ojo abierto -->
                    <svg id="ojo-password-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <!-- ojo cerrado -->
                    <svg id="ojo-password-cerrado" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                    </svg>

                </button>

            </div>

            <p class="text-xs text-[#8a8e84] mt-1.5">
                Debe tener mínimo 8 caracteres, al menos una letra y un número.
            </p>

            @error('password', 'updatePassword')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- confirmar nueva contraseña -->
        <div>

            <label for="password_confirmation" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Confirmar nueva contraseña
            </label>

            <div class="relative">

                <input id="password_confirmation" name="password_confirmation" type="password" required
                    autocomplete="new-password"
                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#3a7abf] focus:bg-white transition-colors duration-200"
                    placeholder="Repite la nueva contraseña">

                <button type="button"
                    onclick="mostrarContra('password_confirmation', 'ojo-password-confirmation-abierto', 'ojo-password-confirmation-cerrado')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#1a4f8a] transition-colors duration-200">

                    <!-- ojo abierto -->
                    <svg id="ojo-password-confirmation-abierto" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <!-- ojo cerrado -->
                    <svg id="ojo-password-confirmation-cerrado" xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.965 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                    </svg>

                </button>

            </div>

            @error('password_confirmation', 'updatePassword')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- boton -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">

            <button type="submit"
                class="w-full sm:w-auto bg-[#3a7abf] hover:bg-[#1a4f8a] text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors duration-200">
                Actualizar contraseña
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-xs text-[#1a4f8a]">
                    Contraseña actualizada correctamente.
                </p>
            @endif

        </div>

    </form>

</section>
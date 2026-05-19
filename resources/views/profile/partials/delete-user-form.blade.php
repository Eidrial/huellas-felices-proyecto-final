<section>
    <header class="mb-5">
        <p class="text-sm text-[#8a8e84] mt-1">
            Esta acción es permanente. Al eliminar tu cuenta, también perderás el acceso a tus mascotas, estancias, avisos y facturas.
        </p>
    </header>

    <form id="form-eliminar-cuenta"
        method="post"
        action="{{ route('profile.destroy') }}"
        class="space-y-5">

        @csrf
        @method('delete')

        <div>
            <label for="password_delete" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Contraseña
            </label>

            <div class="relative">

                <input id="password_delete"
                    name="password"
                    type="password"
                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 pr-10 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#9b2a2a] focus:bg-white transition-colors duration-200"
                    placeholder="Introduce tu contraseña para confirmar">

                <button type="button"
                    onclick="mostrarContra('password_delete', 'ojo-delete-abierto', 'ojo-delete-cerrado')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8e84] hover:text-[#9b2a2a] transition-colors duration-200">

                    <!-- ojo abierto -->
                    <svg id="ojo-delete-abierto"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M2.036 12.322a1 1 0 010-.644C3.423 7.51 7.36 4.5 12 4.5s8.577 3.01 9.964 7.178a1 1 0 010 .644C20.577 16.49 16.64 19.5 12 19.5s-8.577-3.01-9.964-7.178z" />

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>

                    <!-- ojo cerrado -->
                    <svg id="ojo-delete-cerrado"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 hidden"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 3l18 18" />

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.878 5.091A9.953 9.953 0 0112 4.5c4.64 0 8.577 3.01 9.964 7.178a9.965 9.965 0 01-4.132 5.362M6.223 6.223A9.965 9.953 0 002.036 12.322C3.423 16.49 7.36 19.5 12 19.5c1.518 0 2.956-.323 4.25-.903" />
                    </svg>

                </button>

            </div>

            @error('password', 'userDeletion')
                <p class="text-xs text-[#9b2a2a] mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="button"
            id="btn-eliminar-cuenta"
            class="w-full sm:w-auto bg-[#9b2a2a] hover:bg-[#7a2020] text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors duration-200">
            Eliminar mi cuenta
        </button>

    </form>

</section>
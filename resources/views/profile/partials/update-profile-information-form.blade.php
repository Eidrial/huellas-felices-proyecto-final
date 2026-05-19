<section>

    <header class="mb-5">
        <p class="text-sm text-[#8a8e84] mt-1">
            Actualiza tus datos personales y de contacto. Algunos serán necesarios para poder realizar reservas.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- nombre -->
        <div>

            <label for="name" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Nombre
            </label>

            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required minlength="2"
                maxlength="255" autocomplete="given-name" placeholder="Tu nombre"
                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('name')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- apellidos -->
        <div>

            <label for="apellidos" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Apellidos
            </label>

            <input id="apellidos" name="apellidos" type="text" value="{{ old('apellidos', $user->apellidos) }}"
                maxlength="255" autocomplete="family-name" placeholder="Tus apellidos"
                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('apellidos')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- dni -->
        <div>

            <label for="dni" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                DNI / NIE
            </label>

            <input id="dni" name="dni" type="text" value="{{ old('dni', $user->dni) }}" maxlength="12"
                pattern="[0-9XYZxyz]{1}[0-9]{7}[A-Za-z]{1}" title="Introduce un DNI o NIE válido" autocomplete="off"
                spellcheck="false" placeholder="12345678A"
                class="w-full uppercase border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('dni')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- telefono -->
        <div>

            <label for="telefono" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Teléfono
            </label>

            <input id="telefono" name="telefono" type="tel" value="{{ old('telefono', $user->telefono) }}"
                maxlength="15" inputmode="numeric" pattern="[0-9+ ]{9,15}" title="Introduce un teléfono válido"
                autocomplete="tel" placeholder="600123123"
                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('telefono')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- direccion -->
        <div>

            <label for="direccion" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Dirección
            </label>

            <input id="direccion" name="direccion" type="text" value="{{ old('direccion', $user->direccion) }}"
                maxlength="255" autocomplete="street-address" placeholder="Calle, número, ciudad"
                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('direccion')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- email -->
        <div>

            <label for="email" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                Correo electrónico
            </label>

            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                maxlength="255" autocomplete="username" spellcheck="false" placeholder="correo@ejemplo.com"
                class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">

            @error('email')
                <p class="text-xs text-[#9b2a2a] mt-1.5">
                    {{ $message }}
                </p>
            @enderror

        </div>

        <!-- boton -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-1">

            <button type="submit"
                class="w-full sm:w-auto bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm font-medium px-5 py-2.5 rounded-xl transition-colors duration-200">
                Guardar cambios
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-xs text-[#2d5a27]">
                    Cambios guardados correctamente.
                </p>
            @endif

        </div>

    </form>

</section>
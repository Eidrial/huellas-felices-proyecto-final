@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8 md:py-10">

        <!-- cabecera -->
        <div class="mb-7">
            <a href="{{ route('mascotas.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> Volver al listado
            </a>

            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                Añadir mascota
            </h2>

            <p class="text-sm text-[#8a8e84]">
                Rellena los datos de tu nuevo compañero
            </p>
        </div>

        <!-- errores de validacion -->
        @if($errors->any())
            <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li> <!-- cada error que viene del backend -->
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- formulario -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">

            <form method="POST"
                action="{{ route('mascotas.store') }}"
                enctype="multipart/form-data"
                class="space-y-5">

                @csrf

                <!-- nombre -->
                <div>
                    <label for="nombre"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Nombre
                    </label>

                    <!-- old en valor es el campo no se borre en el caso de que haya errores al enviar el formulario,seguirá mostrando lo que envió -->
                    <input type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        maxlength="50"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="Nombre de tu mascota"
                        required>
                </div>

                <!-- especie -->
                <div>
                    <label for="especie"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Especie
                    </label>

                    <select id="especie"
                        name="especie"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        required>

                        <!-- para elegir la especie (solo disponible perro -->
                        <option value="" selected disabled>
                            Selecciona una especie
                        </option>

                        <option value="gato" disabled>
                            Gato (no disponible)
                        </option>

                        <option value="perro">
                            Perro
                        </option>

                    </select>
                </div>

                <!-- raza -->
                <div>
                    <label for="raza"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Raza
                    </label>

                    <input type="text"
                        id="raza"
                        name="raza"
                        value="{{ old('raza') }}"
                        maxlength="80"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="Ej: Husky"
                        required>
                </div>

                <!-- edad -->
                <div>
                    <label for="edad"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Edad (años)
                    </label>

                    <input type="number"
                        id="edad"
                        name="edad"
                        value="{{ old('edad') }}"
                        min="1"
                        max="25"
                        step="1"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="Ej: 2"
                        required>
                </div>

                <p class="text-xs text-[#8a8e84] mt-1">
                    Solo se admiten perros de 1 año o más.
                </p>

                <!-- peso -->
                <div>
                    <label for="peso"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Peso (kg)
                    </label>

                    <input type="number"
                        step="0.1"
                        id="peso"
                        name="peso"
                        value="{{ old('peso') }}"
                        min="0.5"
                        max="100"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="0.0"
                        required>
                </div>

                <!-- foto -->
                <div>
                    <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Foto <span class="text-[#8a8e84] font-normal">(opcional)</span>
                    </label>

                    <!-- input real oculto -->
                    <input type="file"
                        id="foto"
                        name="foto"
                        accept="image/*"
                        class="hidden">

                    <div class="flex flex-col gap-2">

                        <!-- boton -->
                        <label for="foto"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-[#d9ddd0] bg-[#fafaf8] text-sm text-[#1e2e1a] cursor-pointer hover:border-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200">
                            Seleccionar imagen
                        </label>

                        <!-- nombre archivo -->
                        <span id="nombre-archivo"
                            class="text-sm text-[#8a8e84] break-all">
                            Ningún archivo seleccionado
                        </span>

                    </div>
                </div>

                <!-- boton -->
                <button type="submit"
                    class="w-full bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm font-medium py-3 rounded-xl transition-colors duration-200">
                    Añadir mascota
                </button>

            </form>
        </div>

    </div>
@endsection
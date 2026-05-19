@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8 md:py-10">

        <!-- cabecera -->
        <div class="mb-7">
            <a href="{{ route('admin.mascotas.index') }}"
                class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                <span>←</span> Volver a mascotas
            </a>

            <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                Editar mascota
            </h2>

            <p class="text-sm text-[#8a8e84]">
                {{ $mascota->nombre }}
            </p>
        </div>

        <!-- errores de validacion -->
        @if($errors->any())
            <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- formulario -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">

            <form action="{{ route('admin.mascotas.actualizar', $mascota) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-5">

                @csrf
                @method('PUT')

                <!-- nombre -->
                <div>
                    <label for="nombre"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Nombre
                    </label>

                    <input type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $mascota->nombre) }}"
                        required
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">
                </div>

                <!-- especie -->
                <div>
                    <label for="especie"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Especie
                    </label>

                    <input type="text"
                        id="especie"
                        name="especie"
                        value="{{ old('especie', $mascota->especie) }}"
                        required
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">
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
                        value="{{ old('raza', $mascota->raza) }}"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">
                </div>

                <!-- edad y peso en fila -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- edad -->
                    <div>
                        <label for="edad"
                            class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                            Edad (años)
                        </label>

                        <input type="number"
                            id="edad"
                            name="edad"
                            value="{{ old('edad', $mascota->edad) }}"
                            min="0"
                            required
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">
                    </div>

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
                            value="{{ old('peso', $mascota->peso) }}"
                            min="0"
                            required
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200">
                    </div>

                </div>

                <!-- foto -->
                <div>

                    <label for="foto"
                        class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Foto <span class="text-[#8a8e84] font-normal">(opcional)</span>
                    </label>

                    <!-- si la mascota ya tiene foto -->
                    @if($mascota->foto)

                        <div class="flex items-center gap-4 mb-3 p-3 bg-[#f7f5f0] rounded-xl border border-[#d9ddd0]">

                            <!-- imagen actual -->
                            <img src="{{ asset('storage/' . $mascota->foto) }}"
                                alt="Foto de {{ $mascota->nombre }}"
                                class="w-16 h-16 object-cover rounded-xl border border-[#d9ddd0]">

                            <!-- texto -->
                            <div>
                                <p class="text-xs text-[#8a8e84]">
                                    Foto actual
                                </p>

                                <p class="text-sm text-[#1e2e1a] font-medium">
                                    {{ $mascota->nombre }}
                                </p>
                            </div>

                        </div>

                    @else

                        <!-- si no tiene foto -->
                        <div class="flex items-center gap-3 mb-3 p-3 bg-[#f7f5f0] rounded-xl border border-[#d9ddd0]">

                            <div
                                class="w-16 h-16 bg-[#eef5e8] border border-[#c8d9be] rounded-xl flex items-center justify-center text-2xl">
                                🐾
                            </div>

                            <p class="text-xs text-[#8a8e84]">
                                Sin foto todavía
                            </p>

                        </div>

                    @endif

                    <!-- input real oculto -->
                    <input type="file"
                        id="foto"
                        name="foto"
                        accept="image/*"
                        class="sr-only">

                    <div class="flex flex-col gap-2">

                        <!-- boton bonito -->
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

                <!-- botones -->
                <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 pt-1">

                    <!-- cancelar -->
                    <a href="{{ route('admin.mascotas.index') }}"
                        class="text-sm px-4 py-2.5 rounded-xl border border-[#d9ddd0] text-[#8a8e84] hover:bg-[#f7f5f0] transition-colors duration-200 text-center">
                        Cancelar
                    </a>

                    <!-- guardar -->
                    <button type="submit"
                        class="text-sm px-5 py-2.5 rounded-xl bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] font-medium transition-colors duration-200">
                        Guardar cambios
                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection
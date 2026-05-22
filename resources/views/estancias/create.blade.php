@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="max-w-xl mx-auto">

            <!-- cabecera -->
            <div class="mb-7">
                <a href="{{ route('estancias.index') }}"
                    class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
                    <span>←</span> Volver a mis estancias
                </a>

                <h2 class="font-serif text-3xl font-medium text-[#1e2e1a] mb-1">
                    Reservar estancia
                </h2>

                <p class="text-sm text-[#8a8e84]">
                    Elige mascota y fechas para tu reserva
                </p>
            </div>

            <!-- errores -->
            @if($errors->any())
                <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- info precios y condiciones -->
            <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-xl px-4 sm:px-5 py-4 mb-6">
                <p class="text-sm font-medium text-[#2d5a27] mb-2">
                    {{ number_format(config('residencia.precio_dia'), 2) }} €/día — (sin incluir posibles cuidados extra)
                </p>

                <ul class="space-y-1 text-xs text-[#3d5c38]">
                    <li>· Se paga el primer día al entregar el perro.</li>
                    <li>· Las reservas deben hacerse con al menos 1 día de antelación.</li>
                    <li>· No se permiten entradas ni salidas los domingos.</li>
                    <li>· Máximo {{ config('residencia.max_dias_estancia') }} días por estancia.</li>
                    <li>· Mínimo {{ config('residencia.min_dias_estancia') }} días por estancia.</li>
                    <li>· Máximo {{ config('residencia.max_estancias_por_mascota') }} reservas pendientes por mascota.</li>
                    <li>· Las estancias no pueden coincidir entre sí.</li>
                </ul>
            </div>

            <!-- formulario -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">
                <form method="POST" action="{{ route('estancias.store') }}" class="space-y-5">
                    @csrf

                    <!-- mascota -->
                    <div>
                        <label for="mascota_id" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                            Mascota
                        </label>

                        <select id="mascota_id" name="mascota_id"
                            class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            required>

                            <option value="" selected disabled hidden>
                                Selecciona una mascota
                            </option>

                            @if($mascotas->isEmpty())
                                <option value="" disabled>
                                    No tienes mascotas registradas
                                </option>
                            @else
                                @foreach($mascotas as $mascota)
                                    <option value="{{ $mascota->id }}" @selected(old('mascota_id') == $mascota->id)
                                        @if($mascota->aprobado === 0) disabled @endif>

                                        {{ $mascota->nombre }}

                                        ({{ $mascota->aprobado === 1 ? 'Aprobada' : ($mascota->aprobado === null ? 'Pendiente' : 'No aprobada') }})

                                    </option>
                                @endforeach
                            @endif

                        </select>

                        <p class="text-xs text-[#8a8e84] mt-1.5">
                            Si la mascota está pendiente, la reserva se guardará como
                            pendiente hasta que un administrador la apruebe.
                        </p>
                    </div>

                    <!-- fechas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label for="fecha_entrada" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Fecha de entrada
                            </label>

                            <input type="date" id="fecha_entrada" name="fecha_entrada" value="{{ old('fecha_entrada') }}"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="w-full cursor-pointer border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                required>
                        </div>

                        <div>
                            <label for="fecha_salida" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                Fecha de salida
                            </label>

                            <input type="date" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida') }}"
                                class="w-full cursor-pointer border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                required>

                            <p class="text-xs text-[#8a8e84] mt-1">
                                La salida no cuenta como día.
                            </p>
                        </div>

                    </div>

                    <!-- aviso disponibilidad -->
                    <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-xl px-4 sm:px-5 py-4 mb-6">
                        <p class="text-sm text-[#7a4e10] leading-relaxed">
                            La disponibilidad se comprobará al guardar la solicitud.
                            Si no hay plazas para esas fechas,
                            la estancia aparecerá como <strong>Sin disponibilidad</strong>
                            y podrás modificarla o cancelarla sin coste alguno.
                        </p>
                    </div>

                    <!-- medicacion (opcional) -->
                    <div class="border-t border-[#f0ede6] pt-5">

                        <p class="text-sm font-medium text-[#1e2e1a] mb-3">
                            Medicación
                            <span class="text-[#8a8e84] font-normal">(opcional)</span>
                        </p>

                        <div class="space-y-4">

                            <div>
                                <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                    Descripción
                                </label>

                                <input type="text" name="medicacion_descripcion" value="{{ old('medicacion_descripcion') }}"
                                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                    placeholder="Ej: Pastilla para alergia">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                                    Horas
                                    <span class="text-[#8a8e84] font-normal">
                                        (separadas por coma)
                                    </span>
                                </label>

                                <input type="text" name="medicacion_horas" value="{{ old('medicacion_horas') }}"
                                    class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                                    placeholder="Ej: 09:00,21:00">
                            </div>

                        </div>

                    </div>

                    <!-- boton -->
                    <button type="submit"
                        class="w-full bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm font-medium py-3 rounded-xl transition-colors duration-200">
                        Reservar
                    </button>

                </form>
            </div>

        </div>

    </div>

    <script>
        const entrada = document.getElementById('fecha_entrada');
        const salida = document.getElementById('fecha_salida');

        entrada.addEventListener('change', function () {
            salida.min = this.value;

            if (salida.value && salida.value < this.value) {
                salida.value = '';
            }
        });
    </script>

@endsection
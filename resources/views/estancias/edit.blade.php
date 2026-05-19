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
                    Editar estancia
                </h2>

                <p class="text-sm text-[#8a8e84]">
                    Modifica la fecha de salida de
                    {{ $estancia->mascota->nombre ?? 'tu mascota' }}
                </p>
            </div>

            <!-- errores -->
            @if ($errors->any())
                <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                    <ul class="space-y-1 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- resumen -->
            <div class="bg-[#eef5e8] border border-[#c8d9be] rounded-xl px-4 sm:px-5 py-4 mb-6">
                <p class="text-sm font-medium text-[#2d5a27] mb-2">
                    Resumen actual de la estancia
                </p>

                <ul class="space-y-1 text-xs text-[#3d5c38]">
                    <li>· Mascota: {{ $estancia->mascota->nombre ?? '—' }}</li>
                    <li>· Entrada: {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}</li>
                    <li>· Salida actual: {{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}</li>
                    <li>· Estado: {{ ucfirst($estancia->estado) }}</li>
                    <li>· Puedes acortar la estancia siempre.</li>
                    <li>· Para ampliarla, debe haber disponibilidad.</li>
                    <li>· No se permiten salidas en domingo.</li>
                </ul>
            </div>

            <!-- formulario -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">

                <form method="POST"
                    action="{{ route('estancias.update', $estancia) }}"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    <div>
                        <label for="fecha_salida"
                            class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                            Nueva fecha de salida
                        </label>

                        <input type="date"
                            id="fecha_salida"
                            name="fecha_salida"
                            value="{{ old('fecha_salida', $estancia->fecha_salida) }}"
                            min="{{ $estancia->fecha_entrada }}"
                            class="w-full cursor-pointer border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 text-sm text-[#1e2e1a] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                            required>

                        <p class="text-xs text-[#8a8e84] mt-1.5">
                            La fecha de salida no cuenta como día de estancia.
                        </p>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:items-center gap-3 pt-1">

                        <a href="{{ route('estancias.index') }}"
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

    </div>
@endsection
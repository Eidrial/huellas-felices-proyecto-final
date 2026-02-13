@extends('layouts.app')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 shadow rounded mt-6">
        <h2 class="text-2xl font-bold mb-4 text-center">Reservar estancia</h2>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-blue-50 text-blue-800 p-3 rounded mb-4">
            <p><strong>Precio por día:</strong> {{ number_format(config('residencia.precio_dia'), 2) }} €</p>
            <p class="text-sm">Se paga el primer día al entregar el perro.</p>
            <p class="text-sm">Las reservas deben hacerse con al menos 1 día de antelación.</p>
        </div>

        <form method="POST" action="{{ route('estancias.store') }}" class="bg-white shadow p-6 rounded">
            @csrf

            <div class="mb-4">
                <label class="block font-medium mb-1">Mascota</label>
                <select name="mascota_id" class="w-full border rounded p-2" required>
                    <option value="" selected disabled>Selecciona una mascota</option>
                    @foreach($mascotas as $mascota)
                        <option value="{{ $mascota->id }}" @selected(old('mascota_id') == $mascota->id) @if($mascota->aprobado == 0)
                        disabled @endif>
                            {{ $mascota->nombre }}
                            ({{ $mascota->aprobado == 1 ? 'Aprobada' : ($mascota->aprobado === null ? 'Pendiente' : 'No aprobada') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Si la mascota está pendiente de aprobación, la reserva se guardará como pendiente.
                    Cuando el administrador la apruebe, se confirmará automáticamente si hay plazas disponibles en las
                    fechas seleccionadas.
                </p>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Fecha de entrada</label>
                <input type="date" id="fecha_entrada" name="fecha_entrada" value="{{ old('fecha_entrada') }}"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block font-medium mb-1">Fecha de salida</label>
                <input type="date" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida') }}"
                    class="w-full border rounded p-2" required>
                <p class="text-xs text-gray-500 mt-1">
                    La fecha de salida no cuenta como día de estancia.
                </p>
            </div>

            <button type="submit" class="bg-green-600 text-black px-4 py-2 rounded hover:bg-green-700">
                Reservar
            </button>

            <a href="{{ route('estancias.index') }}"
                class="inline-block mt-2 bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
                Volver
            </a>
        </form>


        <script>
            const entrada = document.getElementById('fecha_entrada');
            const salida = document.getElementById('fecha_salida');

            entrada.addEventListener('change', function () {
                //la salida no puede ser anterior a la entrada
                salida.min = this.value;

                //si ya hay una fecha de salida invalida, se borra
                if (salida.value && salida.value < this.value) {
                    salida.value = '';
                }
            });
        </script>
    </div>
@endsection
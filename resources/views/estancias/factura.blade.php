@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto bg-white p-6 shadow rounded">
        <h2 class="text-2xl font-bold mb-6 text-center">
            Resumen de estancia
        </h2>

        <div class="mb-6">
            <p><strong>Mascota:</strong> {{ $estancia->mascota->nombre }}</p>
            <p><strong>Entrada:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}</p>
            <p><strong>Salida:</strong> {{ date('d/m/Y', strtotime($estancia->fecha_salida)) }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($estancia->estado) }}</p>
        </div>

        @if($estancia->esCancelacionUnDia())
            <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-6">
                <strong>Nota:</strong> Esta estancia se canceló el mismo día de entrada, por lo que se cobra 1 día de estancia.
            </div>
        @endif

        <h3 class="text-xl font-semibold mb-2 text-center">Factura</h3>

        <table class="w-full border text-center mb-6">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-2">Concepto</th>
                    <th class="p-2">Importe</th>
                </tr>
            </thead>

            <tbody>
                <tr class="border-t">
                    <!-- dias de estancia + precio -->
                    <td class="p-2">
                        {{ $estancia->diasFacturados() }} días x
                        {{ number_format($estancia->precio_dia, 2) }} €
                    </td>
                    <!-- precio total de esos dias -->
                    <td class="p-2">
                        {{ number_format($estancia->precio_total, 2) }} €
                    </td>
                </tr>

                <!-- comprobar si hay extras -->
                @if($estancia->cuidados->where('tipo', 'extra')->count() > 0)

                    <!-- recorrer y mostrar cada extra -->
                    @foreach($estancia->cuidados->where('tipo', 'extra') as $extra)
                        <tr class="border-t">
                            <!-- descripcion del cuidado extra -->
                            <td class="p-2">
                                Extra: {{ $extra->descripcion }}
                            </td>
                            <!-- precio del cudiado extra -->
                            <td class="p-2">
                                {{ number_format($extra->precio_extra, 2) }} €
                            </td>
                        </tr>
                    @endforeach

                @else

                    <!-- si no hay extras, mostrar mensaje informativo -->
                    <tr class="border-t">
                        <td class="p-2 text-gray-500" colspan="2">
                            No hay cuidados extras registrados.
                        </td>
                    </tr>

                @endif
            </tbody>

            <tr class="border-t font-bold bg-gray-100">
                <td class="p-2">TOTAL</td>
                <td class="p-2">
                    <!-- precio total con extras -->
                    {{ number_format($estancia->totalConExtras(), 2) }} €
                </td>
            </tr>

        </table>

        <a href="{{ route('estancias.index') }}"
            class="inline-block bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            Volver
        </a>
    </div>
@endsection
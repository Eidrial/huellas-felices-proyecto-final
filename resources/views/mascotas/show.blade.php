@extends('layouts.app')

@section('content')
   <div class="max-w-xl mx-auto bg-white p-6 shadow rounded mt-6">

      @if($mascota->foto)
         <img src="{{ asset('storage/' . $mascota->foto) }}" class="w-48 h-48 object-cover rounded mx-auto mb-4">
      @else
         <div class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded text-gray-500">
            🐾 <!-- no hay foto -->
         </div>
      @endif

      <h2 class="text-2xl font-bold mb-4 text-center">Información de {{ $mascota->nombre }}</h2>

      <p><strong>Nombre:</strong> {{ $mascota->nombre }}</p>
      <p><strong>Especie:</strong> {{ $mascota->especie }}</p>
      <p><strong>Raza:</strong> {{ $mascota->raza }}</p>
      <p><strong>Edad:</strong> {{ $mascota->edad }} años</p>
      <p><strong>Peso:</strong> {{ $mascota->peso }} kg</p>
      <p><strong>Estado:</strong>
         @if($mascota->aprobado === 1)
            Aprobada
         @elseif($mascota->aprobado === 0)
            No aprobada
         @else
            Pendiente
         @endif
      </p>

      <a href="{{ route('mascotas.edit', $mascota) }}"
         class="mt-4 inline-block bg-blue-600 text-blacke px-4 py-2 rounded hover:bg-blue-700">
         Editar mascota
      </a>

      <a href="{{ route('mascotas.index') }}"
         class="mt-2 inline-block bg-gray-600 text-black px-4 py-2 rounded hover:bg-gray-700">
         Volver al listado
      </a>
   </div>
@endsection
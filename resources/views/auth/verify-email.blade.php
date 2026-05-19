@extends('layouts.app')

@section('content')
    <div class="max-w-md md:max-w-xl mx-auto px-4 my-8 md:my-12">

        <!-- cabecera -->
        <div class="text-center mb-8">
            <h2 class="font-serif text-3xl md:text-4xl font-medium text-[#1e2e1a] mb-2">
                Verifica tu correo
            </h2>
            <p class="text-sm md:text-base text-[#8a8e84]">
                Antes de continuar, revisa tu correo electrónico y haz clic en el enlace de verificación que te hemos
                enviado.
            </p>
        </div>

        <!-- mensaje de estado -->
        @if (session('status') == 'verification-link-sent')
            <div
                class="mensaje-sesion bg-[#eef5e8] text-[#2d5a27] border border-[#c8d9be] text-sm p-3 md:p-4 mb-5 rounded-xl text-center">
                Hemos reenviado el enlace de verificación a tu correo electrónico.
            </div>
        @endif

        <!-- contenido -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7 text-center">

            <p class="text-sm md:text-base text-[#8a8e84] mb-6">
                Si no has recibido el correo, puedes solicitar que te enviemos otro.
            </p>

            <!-- reenviar -->
            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf

                <button type="submit"
                    class="w-full bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm md:text-base font-medium py-3 rounded-xl transition-colors duration-200">
                    Reenviar correo de verificación
                </button>
            </form>

            <!-- cerrar sesión -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="text-sm text-[#8a8e84] hover:text-[#1e2e1a] transition-colors duration-200">
                    Cerrar sesión
                </button>
            </form>

        </div>

    </div>
@endsection
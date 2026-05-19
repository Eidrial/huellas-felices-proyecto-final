@extends('layouts.app')

@section('content')
    <div class="max-w-md md:max-w-xl mx-auto px-4 my-8 md:my-12">

        <!-- cabecera -->
        <div class="text-center mb-8">
            <h2 class="font-serif text-3xl md:text-4xl font-medium text-[#1e2e1a] mb-2">
                Recuperar contraseña
            </h2>
            <p class="text-sm md:text-base text-[#8a8e84]">
                Indica tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>
        </div>

        <!-- mensaje de estado -->
        @if (session('status'))
            <div
                class="mensaje-sesion bg-[#eef5e8] text-[#2d5a27] border border-[#c8d9be] text-sm p-3 md:p-4 mb-5 rounded-xl text-center">
                Hemos enviado el enlace de recuperación a tu correo. Revisa también la carpeta de Spam.
            </div>
        @endif

        <!-- errores de validacion -->
        @if ($errors->any())
            <div class="bg-[#fceaea] text-[#9b2a2a] border border-[#e8b4b4] text-sm p-4 mb-5 rounded-xl">
                <ul class="space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- formulario -->
        <div class="bg-white border border-[#d9ddd0] rounded-2xl p-5 sm:p-7">
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <!-- email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#1e2e1a] mb-1.5">
                        Correo electrónico
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                        class="w-full border border-[#d9ddd0] bg-[#fafaf8] rounded-xl px-4 py-2.5 md:py-3 text-sm md:text-base text-[#1e2e1a] placeholder-[#b0b4aa] focus:outline-none focus:border-[#5a9e47] focus:bg-white transition-colors duration-200"
                        placeholder="tu@email.com" required autofocus autocomplete="username">
                </div>

                <!-- boton -->
                <button type="submit"
                    class="w-full bg-[#3a7a2e] hover:bg-[#2d5a27] text-[#f0ede6] text-sm md:text-base font-medium py-3 rounded-xl transition-colors duration-200">
                    Enviar enlace de recuperación
                </button>
            </form>
        </div>

        <!-- enlace login -->
        <p class="text-center text-sm text-[#8a8e84] mt-5">
            ¿Ya recuerdas tu contraseña?
            <a href="{{ route('login') }}"
                class="text-[#5a9e47] hover:text-[#2d5a27] font-medium transition-colors duration-200">
                Inicia sesión
            </a>
        </p>

    </div>
@endsection
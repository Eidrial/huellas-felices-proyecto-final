@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-8">

        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-1.5 text-sm text-[#5a9e47] hover:text-[#2d5a27] transition-colors duration-200 mb-4">
            <span>←</span> Volver
        </a>

        <!-- cabecera -->
        <div class="relative bg-[#2d5a27] rounded-2xl overflow-hidden mb-6 px-7 py-8">
            <div class="absolute right-6 top-4 text-[4rem] opacity-[0.07] select-none leading-none">🐾</div>

            <p class="text-xs uppercase tracking-[0.2em] text-[#9fcf8e] font-medium mb-2">
                Tu cuenta
            </p>

            <h2 class="font-serif text-3xl font-medium text-[#f0ede6]">
                Mi perfil
            </h2>

            <p class="text-[#9fcf8e] text-sm mt-1">
                Actualiza tus datos personales y tu contraseña
            </p>
        </div>

        @if (!auth()->user()->hasVerifiedEmail())
            <div class="bg-[#fef8ec] border border-[#e4c57a] rounded-xl px-5 py-4 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <p class="text-sm text-[#7a4e10]">
                        Tu correo electrónico aún no está verificado.
                    </p>

                    <button type="button" id="btn-reenviar-verificacion"
                        class="w-full sm:w-auto text-xs px-3 py-1.5 rounded-lg border border-[#e4c57a] text-[#7a4e10] hover:bg-[#fff4d8] transition-colors duration-200">
                        Reenviar correo de verificación
                    </button>

                </div>
            </div>
        @endif

        <!-- form oculto para reenviar verificación -->
        <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-stretch">

            <!-- datos usuario -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                <div class="h-[3px] bg-[#5a9e47]"></div>

                <div class="p-5 sm:p-6 flex-1">

                    <h3 class="font-medium text-[#1e2e1a] mb-4">
                        Datos personales
                    </h3>

                    @include('profile.partials.update-profile-information-form')

                </div>

            </div>

            <!-- contraseña -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden h-full flex flex-col">

                <div class="h-[3px] bg-[#3a7abf]"></div>

                <div class="p-5 sm:p-6 flex-1">

                    <h3 class="font-medium text-[#1e2e1a] mb-4">
                        Cambiar contraseña
                    </h3>

                    @include('profile.partials.update-password-form')

                </div>

            </div>

            <!-- eliminar cuenta -->
            <div class="bg-white border border-[#d9ddd0] rounded-2xl overflow-hidden xl:col-span-2">

                <div class="h-[3px] bg-[#9b2a2a]"></div>

                <div class="p-5 sm:p-6">

                    <h3 class="font-medium text-[#9b2a2a] mb-4">
                        Eliminar cuenta
                    </h3>

                    @include('profile.partials.delete-user-form')

                </div>

            </div>

        </div>

    </div>
    
@endsection
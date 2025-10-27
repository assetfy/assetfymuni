<x-guest-layout :title="'Registro - Assetfy'">

    <div class="min-h-screen flex items-center justify-center">

        <div class="w-full max-w-sm mx-auto lg:w-84">
            <a href="{{ url('/') }}">
                <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" class="title" style="height: 4rem;">
            </a>

            <div class="w-full max-w-sm mx-auto lg:w-84 flex-col items-center">
                @if (session('status') == 'verification-link-sent')
                <p class="mb-1 font-medium text-sm text-red-600 mt-2 text-center">
                    Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                </p>
                @else
                <p class="mt-2 text-center text-sm leading-5 text-black max-w" style="padding-top: 10px;">
                    Antes de continuar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo electrónico, con gusto te enviaremos otro.
                </p>
                @endif

                @if ($errors->any())
                <div class="mb-4">
                    <ul class="list-inside text-sm text-red-600 text-center">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md lg:max-w-md">

                <form class="space-y-6 text-center" method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <div class="space-y-1 relative">

                        <div class="mt-3"></div>
                        <x-button type="submit" class="w-full justify-center custom-button py-2 px-4">
                            Reenviar Correo de Verificación
                        </x-button>
                    </div>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center pt-2">
                    @csrf

                    <div class="mt-6 text-center">
                        <button type="submit" class="font-medium text-black hover:text-gray-600" style="font-weight: bold">
                            {{ __('Volver a activos') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>

        <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">

</x-guest-layout>
<!-- resources/views/auth/forgot-password.blade.php -->
<x-guest-layout :title="'Recuperación de Contraseña - Assetfy'">
    <div class="min-h-screen flex items-center justify-center">

        <div class="w-full max-w-sm mx-auto lg:w-84">

            <div class="w-full max-w-sm mx-auto lg:w-84 flex flex-col items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" style="height: 4rem;">
                </a>
                <h2 class="mt-6 text-5xl font-bold text-gray-900 text-center" style="color: #4e7ca0;">
                    Restablece tu contraseña
                </h2>
                <div class="mt-4"></div>
                <h3 class="text-center text-1xl">
                    Introduce tu dirección de correo electrónico.<br>Te enviaremos un correo electrónico con instrucciones para restablecer tu contraseña.
                </h3>
                @if (session('status'))
                <div class="mb-1 font-medium text-sm text-red-600 mt-2 text-center">
                    {{ session('status') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-4 mt-2">
                    <ul class="list-inside text-sm text-red-600 text-center">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md lg:max-w-md">
                <form class="" method="POST" action="{{ route('password.email') }}" class="mt-3 space-y-3">
                    @csrf
                    <div class="space-y-1 relative">
                        <label for="email" style="color: #4e7ca0; font-weight: bold">
                            Correo electrónico
                        </label>
                        <input id="email" class="text-md block px-3 py-2 rounded-lg w-full 
                            bg-white border-2 border-gray-300 placeholder-gray-600 
                            focus:placeholder-gray-500
                            focus:border-gray-600  
                            focus:outline-none focus:shadow-lg" name="email" type="email" autocomplete="email" required autofocus />
                    </div>

                    <div class="space-y-1 relative">
                        <div class="mt-3"></div>

                        <x-button type="submit" class="w-full justify-center custom-button py-2 px-4">
                            Enviar enlace de reseteo
                        </x-button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ url('/') }}" class="font-medium text-black hover:text-gray-600" style="font-weight: bold">
                        Volver a la página de inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">

</x-guest-layout>
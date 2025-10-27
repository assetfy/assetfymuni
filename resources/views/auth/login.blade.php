{{-- resources/views/auth/login.blade.php --}}
@php
    // Fuerza a Laravel a marcar la sesión como "modificada"
    session()->put('__session_initialized', true);
@endphp

<x-guest-layout :title="'Iniciar Sesión - Assetfy'">
    <div class="min-h-screen flex items-center justify-center">
        <div class="w-full max-w-sm mx-auto lg:w-84">
            <div class="w-full max-w-sm mx-auto lg:w-84 flex flex-col items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/assetfy-inv.png') }}" alt="Logo" style="height: 4rem;">
                </a>
                <h2 class="mt-6 text-5xl font-bold text-gray-900 text-center" style="color: #4e7ca0;">
                    Iniciar Sesión
                </h2>
                <div class="mt-4"></div>
                <h3 class="text-center text-1xl">
                    Coloca tu usuario y contraseña para iniciar sesión
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

            <div class="mt-2">
                <form method="POST" action="{{ route('login') }}" class="mt-3 space-y-3">
                    @csrf

                    <div class="space-y-1 relative">
                        <label for="email" style="color: #4e7ca0; font-weight: bold">
                            Correo Electrónico
                        </label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus
                            placeholder="name@example.com"
                            class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600
                                   focus:placeholder-gray-500 focus:border-gray-600 focus:outline-none focus:shadow-lg" />
                    </div>

                    <div class="space-y-1 relative" x-data="{ show: true }">
                        <label for="password" style="color: #4e7ca0; font-weight: bold">
                            Contraseña
                        </label>
                        <div class="relative">
                            <input placeholder="************" :type="show ? 'password' : 'text'" name="password"
                                required autocomplete="current-password"
                                class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600
                                       focus:placeholder-gray-500 focus:border-gray-600 focus:outline-none focus:shadow-lg" />
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                {{-- Ojo: estos SVGs intercambian la visibilidad con Alpine --}}
                                <svg class="h-6 text-gray-700" fill="none" @click="show = !show"
                                    :class="{ 'block': !show, 'hidden': show }" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 576 512">
                                    <path fill="currentColor" d="M572.52 241.4C518.29 135.59…"></path>
                                </svg>
                                <svg class="h-6 text-gray-700" fill="none" @click="show = !show"
                                    :class="{ 'hidden': !show, 'block': show }" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512">
                                    <path fill="currentColor" d="M320 400c-75.85 0-137.25-…"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <x-checkbox id="remember_me" name="remember" />
                            <label for="remember_me" class="block ml-2 text-sm text-gray-900">
                                {{ __('Recordar') }}
                            </label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm font-medium text-black hover:text-gray-600" style="font-weight: bold">
                                {{ __('Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    <div>
                        <x-button class="w-full justify-center custom-button">
                            {{ __('Iniciar Sesión') }}
                        </x-button>
                    </div>
                </form>

                <p class="mt-2 text-center text-sm text-gray-900">
                    ¿No tienes una cuenta?
                    <a href="{{ route('register') }}" class="font-medium text-black hover:text-gray-600"
                        style="font-weight: bold">
                        Regístrate
                    </a>
                </p>
            </div>
        </div>
    </div>

    {{-- Si usas Livewire en esta página: --}}
    @livewireScripts

    <link rel="stylesheet" href="{{ asset('css/login.css') }}" type="text/css">
</x-guest-layout>

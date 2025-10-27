<!-- resources/views/auth/forgot-password.blade.php -->
<x-guest-layout :title="'Resetear Contraseña - Assetfy'">
    <div class="min-h-screen flex items-center justify-center">

        <div class="w-full max-w-sm mx-auto lg:w-84">

        <div class="w-full max-w-sm mx-auto lg:w-84 flex flex-col items-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" style="height: 4rem;">
                </a>
                <h2 class="mt-6 text-5xl font-bold text-gray-900 text-center" style="color: #4e7ca0;">
                    Introduce la nueva contraseña
                </h2>
                <div class="mt-4"></div>
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

            <div class="mt-4">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}">

                    <div>
                        <label for="email" style="color: #4e7ca0; font-weight: bold">
                            Correo electrónico
                        </label>
                        <div class="mt-1">
                            <input id="email" class="text-md block px-3 py-2 rounded-lg w-full 
            bg-gray-200 border-2 border-red-500 placeholder-gray-600 
            cursor-not-allowed focus:border-red-500 hover:border-red-500" type="email" name="email" value="{{ request()->email }}" readonly />
                        </div>
                    </div>

                    <div x-data="{ password: '', confirmPassword: '', showPassword: true, showConfirmPassword: true, passwordsMatch: true }" style="padding-top: 10px;">
                        <div class="space-y-1 relative">
                            <label for="password" style="color: #4e7ca0; font-weight: bold" value="{{ __('Contraseña') }}">Contraseña</label>
                            <div class="relative">
                                <input placeholder="************" :type="showPassword ? 'password' : 'text'" class="text-md block px-3 py-2 rounded-lg w-full 
                            bg-white border-2 border-gray-300 placeholder-gray-600 
                            focus:placeholder-gray-500
                            focus:border-gray-600  
                            focus:outline-none focus:shadow-lg" id="password" name="password" required autocomplete="new-password" x-model="password" @input="passwordsMatch = password === confirmPassword">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                    <svg class="h-6 text-gray-700" fill="none" @click="showPassword = !showPassword" :class="{ 'block': !showPassword, 'hidden': showPassword }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                        <path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path>
                                    </svg>
                                    <svg class="h-6 text-gray-700" fill="none" @click="showPassword = !showPassword" :class="{ 'hidden': !showPassword, 'block': showPassword }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                        <path fill="currentColor" d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1 relative" style="padding-top: 10px;">
                            <label for="password_confirmation" style="color: #4e7ca0; font-weight: bold" value="{{ __('Confirmar Contraseña') }}">Confirmar Contraseña</label>
                            <div class="relative">
                                <input placeholder="************" :type="showConfirmPassword ? 'password' : 'text'" class="text-md block px-3 py-2 rounded-lg w-full 
                            bg-white border-2 border-gray-300 placeholder-gray-600 
                            focus:placeholder-gray-500
                            focus:border-gray-600  
                            focus:outline-none focus:shadow-lg" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" x-model="confirmPassword" @input="passwordsMatch = password === confirmPassword">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                    <svg class="h-6 text-gray-700" fill="none" @click="showConfirmPassword = !showConfirmPassword" :class="{ 'block': !showConfirmPassword, 'hidden': showConfirmPassword }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                        <path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path>
                                    </svg>
                                    <svg class="h-6 text-gray-700" fill="none" @click="showConfirmPassword = !showConfirmPassword" :class="{ 'hidden': !showConfirmPassword, 'block': showConfirmPassword }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                        <path fill="currentColor" d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div x-show="!passwordsMatch" class="text-red-500 mt-2">Las contraseñas no coinciden.</div>
                    </div>

                    <div style="padding-top: 15px;">
                        <x-button type="submit" class="w-full justify-center custom-button py-2 px-4">
                            Cambiar Contraseña
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">

</x-guest-layout>
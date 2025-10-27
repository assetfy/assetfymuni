<x-guest-layout :title="'Registro - Assetfy'">
    <div class="flex min-h-screen bg-white">
        <a href="{{ url('/') }}">
            <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" class="title" style="height: 4rem;">
        </a>
        <div class="hidden lg:block relative w-0 flex-1 lg:w-1/2">
            <div id="default-carousel" class="relative w-full h-full" data-carousel="slide">
                <div class="relative h-full overflow-hidden">
                    <!-- Item 1 -->
                    <div class="duration-700 ease-in-out" data-carousel-item="active">
                        <img id="carousel-image" src="{{ asset('storage/fotos/3.png') }}"
                            class="absolute inset-0 w-full h-full object-cover" alt="Imagen 1">
                    </div>
                    <!-- Item 2 -->
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img id="carousel-image" src="{{ asset('storage/fotos/2.png') }}"
                            class="absolute inset-0 w-full h-full object-cover" alt="Imagen 2">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img id="carousel-image" src="{{ asset('storage/fotos/1.png') }}"
                            class="absolute inset-0 w-full h-full object-cover" alt="Imagen 3">
                    </div>
                    <div class="hidden duration-700 ease-in-out" data-carousel-item>
                        <img id="carousel-image" src="{{ asset('storage/fotos/4.png') }}"
                            class="absolute inset-0 w-full h-full object-cover" alt="Imagen 4">
                    </div>
                </div>
                <div class="absolute z-30 flex space-x-3 bottom-5 left-1/2 transform -translate-x-1/2">
                    <button type="button" class="w-5 h-5 rounded-full bg-black rounded-button" aria-current="true"
                        aria-label="Slide 1" data-carousel-slide-to="0"></button>
                    <button type="button" class="w-5 h-5 rounded-full bg-black rounded-button" aria-current="false"
                        aria-label="Slide 2" data-carousel-slide-to="1"></button>
                    <button type="button" class="w-5 h-5 rounded-full bg-black rounded-button" aria-current="false"
                        aria-label="Slide 3" data-carousel-slide-to="2"></button>
                    <button type="button" class="w-5 h-5 rounded-full bg-black rounded-button" aria-current="false"
                        aria-label="Slide 4" data-carousel-slide-to="3"></button>
                </div>

            </div>
        </div>

        <div
            class="flex flex-col justify-center items-center flex-1 px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 lg:w-1/2">
            <div class="w-full max-w-sm mx-auto lg:w-84">
                <div class="w-full max-w-sm mx-auto lg:w-84 flex flex-col items-center">
                    <h2 class="mt-6 text-5xl font-extrabold text-gray-900">
                        Crea una cuenta
                    </h2>
                    <div class="mt-4"></div>
                    <h3 class="text-center text-1xl">
                        Ingresa tus datos y empieza a disfrutar de lo simple
                    </h3>
                </div>
                <div class="container" style="padding-top: 10px;">
                    <div class="crear-cuenta">
                        <a href="{{ route('login') }}" class="crear-cuenta-link"
                            style="padding-left: 50px;"><strong>Iniciar Sesión</strong></a>
                        <button class="iniciar-sesion"><a
                                href="{{ route('register') }}"><strong>Registrarse</strong></a></button>
                    </div>
                </div>
                <div class="mt-4">

                    <x-validation-errors class="mb-4" />

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div>
                            <x-label for="apellido" value="{{ __('Apellido') }}" />
                            <x-input id="apellido" name="apellido" type="text" required autocomplete="family-name"
                                class="text-md block px-3 py-2 rounded-lg w-full 
                                            bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                                            focus:placeholder-gray-500 focus:bg-white focus:border-gray-600  
                                            focus:outline-none focus:shadow-lg" />
                        </div>

                        <div class="mt-4">
                            <x-label for="nombre" value="{{ __('Nombre') }}" />
                            <x-input id="nombre" name="nombre" type="text" required autocomplete="given-name"
                                class="text-md block px-3 py-2 rounded-lg w-full 
                                            bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                                            focus:placeholder-gray-500 focus:bg-white focus:border-gray-600  
                                            focus:outline-none focus:shadow-lg" />
                        </div>


                        <div style="padding-top: 10px;">
                            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                            <x-input id="email"
                                class="text-md block px-3 py-2 rounded-lg w-full 
                           bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
                            focus:placeholder-gray-500
                             focus:bg-white 
                            focus:border-gray-600  
                                focus:outline-none focus:shadow-lg"
                                type="email" name="email" :value="old('email')" required autofocus
                                placeholder="name@example.com" />
                        </div>

                        <div style="padding-top: 10px;" x-data="{ cuil: '', errorCuil: '' }">
                            <x-label for="cuil" value="{{ __('CUIL') }}" />
                            <x-input id="cuil"
                                class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500 focus:bg-white focus:border-gray-600 focus:outline-none focus:shadow-lg"
                                type="password" name="cuil" x-model="cuil"
                                @input="if(cuil.length !== 11) { errorCuil = 'El CUIL debe tener 11 dígitos'; } else { errorCuil = ''; }"
                                required autocomplete="cuil" placeholder="xx-xxxxxxxxxx-xx"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)" />
                            <p x-text="errorCuil" class="text-red-500 text-sm mt-2"></p>
                        </div>


                        <div x-data="{ password: '', confirmPassword: '', showPassword: true, showConfirmPassword: true, passwordsMatch: true }" style="padding-top: 10px;">
                            <div class="space-y-1 relative">
                                <label for="password" value="{{ __('Contraseña') }}">Contraseña</label>
                                <div class="relative">
                                    <input placeholder="************" :type="showPassword ? 'password' : 'text'"
                                        class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500
                                        focus:bg-white focus:border-gray-600 focus:outline-none"
                                        id="password" name="password" required autocomplete="new-password"
                                        x-model="password" @input="passwordsMatch = password === confirmPassword">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                        <svg class="h-6 text-gray-700" fill="none"
                                            @click="showPassword = !showPassword"
                                            :class="{ 'block': !showPassword, 'hidden': showPassword }"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                            <path fill="currentColor"
                                                d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z">
                                            </path>
                                        </svg>
                                        <svg class="h-6 text-gray-700" fill="none"
                                            @click="showPassword = !showPassword"
                                            :class="{ 'hidden': !showPassword, 'block': showPassword }"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                            <path fill="currentColor"
                                                d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1 relative" style="padding-top: 10px;">
                                <label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}">Confirmar
                                    Contraseña</label>
                                <div class="relative">
                                    <input placeholder="************" :type="showConfirmPassword ? 'password' : 'text'"
                                        class="text-md block px-3 py-2 rounded-lg w-full bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md focus:placeholder-gray-500
                                        focus:bg-white focus:border-gray-600 focus:outline-none"
                                        id="password_confirmation" name="password_confirmation" required
                                        autocomplete="new-password" x-model="confirmPassword"
                                        @input="passwordsMatch = password === confirmPassword">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                                        <svg class="h-6 text-gray-700" fill="none"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            :class="{ 'block': !showConfirmPassword, 'hidden': showConfirmPassword }"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                            <path fill="currentColor"
                                                d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z">
                                            </path>
                                        </svg>
                                        <svg class="h-6 text-gray-700" fill="none"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            :class="{ 'hidden': !showConfirmPassword, 'block': showConfirmPassword }"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                            <path fill="currentColor"
                                                d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.07 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144.13 144.13 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zm-183.72-142l-39.3-30.38A94.75 94.75 0 0 0 416 256a94.76 94.76 0 0 0-121.31-92.21A47.65 47.65 0 0 1 304 192a46.64 46.64 0 0 1-1.54 10l-73.61-56.89A142.31 142.31 0 0 1 320 112a143.92 143.92 0 0 1 144 144c0 21.63-5.29 41.79-13.9 60.11z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div x-show="!passwordsMatch" class="text-red-500 mt-2">Las contraseñas no coinciden.
                            </div>
                        </div>

                        <!-- Nuevo Campo: Tipo de Usuario -->
                        <div style="padding-top: 10px;">
                            <x-label for="user_type" value="{{ __('Tipo de Usuario') }}" />
                            <select id="user_type" name="user_type"
                                class="text-md block px-3 py-2 rounded-lg w-full 
            bg-white border-2 border-gray-300 placeholder-gray-600 shadow-md
            focus:placeholder-gray-500
            focus:bg-white 
            focus:border-gray-600  
            focus:outline-none focus:shadow-lg">
                                <option value="" selected disabled>Seleccione el tipo de usuario</option>
                                <option value="normal">Usuario Individual</option>
                                <option value="empresa">Usuario de Empresa</option>
                                <option value="proveedora">Usuario Proveedor</option>
                            </select>
                            @error('user_type')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                            <div class="mt-4">
                                <x-label for="terms">
                                    <div class="flex items-center">
                                        <x-checkbox name="terms" id="terms" required />

                                        <div class="ms-2">
                                            {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                                'terms_of_service' =>
                                                    '<a target="_blank" href="' .
                                                    route('terms.show') .
                                                    '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                    __('Términos de Servicio') .
                                                    '</a>',
                                                'privacy_policy' =>
                                                    '<a target="_blank" href="' .
                                                    route('policy.show') .
                                                    '" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">' .
                                                    __('Política de Privacidad') .
                                                    '</a>',
                                            ]) !!}
                                        </div>
                                    </div>
                                </x-label>
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('login') }}">
                                {{ __('¿Ya te encuentras registrado?') }}
                            </a>

                            <x-button class="ms-6 justify-center custom-button">
                                {{ __('siguiente') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script src="{{ asset('js/login.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
</x-guest-layout>

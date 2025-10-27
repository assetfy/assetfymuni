<nav class="fixed w-full z-10 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center lg:hidden">
                <button aria-label="Toggle sidebar" @click="sidebarOpen = !sidebarOpen"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    aria-expanded="false">
                    <span class="sr-only">Toggle sidebar</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <!-- <div wire:click="crearActivos" class="flex items-center space-x-2 ml-[9rem] cursor-pointer mt-2 sm:mt-4">
                <button
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full h-8 w-8 flex items-center justify-center text-2xl">
                    +
                </button>
                <p class="text-sm text-gray-700 select-none">Crear BIENES</p>
            </div> -->
            <!-- <div class="flex items-center cursor-pointer mt-2 sm:mt-4">
                <a href="{{ route('servicios-vista-formulario') }}" class="flex items-center space-x-2">
                    <i
                        class="fa-solid fa-file-invoice text-blue-500 hover:text-blue-700 font-bold flex items-center justify-center text-2xl"></i>
                    <span>Generar Cotización/Servicio</span>
                </a>
            </div> -->
            <!--Logo de la empresa -->
            {{--
                @if ($this->logo->logo)
                    <div class="flex items-center justify-center w-full sm:justify-start sm:w-auto">
                        <!-- Logo agregado -->
                        <a href="{{ route('dashboard-empresa') }}" class="ajax-link"
            @click.prevent="currentPage = 'dashboard'">
            </a>
        </div>
        @else
        <!-- Si no hay logo, mostramos el nombre de la empresa -->
        <div class="flex items-center justify-center w-full sm:justify-start sm:w-auto">
            <span class="text-xl font-semibold text-black">
                @php
                $words = explode(' ', $this->logo->razon_social);
                $initials = '';
                if (count($words) > 1) {
                $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                } else {
                $initials = strtoupper(substr($words[0], 0, 2));
                }
                @endphp
                {{ $initials }}
            </span>
        </div>
        @endif
        --}}
        <div class="hidden lg:flex lg:items-center lg:space-x-4"> </div>

        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 text-gray-700">
                <!-- Texto del perfil -->
                @if ($panelActual == 'Usuario')
                <span class="text-sm font-medium text-gray-600">
                    {{ $panelActual == 'Usuario' ? 'Perfil: Usuario' : 'Perfil: Empresa' }}
                </span>
                @else
                <span class="text-sm font-medium text-gray-600">
                    {{ $nombreEmpresa }}
                </span>
                @endif
                <!-- Ícono dinámico según el perfil -->
                @if ($panelActual == 'Usuario')
                <!-- Ícono para Usuario -->
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12zm0 1.5c-2.7 0-8.1 1.351-8.1 4.05v1.2a.75.75 0 00.75.75h14.7a.75.75 0 00.75-.75v-1.2c0-2.699-5.4-4.05-8.1-4.05z" />
                </svg>
                @else
                <!-- Ícono para Empresa -->
                <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M3 21v-9a1 1 0 011-1h3V4a1 1 0 011-1h8a1 1 0 011 1v7h3a1 1 0 011 1v9h-6v-3H9v3H3zm6-3h6v3h-6v-3z" />
                </svg>
                @endif
            </div>
            @if ($panelActual !== 'Estado')
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="relative inline-flex items-center px-2 py-1 border-transparent text-sm font-medium rounded-full text-red-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">

                    <i class="far fa-bell fa-1x text-red-600"></i>

                    @if ($notificaciones->count() > 0)
                    <span
                        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full z-10">
                        {{ $notificaciones->count() }}
                    </span>
                    @endif
                </button>

                <div x-show="open" x-cloak @click.away="open = false"
                    class="origin-top-right absolute right-0 mt-2 w-80 rounded-md ring-1 ring-black ring-opacity-5 bg-white shadow-lg z-50">
                    <div class="py-2 max-h-96 overflow-y-auto" role="menu" aria-orientation="vertical"
                        aria-labelledby="options-menu">
                        @forelse ($notificaciones as $notificacion)
                        <button wire:click="verNotificacion({{ $notificacion->id_notificacion }})"
                            class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            {{ $notificacion->descripcion }}
                        </button>
                        @empty
                        <span class="block px-4 py-2 text-sm text-gray-500">Sin notificaciones nuevas</span>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            <div class="flex items-center space-x-2">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="bg-white rounded-full flex items-center justify-center w-8 h-8 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Open user menu</span>
                        @php
                        $photoKey = auth()->user()->profile_photo_path;
                        $photoUrl = $photoKey
                        ? Storage::disk('s3')->temporaryUrl($photoKey, now()->addMinutes(10))
                        : auth()->user()->profile_photo_url;
                        @endphp
                        <img src="{{ $photoUrl }}" alt="{{ auth()->user()->name }}"
                            class="w-full h-full rounded-full object-cover" />
                    </button>

                    <div x-show="open" x-cloak @click.away="open = false"
                        class="bg-white origin-top-right absolute right-0 mt-2 w-48 rounded-md ring-1 ring-black ring-opacity-5"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        <a href="{{ route('usuarios-perfiles') }}"
                            class="ajax-link block px-4 py-2 text-sm text-gray-700"
                            data-url="{{ route('usuarios-perfiles') }}"
                            @click.prevent="currentPage = 'usuarios-perfiles'">Perfil</a>
                        <div wire:ignore>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700">
                                    Desconectarse
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <span class="text-sm font-bold text-black">
                    @php
                    $nombreCompleto = auth()->user()->name;
                    $partes = explode(' ', $nombreCompleto);
                    $iniciales = '';
                    if (count($partes) >= 2) {
                    $iniciales = strtoupper(substr($partes[0], 0, 1) . substr($partes[1], 0, 1));
                    } else {
                    $iniciales = strtoupper(substr($nombreCompleto, 0, 2));
                    }
                    @endphp
                    {{ $iniciales }}
                </span>
            </div>
        </div>
    </div>
</nav>
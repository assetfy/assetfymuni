<nav class="fixed w-full z-10 flex justify-between items-center bg-white mt-2 sm:mt-4 lg:mt-0">
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
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full h-8 w-8 flex items-center justify-center text-2xl">
                    +
                </button>
                <p class="text-sm text-gray-700 select-none">Crear BIENES</p>
            </div>
            <div class="flex items-center cursor-pointer mt-2 sm:mt-4">
                <a href="{{ route('servicios-vista-formulario') }}"
                    class="ajax-link flex items-center space-x-2">
                    <i class="fa-solid fa-file-invoice text-blue-500 hover:text-blue-700 font-bold flex items-center justify-center text-2xl"></i>
                    <span>Generar Cotización/Servicio</span>
                </a>
            </div> -->
            @if (count($calificaciones) > 0 || count($calificacionesRealizadas) > 0)
            <div class="flex items-center cursor-pointer mt-2 sm:mt-4">
                <a href="{{ url('/calificacion-general?filters[estado_presupuesto][0]=Servicio+Realizado,+Solicitud+Cerrada') }}"
                    class="ajax-link flex items-center space-x-2">
                    <i class="fas fa-pencil text-blue-500 hover:text-blue-700 font-bold flex items-center justify-center text-2xl"></i>
                    <span>Recomendaciones</span>
                </a>
            </div>
            @endif

            <div class="flex items-center space-x-4 mt-2 sm:mt-4">
                <div class="flex items-center justify-center w-full sm:justify-start sm:w-auto">
                    <!-- Logo agregado -->
                    <a href="{{ route('dashboard-empresa') }}" class="ajax-link"
                        @click.prevent="currentPage = 'dashboard'">
                    </a>
                </div>
                <div class="hidden lg:block">
                    <!-- Navigation links -->
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 text-gray-700">
                    <!-- Texto del perfil -->
                    <span class="text-sm font-medium text-gray-600">
                        {{ $panelActual == 'Usuario' ? 'Perfil: Usuario' : 'Perfil: Empresa' }}
                    </span>
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
                <!-- Dropdown de Notificaciones -->
                <div wire:poll.30s="refreshData" wire:key="notification-dropdown" class="relative"
                    x-data="{ open: false }">
                    <button @click="open = !open"
                        class="relative inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-full text-gray-800 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="far fa-bell fa-1x text-gray-800"></i>
                        @if ($notificacionesCount > 0)
                        <span
                            class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-400"></span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md bg-white ring-1 ring-black ring-opacity-5"
                        role="menu" aria-orientation="vertical" aria-labelledby="notificacionesDropdown">
                        @if ($notificaciones->count() > 0)
                        @foreach ($notificaciones as $notificacion)
                        @if(!empty($empresa))
                            @foreach ($empresa as $lista)
                                @if ($notificacion->cuit == $lista->cuit)
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700">{{ $lista->razon_social }} te
                                    invita a unirte</a>
                                @endif
                            @endforeach
                        @endif
                        @endforeach
                        @endif
                        @if ($solicitudes->count() > 0)
                        <a href="{{ route('usuarios-servicios') }}"
                            class="ajax-link block px-4 py-2 text-sm text-gray-700">
                            Tienes {{ $solicitudes->count() }} solicitudes pendientes
                        </a>
                        @endif
                        <a href="{{ route('notificaciones') }}"
                            class="ajax-link block px-4 py-2 text-sm text-gray-700">Mostrar todas las notificaciones</a>
                    </div>
                </div>

                <!-- Dropdown de Usuario -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Open user menu</span>
                        @if (auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . $this->user->profile_photo_path) }}"
                            alt="{{ $this->user->name }}" class="h-8 w-8 rounded-full">
                        @else
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                            class="h-8 w-8 rounded-full">
                        @endif
                    </button>
                    <div x-show="open" @click.away="open = false"
                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md bg-white ring-1 ring-black ring-opacity-5"
                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu">
                        <a href="{{ route('usuarios-perfiles') }}"
                            class="ajax-link block px-4 py-2 text-sm text-gray-700"
                            data-url="{{ route('usuarios-perfiles') }}"
                            @click.prevent="currentPage = 'usuarios-perfiles'">Perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700">Desconectarse</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
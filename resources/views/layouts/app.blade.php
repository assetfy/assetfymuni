<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Meta tags condicionales para usuarios que NO son 'normal' --}}
    @if (Auth::check() && session('user_type') !== 'normal')
        <meta name="user-id" content="{{ Auth::user()->id }}">
        <meta name="user-name" content="{{ Auth::user()->name }}">
    @endif

    <title>{{ config('app.name', 'Activos') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://use.fontawesome.com/releases/v6.3.0/css/all.css" rel="stylesheet">
    <!-- Otros estilos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Intro.js CSS -->
    <link href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch/dist/geosearch.css" />
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.21/dist/sweetalert2.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    {{-- Scripts Condicionales para usuarios que NO son 'normal' --}}
    @if (Auth::check() && session('user_data.user_type') !== 'empresa')
        <script>
            var userPanel = "{{ Auth::user()->panel_actual }}"; // Inyecta la variable desde el backend
            var currentView = "{{ request()->route()->getName() }}";
        </script>
    @endif

    {{-- Compilación de Assets con Vite- --}}
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/calendar.js', 'resources/js/sidebar.js', 'resources/js/swal.js', 'resources/js/map-iniModal.js', 'resources/js/carousel.js', 'resources/js/swapy.js', 'resources/js/chatbot.js', 'resources/js/apexcharts.js', 'resources/js/mapAndDonut.js', 'resources/js/google.maps.js', 'resources/js/customMap.js', 'resources/js/orgchart.js', 'resources/js/orgchart-init.js', 'resources/js/GraficoDonaReporte.js', 'resources/js/GraficoDeTipos.js', 'resources/js/GraficoGestor.js', 'resources/js/jstree-setup.js', 'resources/js/Js-treeEdit.js', 'resources/js/soporte.js', 'resources/js/organigrama.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased" x-data="{ show: false, sidebarOpen: false }">
    {{-- Condición Principal: Mostrar sidebar, navbar y enlaces solo para usuarios 'normal' o cuando user_type está vacío --}}
    @if (Auth::check() && (empty(session('user_data')) || session('user_data.user_type') !== 'empresa'))
        {{-- Verificar si es la primera sesión del usuario --}}
        @if (Auth::user()->primera_session != 2)
            <div id="content" :class="{ 'sidebar-open': sidebarOpen }" class="flex">
                {{-- Sidebar --}}
                <div
                    :class="{
                        'sidebar-empresa': {{ Auth::user()->panel_actual == 'Empresa' || Auth::user()->panel_actual == 'Prestadora' ? 'true' : 'false' }},
                        'sidebar-usuario': {{ Auth::user()->panel_actual == 'Usuario' ? 'true' : 'false' }},
                        'sidebar-estado': {{ Auth::user()->panel_actual == 'Estado' || Auth::user()->panel_actual == 'Controladora' ? 'true' : 'false' }},
                    }">

                    @switch(Auth::user()->panel_actual)
                        @case('Usuario')
                            @livewire('usuarios.sidebar-usuario')
                        @break

                        @case('Estado')
                        @case('Controladora')
                            @livewire('estado.sidebar-estado')
                        @break

                        @case('Empresa')
                        @case('Prestadora')
                            @livewire('empresas.sidebar-empresa')
                        @break
                    @endswitch
                </div>

                {{-- Navbar --}}
                @switch(Auth::user()->panel_actual)
                    @case('Usuario')
                        @livewire('usuarios.navegation-menu')
                    @break

                    @case('Estado')
                    @case('Controladora')
                        @livewire('empresas.navegation-menu')
                    @break

                    @case('Empresa')
                    @case('Prestadora')
                        @livewire('empresas.navegation-menu')
                    @break
                @endswitch

                {{-- Main Content para usuarios 'normal' --}}
                <div id="main-content" class="flex-1">
                    <div class="home-section" @click="sidebarOpen = false">
                        {{ $slot }} <!-- Contenido principal -->
                        @livewire('chatbot')
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- Solo Main Content para usuarios que NO son 'normal' --}}
        <div id="main-content" class="flex-1 p-2">
            <div class="home-section" @click="sidebarOpen = false">
                {{ $slot }} <!-- Contenido principal -->
            </div>
        </div>
    @endif

    {{-- Scripts y otros elementos al final del body --}}
    @stack('modals')
    @stack('js')
    @stack('printportals')

    @if (!Auth::check())
        @livewire('activos.correos')
    @endif
    @livewireScripts
    {{-- Incluir enlaces parciales solo si el usuario es 'normal' o user_type está vacío --}}
    @if (Auth::check() && (empty(session('user_data.user_type')) || session('user_data.user_type') !== 'empresa'))
        @livewire('modal-container')
        @livewire('servicios.proveedores-detalle')
    @endif
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Axios JS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <!-- Leaflet GeoSearch JS -->
    <script src="https://unpkg.com/leaflet-geosearch/dist/bundle.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.21/dist/sweetalert2.all.min.js"></script>
    <!-- Swapy JS -->
    <script src="https://unpkg.com/swapy@latest/dist/swapy.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- ApexCharts JS -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Intro.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
    <!-- Popper.js JS -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>

    <!-- Fuse.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fuse.js@6.4.6/dist/fuse.min.js"></script>

    <!-- Google Maps JavaScript API con atributo async y solo la librería places y marker -->
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places,marker">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.js"></script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js" defer></script>


    <!-- Incluir ajax.js después de Livewire Scripts y jQuery -->
    <script src="{{ asset('js/ajax.js') }}"></script>

    {{-- Mostrar mensaje de error si existe --}}
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: "Permiso denegado",
                    text: "{{ session('error') }}",
                    icon: "error"
                });
            });
        </script>
    @endif
    <!-- Footer que siempre permanece abajo -->
    <footer class="app-footer">
        <br>
        <div class="container-fluid inner">
            <nav class="footer-links" aria-label="Enlaces legales">
                <a href="#" class="footer-link" title="Sub-Assetfy" aria-label="Sub-Assetfy">
                    <i class="fa-solid fa-layer-group"></i><span class="label">Sub-Assetfy</span>
                </a>
                <a href="#" class="footer-link" title="Términos de Uso" aria-label="Términos de Uso">
                    <i class="fa-solid fa-scale-balanced"></i><span class="label">Términos de Uso</span>
                </a>
                <a href="#" class="footer-link" title="Políticas de Privacidad"
                    aria-label="Políticas de Privacidad">
                    <i class="fa-solid fa-user-shield"></i><span class="label">Políticas de Privacidad</span>
                </a>
                <a href="#" class="footer-link" title="Contacto" aria-label="Contacto">
                    <i class="fa-solid fa-envelope"></i><span class="label">Contacto</span>
                </a>
                <a href="{{ route('soporte.form') }}" class="footer-link" title="Soporte" aria-label="Soporte">
                    <i class="fa-solid fa-life-ring"></i><span class="label">Soporte</span>
                </a>
            </nav>

            <div class="brand">2025 – Assetfy ©</div>
        </div>
    </footer>

</body>


</html>

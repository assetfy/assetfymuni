<div class="container mx-auto p-4 h-full" x-data="{
    showCalendar: false,
    searchQuery: '', // usado en la primera columna para evitar error al watch
    /* Si no usas searchQuery aquí, podrías eliminarlo, pero
       lo dejamos por si lo necesitas en el futuro. */
    toggleCalendar() {
        const calendarAndUbicaciones = document.getElementById('calendarAndUbicaciones');
        const toggleButtonIcon = document.querySelector('#toggleCalendarButton i');
        if (this.showCalendar) {
            calendarAndUbicaciones.style.transform = 'rotateY(0deg)';
            toggleButtonIcon.classList.remove('fa-building');
            toggleButtonIcon.classList.add('fa-calendar-days');
        } else {
            calendarAndUbicaciones.style.transform = 'rotateY(180deg)';
            toggleButtonIcon.classList.remove('fa-calendar-days');
            toggleButtonIcon.classList.add('fa-building');
        }
        this.showCalendar = !this.showCalendar;
    }
}" x-init="$watch('searchQuery', () => { activeTab = 0; })">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">
        <!-- Columna Izquierda -->
        <div class="lg:col-span-2 flex-1 flex-col">
            <!-- Encabezado -->
            <div class="flex items-center mb-8">
                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                    class="h-20 w-20 rounded-full mr-4">
                <div>
                    <h1 class="text-3xl font-bold">👋 Hola, {{ auth()->user()->name }}!</h1>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <!-- Cards de Reportes -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-blue-800">Total de Activos</h3>
                    <p class="mt-2 text-4xl font-bold text-blue-600">{{ count($activos) }}</p>
                </div>
                <div class="bg-green-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-green-800">Activos en Estado Normal</h3>
                    <p class="mt-2 text-4xl font-bold text-green-600">{{ count($lista_activos_normal) }}</p>
                </div>
                <div class="bg-yellow-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-orange-800">Activos en Estado Baja</h3>
                    <p class="mt-2 text-4xl font-bold text-orange-500">{{ count($lista_activos_baja) }}</p>
                </div>
                <div class="bg-yellow-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-yellow-800">Cotizaciones</h3>
                    <p class="mt-2 text-4xl font-bold text-yellow-600">{{ count($lista_cotizaciones_solicitadas) }}</p>
                </div>
                <div class="bg-purple-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-purple-800">Servicios sin confirmar</h3>
                    <p class="mt-2 text-4xl font-bold text-purple-600">{{ $serviciosCount }}</p>
                </div>
                <div class="bg-teal-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-teal-800">Ubicaciones</h3>
                    <p class="mt-2 text-4xl font-bold text-teal-600">{{ $ubicacionesCount }}</p>
                </div>
                <div class="bg-pink-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-pink-800">Servicios Realizados</h3>
                    <p class="mt-2 text-4xl font-bold text-pink-600">{{ count($servicios_finalizados) }}</p>
                </div>
                <div class="bg-indigo-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-indigo-800">Reseñas Realizadas</h3>
                    <p class="mt-2 text-4xl font-bold text-indigo-600">{{ count($calificaciones) }}</p>
                </div>
                <div class="bg-red-50 shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-medium text-red-800">Reseñas Pendientes</h3>
                    <p class="mt-2 text-4xl font-bold text-red-600">{{ count($calificaciones_pendientes) }}</p>
                </div>
            </div>

            <!-- Sección de Novedades -->
            <div wire:poll.30s="refreshDashboardData" wire:key="notificaciones-section">
                <h3 class="text-2xl font-semibold mb-4">Novedades</h3>
                <div class="space-y-4">
                    @if (
                        $servicios->isEmpty() &&
                            $notificaciones->isEmpty() &&
                            ($user->panel_actual == 'Empresa' ? $asignacionesEmpresa->isEmpty() : true))
                        <p class="text-gray-600 text-sm uppercase">Sin novedades.</p>
                    @else
                        <!-- Servicios -->
                        @foreach ($servicios as $servicio)
                            <div class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 shadow-md">
                                <i class="fa-solid fa-file-invoice-dollar fa-xl text-blue-600"></i>
                                <div>
                                    @if ($empresa->isNotEmpty())
                                        @foreach ($empresa as $emp)
                                            <p class="font-semibold text-gray-800">
                                                Recibiste una cotización de {{ $emp->razon_social }}
                                            </p>
                                        @endforeach
                                    @else
                                        <p class="font-semibold text-gray-800">
                                            Información de la empresa no disponible.
                                        </p>
                                    @endif

                                    @foreach ($actividad as $act)
                                        <p class="text-gray-600">{{ $act->nombre }}</p>
                                    @endforeach
                                </div>
                                <div class="ml-auto text-right">
                                    <p class="text-gray-800 font-bold">
                                        ${{ number_format($servicio->precio, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <button class="bg-blue-500 text-white rounded py-2 px-4 flex items-center gap-2"
                                        wire:click="update({{ $servicio }})">
                                        <i class="fa-solid fa-calendar-days fa-lg"></i>
                                        Agendar
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <!-- Notificaciones -->
                        @foreach ($notificaciones as $notificacion)
                            @foreach ($empresasDatos as $empresaDato)
                                @if ($notificacion->cuit == $empresaDato->cuit)
                                    <div class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 shadow-md">
                                        <i class="fa-solid fa-envelope fa-xl text-blue-500"></i>
                                        <div>
                                            <p>
                                                <strong>{{ $empresaDato->razon_social }}</strong>
                                                te invita a unirte
                                            </p>
                                        </div>
                                        <div class="ml-auto">
                                            <button
                                                wire:click="actualizarEstado('Aceptado', '{{ $empresaDato->cuit }}')"
                                                class="bg-blue-500 text-white py-2 px-4 rounded">
                                                <i class="fa-solid fa-check"></i> Aceptar
                                            </button>
                                            <button
                                                wire:click="actualizarEstado('Rechazado', '{{ $empresaDato->cuit }}')"
                                                class="bg-red-500 text-white py-2 px-4 rounded">
                                                <i class="fa-solid fa-times"></i> Rechazar
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach

                        @php
                            // Indexar empresas por CUIT para acceso rápido
                            $empresasPorCuit = collect($empresasDatos)->keyBy('cuit');

                            // Indexar activos por ID para evitar múltiples iteraciones
                            $activosPorId = collect($activosEmpresa)->keyBy('id_activo');
                        @endphp

                        @if (!empty($asignacionEmpresa))
                            @foreach ($asignacionesEmpresa as $asignacionEmpresa)
                                @php
                                    $empresa = $empresasPorCuit[$asignacionEmpresa->cuit] ?? null;
                                @endphp

                                @if ($empresa)
                                    @foreach ($isAsignado as $asignado)
                                        @php
                                            $activo = $activosPorId[$asignado] ?? null;
                                        @endphp

                                        @if ($activo)
                                            <div
                                                class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 shadow-md">
                                                <i class="fa-solid fa-envelope fa-xl text-blue-500"></i>
                                                <div class="flex-grow">
                                                    <p>
                                                        <strong>{{ $empresa->razon_social }}</strong>
                                                        Te ha asignado el bien: {{ $activo->nombre }}
                                                    </p>
                                                </div>
                                                <div class="ml-auto">
                                                    <button wire:click="visto({{ $asignado }})"
                                                        class="bg-red-500 text-white py-2 px-4 rounded">
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @foreach ($isGestionado as $gestion)
                                        @php
                                            $activo = $activosPorId[$gestion] ?? null;
                                        @endphp

                                        @if ($activo)
                                            <div
                                                class="flex items-center space-x-4 p-4 rounded-lg bg-gray-50 shadow-md">
                                                <i class="fa-solid fa-envelope fa-xl text-blue-500"></i>
                                                <div class="flex-grow">
                                                    <p>
                                                        <strong>{{ $empresa->razon_social }}</strong>
                                                        Serás gestor del bien: {{ $activo->nombre }}
                                                    </p>
                                                </div>
                                                <div class="ml-auto">
                                                    <button wire:click="visto({{ $gestion }})"
                                                        class="bg-red-500 text-white py-2 px-4 rounded">
                                                        OK
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha (Mapa/Calendario) -->
        <div class="lg:col-span-1 flex flex-col h-full" style="padding-top: 60px;" x-data="{
            showCalendar: false,
            map: null,
            searchQuery: '', // <-- Input de búsqueda
            markers: [], // <-- Array donde se guardan los marcadores
        
            toggleCalendar() {
                const calendarAndUbicaciones = document.getElementById('calendarAndUbicaciones');
                const toggleButtonIcon = document.querySelector('#toggleCalendarButton i');
                if (this.showCalendar) {
                    // Volver a la vista del MAPA
                    calendarAndUbicaciones.style.transform = 'rotateY(0deg)';
                    toggleButtonIcon.classList.remove('fa-building');
                    toggleButtonIcon.classList.add('fa-calendar-days');
                } else {
                    // Ir al CALENDARIO
                    calendarAndUbicaciones.style.transform = 'rotateY(180deg)';
                    toggleButtonIcon.classList.remove('fa-calendar-days');
                    toggleButtonIcon.classList.add('fa-building');
                }
                this.showCalendar = !this.showCalendar;
        
                // Forzar un 'resize' cuando se regresa al mapa
                if (!this.showCalendar && this.map) {
                    setTimeout(() => {
                        google.maps.event.trigger(this.map, 'resize');
                    }, 300);
                }
            },
        
            // Método para inicializar el mapa, esperando que la API esté cargada
            initMap() {
                const checkApi = setInterval(() => {
                    // Verifica que la API de Google Maps esté disponible
                    if (typeof google !== 'undefined' && google.maps) {
                        clearInterval(checkApi); // API lista: se detiene el temporizador
        
                        // Array de ubicaciones con lat/lng (desde tu controlador)
                        const ubicaciones = {{ json_encode($ubicacionesArray) }};
                        // Centro inicial (por ejemplo, Corrientes)
                        const corrientes = { lat: -27.4799, lng: -58.8361 };
        
                        // Crear el mapa dentro del div #map
                        this.map = new google.maps.Map(document.getElementById('map'), {
                            center: corrientes,
                            zoom: 13,
                            mapTypeId: 'roadmap'
                        });
        
                        // Ajustar para mostrar todas las ubicaciones
                        const bounds = new google.maps.LatLngBounds();
        
                        // Crear marcadores
                        ubicaciones.forEach((ubi) => {
                            if (ubi.lat && ubi.lng) {
                                let marker = new google.maps.Marker({
                                    position: {
                                        lat: parseFloat(ubi.lat),
                                        lng: parseFloat(ubi.lng)
                                    },
                                    map: this.map,
                                    // 'title' se usará para filtrar por dirección
                                    title: ubi.direccion
                                });
                                this.markers.push(marker); // Guardar marcador
                                bounds.extend(marker.position);
                            }
                        });
        
                        // Ajustar el zoom para ver todos los marcadores (si hay alguno)
                        if (!bounds.isEmpty()) {
                            this.map.fitBounds(bounds);
                        }
                    }
                }, 100); // Revisa cada 100 milisegundos
            },
        
            // Método para filtrar marcadores según searchQuery
            filterMarkers() {
                const query = this.searchQuery.toLowerCase().trim();
                this.markers.forEach(marker => {
                    // Comparar con el 'title' (la dirección)
                    marker.setVisible(marker.title.toLowerCase().includes(query));
                });
            }
        }"
            x-init="initMap();
            $watch('searchQuery', () => filterMarkers());">
            <!-- Barra superior: Botón toggle + Input de búsqueda -->
            <div class="flex items-center space-x-4 mb-4">
                <button id="toggleCalendarButton" @click="toggleCalendar"
                    class="bg-blue-500 text-white rounded-full p-3 shadow-md flex items-center justify-center">
                    <i :class="showCalendar ? 'fa-solid fa-building fa-xl' : 'fa-solid fa-calendar-days fa-xl'"></i>
                </button>

                <!-- NUEVO: Input para buscar ubicaciones en el mapa -->
                <input type="text" placeholder="Buscar ubicación..." x-model="searchQuery"
                    class="border px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" />
            </div>

            <!-- Contenedor que maneja la perspectiva 3D para el flip -->
            <div style="position: relative; height: calc(100% - 100px); overflow: hidden;">
                <div id="mainContainer" class="main-container"
                    style="perspective: 1000px; position: relative; width: 100%; height: 100%;">
                    <div id="calendarAndUbicaciones" class="inner-container"
                        style="
                            transform-style: preserve-3d;
                            transition: transform 0.6s;
                            position: relative;
                            width: 100%;
                            height: 100%;
                         ">
                        <!-- PARTE TRASERA: Calendario -->
                        <div id="calendarContainer" class="calendar-container backface-hidden"
                            style="
                                position: absolute;
                                width: 100%;
                                height: 100%;
                                backface-visibility: hidden;
                                transform: rotateY(180deg);
                             ">
                            <div class="p-4 h-full" style="border: none; overflow-y: auto;">
                                <h3 class="text-xl font-semibold mb-4 text-black text-center">
                                    Calendario de servicios
                                </h3>
                                <!-- Componente Livewire del calendario -->
                                <livewire:calendar :initialView="'dayGridMonth'" />

                                <!-- Próximos servicios -->
                                <h4 class="text-lg font-semibold mt-4">Próximos servicios</h4>
                                <div class="mt-2 space-y-2">
                                    @if ($servicios->isEmpty())
                                        <p class="text-gray-500">Sin servicios próximos</p>
                                    @else
                                        @foreach ($servicios as $servicio)
                                            @php
                                                $serviceDate = $servicio->fecha_modificada
                                                    ? \Carbon\Carbon::parse($servicio->fecha_modificada)
                                                    : \Carbon\Carbon::parse($servicio->fechaHora);
                                            @endphp
                                            <div class="service-date p-2 rounded bg-blue-100 mb-2">
                                                <p class="text-sm font-semibold">
                                                    {{ $serviceDate->format('jS F') }}
                                                </p>
                                                <p class="text-sm">
                                                    {{ $servicio->descripcion }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $serviceDate->format('H:i') }}
                                                </p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- PARTE FRONTAL: Mapa -->
                        <div id="ubicacionesContainer" class="ubicaciones-container backface-hidden"
                            style="
                                position: absolute;
                                width: 100%;
                                height: 100%;
                                backface-visibility: hidden;
                                transform: rotateY(0deg);
                            ">
                            <div class="relative w-full h-full p-4" style="border: none; overflow-y: auto;">
                                <!-- wire:ignore para que Livewire no re-renderice el div del mapa -->
                                <div id="map" class="w-full" wire:ignore
                                    style="height: 50%; border: 1px solid #ccc; border-radius: 16px; overflow: hidden;">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (min-width: 1020px) and (max-width: 1379px) {
            .custom-grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>
</div>

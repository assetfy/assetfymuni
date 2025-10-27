<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl"
        class="fixed inset-0 flex items-center justify-center bg-black/50">


        {{-- Header con Logo, Nombre y Calificación --}}
        <x-slot name="title">
            <div class="flex justify-between items-center bg-white p-6 rounded-t-lg shadow relative">

                {{-- Logo de la empresa en la izquierda --}}
                @if (!empty($logo))
                    <div class="absolute left-0 ml-4">
                        <img src="{{ asset('storage/logos/' . $logo) }}" alt="Logo de {{ $nombreEmpresa }}"
                            class="w-16 h-16 object-cover rounded-full border border-gray-300 shadow">
                    </div>
                @endif

                {{-- Nombre de la Empresa en el Centro --}}
                <h2 class="text-xl font-bold text-gray-800 mx-auto">
                    @if (!empty($nombreEmpresa))
                        <span class="text-red-600 font-medium block">{{ $nombreEmpresa }}</span>
                    @else
                        <span class="text-gray-500">No hay datos</span>
                    @endif
                </h2>

                {{-- Botón de Cerrar --}}
                <button type="button" wire:click="$set('open', false)" class="text-red-500 hover:text-red-700">
                    <span class="sr-only">Cerrar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Sección de Calificación de Asset-Fy --}}
            @if (isset($puntajeAssetFly))
                <div class="flex justify-center items-center mt-4">
                    <span class="text-gray-700 font-semibold mr-2">Calificación Asset-Fy:</span>

                    @php
                        $fullStars = floor($puntajeAssetFly);
                        $halfStar = $puntajeAssetFly - $fullStars >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp

                    {{-- Estrellas llenas --}}
                    @for ($i = 0; $i < $fullStars; $i++)
                        <i class="fa fa-star text-yellow-500 text-lg"></i>
                    @endfor

                    {{-- Media estrella si corresponde --}}
                    @if ($halfStar)
                        <i class="fa fa-star-half-o text-yellow-500 text-lg"></i>
                    @endif

                    {{-- Estrellas vacías --}}
                    @for ($j = 0; $j < $emptyStars; $j++)
                        <i class="fa fa-star-o text-yellow-500 text-lg"></i>
                    @endfor

                    <span class="ml-2 text-gray-700">
                        {{ number_format($puntajeAssetFly, 1) }} / 5
                    </span>
                </div>
            @endif
        </x-slot>


        {{-- Contenido del Modal --}}
        <x-slot name="content">
            <div></div>
            <div class="bg-white p-6 space-y-6 rounded-b-lg shadow">

                {{-- Barra de Navegación para las Pestañas --}}
                <div class="flex justify-between items-center border-b pb-3">

                    <ul class="flex justify-start space-x-4">
                        <li>
                            <button wire:click="$set('activeTab', 'assetfy')"
                                class="{{ $activeTab === 'assetfy' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black' }} px-3 py-1">
                                Asset-fy
                            </button>
                        </li>
                        <li>
                            <button wire:click="$set('activeTab', 'google')"
                                class="{{ $activeTab === 'google' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black' }} px-3 py-1">
                                Reseñas
                            </button>
                        </li>
                        <li>
                            <button wire:click="$set('activeTab', 'web')"
                                class="{{ $activeTab === 'web' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black' }} px-3 py-1">
                                Web
                            </button>
                        </li>
                        <li>
                            @if ($Esprovedor)
                                <button wire:click="eliminarfav"
                                    class="px-4 py-2 bg-green-500 text-white font-bold rounded hover:bg-blue-600">
                                    Eliminar de Favoritos
                                </button>
                            @else
                                <button wire:click="agregarFav"
                                    class="px-4 py-2 bg-green-500 text-white font-bold rounded hover:bg-blue-600">
                                    Agregar a Favoritos
                                </button>
                            @endif
                        </li>
                    </ul>
                </div>

                <!-- Pestaña ASSET-FY -->
                <div class="{{ $activeTab === 'assetfy' ? '' : 'hidden' }}">

                    {{-- Íconos en la parte superior --}}
                    <div class="grid grid-cols-2 gap-4 text-center mt-4 p-4 bg-gray-100 rounded-lg shadow-md">

                        {{-- Icono de Contrataciones --}}
                        <div class="flex flex-col items-center">
                            <i class="fa fa-clipboard-list text-blue-500 text-4xl"></i>
                            <span class="text-gray-700 font-semibold mt-2">Contrataciones</span>
                            <span class="text-gray-900 text-lg font-bold">{{ $serviciosContratados ?? 0 }}</span>
                        </div>

                        {{-- Icono de Calificación numérica --}}
                        <div class="flex flex-col items-center">
                            <i class="fa fa-chart-line text-green-500 text-4xl"></i>
                            <span class="text-gray-700 font-semibold mt-2">Calificación</span>
                            <span class="text-gray-900 text-lg font-bold">{{ number_format($puntajeAssetFly, 2) }} /
                                5</span>
                        </div>

                    </div>

                    {{-- Información de la Empresa --}}
                    <strong class="text-gray-700 block mt-6">Nombre de la Empresa:</strong>
                    @if ($nombreEmpresa)
                        <span class="text-gray-900 font-medium mt-1 block">{{ $nombreEmpresa }}</span>
                    @else
                        <span class="text-gray-500">No hay datos</span>
                    @endif

                    <strong class="text-gray-700 block mt-4">Descripción:</strong>
                    @if ($descripcion)
                        <p class="text-gray-900 font-medium mt-1">{{ $descripcion }}</p>
                    @else
                        <p class="text-gray-500">No hay datos</p>
                    @endif

                    <strong class="text-gray-700 block mt-4">Actividad Económica:</strong>
                    @if ($actividadEconomica)
                        <span class="text-gray-900 font-medium mt-1">{{ $actividadEconomica }}</span>
                    @else
                        <span class="text-gray-500">No hay datos</span>
                    @endif

                    {{-- Mapa de ubicación (wire:ignore para que Livewire no lo regenere) --}}
                    <strong class="text-gray-700 block mt-6">Mapa de Ubicación:</strong>
                    <div id="miniMaps" class="mt-2 border border-gray-300 rounded" style="width: 100%; height: 200px;"
                        wire:ignore></div>

                </div>

                <!-- Pestaña CALIFICACIÓN -->
                <div class="{{ $activeTab === 'calificacion' ? '' : 'hidden' }}">
                    @if (!empty($places))
                        @if (!is_null($rating))
                            @php
                                $fullStars = floor($rating); // ej: 4 si rating=4.5
                                $hasHalfStar = $rating - $fullStars >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            <div class="flex items-center space-x-1 mt-2">
                                {{-- Estrellas llenas --}}
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <i class="fa fa-star text-yellow-500"></i>
                                @endfor

                                {{-- Media estrella si corresponde --}}
                                @if ($hasHalfStar)
                                    <i class="fa fa-star-half-o text-yellow-500"></i>
                                @endif

                                {{-- Estrellas vacías --}}
                                @for ($j = 0; $j < $emptyStars; $j++)
                                    <i class="fa fa-star-o text-yellow-500"></i>
                                @endfor

                                <span class="ml-2 text-gray-700">
                                    {{ number_format($rating, 1) }} / 5
                                    ({{ $userRatingsTotal }} reseñas)
                                </span>
                            </div>
                        @else
                            <p class="text-gray-500 mt-2">Sin calificación de Google disponible.</p>
                        @endif
                    @else
                        <p class="text-gray-500 mt-2">No hay Google Place asociado.</p>
                    @endif
                </div>

                <!-- Pestaña GOOGLE -->
                <div class="{{ $activeTab === 'google' ? '' : 'hidden' }}">
                    <!-- Botones para filtrar reseñas positivas y negativas -->
                    <div>
                        <!-- Botones para filtrar reseñas positivas y negativas -->
                        <div class="flex justify-center space-x-4 mb-4">
                            <button wire:click="setReviewFilter('positiva')"
                                class="{{ $filtro === 'positiva' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }} px-4 py-2 rounded">
                                Reseñas Positivas
                            </button>
                            <button wire:click="setReviewFilter('negativa')"
                                class="{{ $filtro === 'negativa' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-black' }} px-4 py-2 rounded">
                                Reseñas Negativas
                            </button>
                        </div>

                        @php
                            // En caso de que, por alguna razón, $activosServicios pueda ser null,
                            // forzamos a que sea una colección vacía para evitar errores:
                            $activosServicios = $activosServicios ?? collect();

                            $reseñasFiltradas = $activosServicios->filter(function ($registro) use ($filtro) {
                                // 1 = contratación positiva, 0 = negativa
                                return $registro->contratacion == ($filtro === 'positiva' ? 1 : 0);
                            });
                        @endphp

                        @if ($reseñasFiltradas->isEmpty())
                            <p class="text-gray-500">
                                No hay reseñas {{ $filtro }}.
                            </p>
                        @else
                            @foreach ($reseñasFiltradas as $reseña)
                                @php
                                    $calificacion = (float) $reseña->calificacion;
                                    $diagnostico = (float) $reseña->diagnostico;
                                    $precio = (float) $reseña->precio;
                                    $promedio = round(($calificacion + $diagnostico + $precio) / 3, 1);
                                @endphp

                                <div class="border p-4 rounded mb-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold">Puntaje: {{ $reseña->calificacion }}</span>
                                        <span class="text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($reseña->fecha_resenia)->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <p>Diagnóstico: {{ $reseña->diagnostico }}</p>
                                        <p>Precio: {{ $reseña->precio }}</p>
                                        <p><span class="font-semibold">Promedio:</span> {{ $promedio }} / 5</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>

                <!-- Pestaña WEB -->
                <div class="{{ $activeTab === 'web' ? '' : 'hidden' }}">
                    @if (!empty($url))
                        <p class="text-gray-700">
                            Sitio Web:
                            <a href="{{ $url }}" target="_blank" class="text-blue-600 underline">
                                {{ $url }}
                            </a>
                        </p>
                    @else
                        <p class="text-gray-500">
                            No hay URL disponible
                        </p>
                    @endif
                </div>
            </div>
        </x-slot>

        {{-- Footer --}}
        <x-slot name="footer">
            <div class="mt-6 text-center">
                <button wire:click="PedirCotizacion"
                    class="px-4 py-2 bg-blue-500 text-white font-bold rounded hover:bg-blue-600">
                    Solicitar Cotizacion
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

<script>
    Livewire.on('showProveedorMap', (coords) => {
        console.log('==== showProveedorMap event disparado ====');
        console.log('Payload coords:', coords);

        // Variables para las coordenadas brutas
        let latRaw, lngRaw;

        if (Array.isArray(coords) && coords.length > 0) {
            latRaw = coords[0].lat;
            lngRaw = coords[0].lng;
        } else {
            latRaw = coords.lat;
            lngRaw = coords.lng;
        }

        let lat = parseFloat(latRaw);
        let lng = parseFloat(lngRaw);

        // Fallback si son NaN
        if (isNaN(lat) || isNaN(lng) || !lat || !lng) {
            lat = -27.4799; // Ej. Corrientes
            lng = -58.8361;
        }

        console.log('Mapa con lat:', lat, 'lng:', lng);

        const mapElement = document.getElementById('miniMaps');
        if (!mapElement) {
            console.log('No hay #miniMap en el DOM');
            return;
        }

        // Esperamos un poco si el modal se anima al abrir
        setTimeout(() => {
            const map = new google.maps.Map(mapElement, {
                center: {
                    lat,
                    lng
                },
                zoom: 14,
                mapTypeId: 'roadmap',
            });

            new google.maps.Marker({
                position: {
                    lat,
                    lng
                },
                map: map,
                title: 'Ubicación de la empresa'
            });
        }, 300);
    });
</script>

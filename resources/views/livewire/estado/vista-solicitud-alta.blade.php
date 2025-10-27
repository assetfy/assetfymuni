<div class="mt-8 p-4 sm:p-6 lg:p-8 bg-white">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Solicitud de Habilitación</h2>
    </div>

    <div class="mt-4 flex flex-wrap items-start justify-between">
        <div class="w-full lg:w-2/3 mb-4 lg:mb-0">
            <div class="mb-4">
                <h3 class="text-lg font-bold"><i class="fas fa-building"></i> Nombre de la empresa:</h3>
                <p>{{ $empresa->razon_social }}</p>
                <h3 class="text-lg font-bold mt-2"><i class="fas fa-briefcase"></i> Actividad económica:</h3>
                <p>{{ $empresa->actividades->nombre }}</p>
                <h3 class="text-lg font-bold mt-2"><i class="fas fa-info-circle"></i> Descripción:</h3>
                <p class="text-gray-700">{{ $empresa->descripcion_actividad }}</p>
                <a href="{{ asset(str_replace('public/', '', $empresa->constancia_afip)) }}" target="_blank" class="text-blue-500 mt-2 block">
                    <i class="fas fa-file-alt"></i> Ver Documentación
                </a>
            </div>
            <div class="mt-4">
                @if ($lat && $long)
                    <div id="map" class="w-full h-64 mb-4" data-lat="{{ $lat }}" data-long="{{ $long }}"></div>
                @endif
            </div>
        </div>

        <div class="w-full lg:w-1/3 mt-6 lg:mt-0">
            <div class="flex flex-col items-center justify-between h-full">
                <div class="flex space-x-2 mb-4">
                    @if($empresa->estado != 'Aceptado')
                        <button wire:click="aceptado" class="bg-blue-500 text-white px-6 py-2 transition duration-300 ease-in-out transform hover:bg-blue-600 hover:shadow-lg hover:-translate-y-1">
                            Aprobar Solicitud
                        </button>
                        <button wire:click="rechazado" class="bg-red-500 text-white px-6 py-2 transition duration-300 ease-in-out transform hover:bg-red-600 hover:shadow-lg hover:-translate-y-1">
                            Rechazar Solicitud
                        </button>
                    @else
                        <a href="{{ route('servicios-prestadora-tabla') }}" class="text-blue-500 ajax-link no-underline">
                            <i class="fas fa-table"></i> Tabla de servicios
                        </a>
                    @endif
                </div>
                <h4 class="text-lg font-medium"><i class="fas fa-chart-line"></i> Performance</h4>
                @if($empresa->estado == 'Aceptado')
                    <div id="chart-container" class="w-full flex justify-center items-center flex-grow">
                        <div id="chart" class="w-full h-full" style="height: 400px;"></div>
                    </div>
                    <div class="flex flex-row space-x-4 mt-4 items-center">
                        <button id="remove" class="bg-red-500 text-white px-4 py-2 transition duration-300 ease-in-out transform hover:bg-red-600">Remover</button>
                        <button id="reset" class="bg-blue-500 text-white px-4 py-2 transition duration-300 ease-in-out transform hover:bg-blue-600">Resetear</button>
                    </div>
                @else
                    <p class="mt-4 text-gray-500 flex-grow">Aún no ha realizado ningún servicio.</p>
                @endif
            </div>
        </div>
    </div>
    
    @if ($lat && $long)
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    @endif
</div>

<script>
    // Asignar los datos a variables globales en window
    window.donutData = @json($monthlyDataJS);
    window.lat = {{ $lat }};
    window.long = {{ $long }};
    
    // Verificar si los datos se han asignado correctamente
    console.log('Datos asignados para Donut:', window.donutData);
</script>

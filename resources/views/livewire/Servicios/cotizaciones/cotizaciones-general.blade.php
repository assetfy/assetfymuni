<div>
    @if(!$tecnicos)
    <div class="flex flex-wrap gap-4 mt-4">
        <button wire:click="mostrarCotizaciones" class="ajax-link bg-pink-50 shadow-md rounded-lg p-4 w-56 m-0 hover:bg-pink-100 transition">
            @if($prestadora)
            <h3 class="text-lg font-medium text-pink-800">Cotizaciones</h3>
            @else
            <h3 class="text-lg font-medium text-pink-800">Mis cotizaciones</h3>
            @if(!$manager)
            <p class="mt-2 text-4xl font-bold text-pink-600">{{ $serviciosPrestadora }}</p>
            @else
            <p class="mt-2 text-4xl font-bold text-pink-600">{{ $gestora }}</p>
            @endif
            @endif
        </button>
        <button wire:click="mostrarAdjudicaciones" class="ajax-link bg-indigo-50 shadow-md rounded-lg p-4 w-56 m-0 hover:bg-indigo-100 transition">
            @if($prestadora)
            <h3 class="text-lg font-medium text-pink-800">Servicios</h3>
            @else
            @if($tipoEmpresa != 1)
            <h3 class="text-lg font-medium text-indigo-800">Mis Ordenes de Servicios</h3>
            @else
            <h3 class="text-lg font-medium text-indigo-800">Cotizaciones Adjudicadas <h2>(Servicios)</h2>
            </h3>
            @endif
            @if(!$manager)
            <p class="mt-2 text-4xl font-bold text-pink-600">{{ $realizadosPrestadora }}</p>
            @else
            <p class="mt-2 text-4xl font-bold text-pink-600">{{ $realizadosGestora }}</p>
            @endif
            @endif
        </button>
        @if($tipoEmpresa != 1)
        <button wire:click="mostrarAsignaciones" class="ajax-link bg-indigo-50 shadow-md rounded-lg p-4 w-56 m-0 hover:bg-indigo-100 transition">
            @if($prestadora)
            <h3 class="text-lg font-medium text-pink-800">Servicios sin Asignar</h3>
            @else
            <h3 class="text-lg font-medium text-indigo-800">Ordenes de Servicios <h2>Sin Asignar</h2>
            </h3>
            <p class="mt-2 text-4xl font-bold text-pink-600">{{ $serviciosSinAsignar }}</p>
            @endif
        </button>
        @endif
    </div>
    @endif

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if($tablaActual === 'usuarios-servicios')
            @livewire('usuarios.usuarios-servicios')
            @elseif($tablaActual === 'dashboard-prestadora')
            @livewire('empresas.dashboard-prestadora')
            @elseif($tablaActual === 'cotizaciones-adjudicadas')
            @livewire('servicios.cotizaciones.cotizaciones-adjudicadas')
            @elseif($tablaActual === 'cotizaciones-servicios')
            @livewire('servicios.cotizaciones.cotizaciones-servicios')
            @elseif($tablaActual === 'cotizaciones-sin-asignar')
            @livewire('servicios.cotizaciones.cotizaciones-sin-asignar')
            @endif
        </div>
    </div>
</div>
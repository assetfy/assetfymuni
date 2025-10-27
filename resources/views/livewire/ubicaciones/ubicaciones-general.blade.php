<div class="relative">
    @if (($apoderado || $admin) && ($tipoEmpresa == 1 || $tipoEmpresa == 2))
        <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
            <!-- Ubicaciones con Bienes -->
            <button wire:click="mostrarUbicaciones"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-[#2B008E] text-base"></i>
                        <span class="text-sm font-medium text-[#2B008E] whitespace-nowrap">Ubicaciones con Bienes</span>
                    </div>
                    @if ($tipoEmpresa == 1)
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantUbicacionesBienes }}</p>
                    @else
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantUbicacionesDelegadas }}</p>
                    @endif
                </div>
            </button>
            <!-- Ubicaciones Sin Bienes -->
            <button wire:click="mostrarUbicacionesSinBienes"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-map-pin text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Ubicaciones Sin Bienes</span>
                    </div>
                    @if ($tipoEmpresa == 1)
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantUbicacionesSinBienes }}</p>
                    @else
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantUbicacionesNoDelegadas }}</p>
                    @endif
                </div>
            </button>
        </div>
    @endif

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'ubicaciones')
                @livewire('ubicaciones.ubicaciones')
            @elseif($tablaActual === 'ubicaciones-sin-bienes')
                @livewire('ubicaciones.ubicaciones-sin-bienes')
            @elseif($tablaActual === 'ubicaciones-delegadas')
                @livewire('ubicaciones.ubicaciones-delegadas')
            @endif
        </div>
    </div>
</div>

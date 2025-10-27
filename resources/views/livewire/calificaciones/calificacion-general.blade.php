<div>
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
        <!-- Servicios Realizados / Contratados -->
        <button wire:click="solicitudesCerradas"
            class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FCE7F3] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    @if (!$manager)
                        <i class="fa-solid fa-clipboard-check text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Servicios Realizados</span>
                    @else
                        <i class="fa-solid fa-handshake text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Servicios Contratados</span>
                    @endif
                </div>
                <p class="mt-1 text-2xl font-bold text-[#2B008E]">
                    @if (!$manager)
                        {{ count($servicios_finalizados) }}
                    @else
                        {{ $serviciosGestora }}
                    @endif
                </p>
            </div>
        </button>
        <!-- Reseñas Realizadas -->
        @if (!$manager)
            <button wire:click="mostrarResenias"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-star text-[#3730A3] text-base"></i>
                        <span class="text-sm font-medium text-[#3730A3] whitespace-nowrap">Reseñas Realizadas</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ count($calificaciones) }}</p>
                </div>
            </button>
        @endif
        <!-- Reseñas Pendientes -->
        <button wire:click="reseniasFaltantes"
            class="ajax-link bg-[#FEF2F2] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FECACA] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-clock text-[#B91C1C] text-base"></i>
                    <span class="text-sm font-medium text-[#B91C1C] whitespace-nowrap">Reseñas Pendientes</span>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ count($calificaciones_pendientes) }}</p>
            </div>
        </button>
    </div>

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'solicitud-servicios')
                @livewire('servicios.solicitud-servicios')
            @elseif($tablaActual === 'solicitudes-cerradas')
                @livewire('servicios.solicitudes.solicitudes-cerradas')
            @elseif($tablaActual === 'resenia-efectuadas')
                @livewire('servicios.solicitudes.resenia-efectuadas')
            @elseif($tablaActual === 'resenias-faltantes')
                @livewire('servicios.solicitudes.resenias-faltantes')
            @endif
        </div>
    </div>
</div>

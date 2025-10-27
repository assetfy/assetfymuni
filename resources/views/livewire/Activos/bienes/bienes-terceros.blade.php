<div>
    @if ($apoderado || $adminPrestadora)
        <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
            <!-- Bienes Clientes -->
            <button wire:click="mostrarBienesTercerosAceptados"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-box-open text-[#3730A3] text-base"></i>
                        <span class="text-sm font-medium text-[#3730A3] whitespace-nowrap">Bienes Clientes</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#1E1B4B]">{{ $bienesAceptados }}</p>
                </div>
            </button>
            <!-- Bienes Pendientes Aceptación -->
            <button wire:click="mostrarBienesTercerosPendientes"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-hourglass-half text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Bienes Pendientes
                            Aceptación</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#831843]">{{ $bienesPendientes }}</p>
                </div>
            </button>
        </div>
    @endif

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'bienes-aceptados-terceros')
                @livewire('activos.bienes.bienes-aceptados-terceros')
            @elseif($tablaActual === 'bienes-pendientes-terceros')
                @livewire('activos.bienes.bienes-pendientes-terceros')
            @endif
        </div>
    </div>
</div>

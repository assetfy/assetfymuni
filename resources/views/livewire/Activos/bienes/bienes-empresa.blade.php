<div class="relative">
    @if ($apoderado || $admin)
        <!-- Botón Delegar Bienes en la esquina superior derecha -->
        {{--  <div class="absolute top-4 right-4">
                <a href="{{ route('delegar-bienes') }}"
                    class="ajax-link tooltip-item bg-[#EEF2FF] shadow-md rounded-lg p-4 hover:bg-[#E0E7FF] transition"
                    data-tippy-content="Delegar Bienes">
                    <i class="fa-solid fa-handshake text-[#3730A3]"></i>
                    <span class="link_name ml-1 text-[#3730A3]">Delegar Bienes</span>
                </a>
            </div> --}}
        {{-- <div class="flex flex-wrap justify-center lg:justify-start items-stretch gap-4 px-4 py-4">
            <!-- Mis Bienes -->
            <button wire:click="mostrarBienesPropios"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Mis Bienes</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $bienesCount }}</p>
                </div>
            </button>
            <!-- Bienes Delegados -->
            <button wire:click="mostrarBienesAceptados"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-handshake text-[#3730A3] text-base"></i>
                        <span class="text-sm font-medium text-[#3730A3] whitespace-nowrap">Bienes Delegados</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $bienesDelegados }}</p>
                </div>
            </button> --}}
        <!-- Bienes Delegados (Pendientes) -->
        {{-- <button wire:click="mostrarBienesPendientes"
                class="ajax-link bg-[#E0F2FE] shadow-md rounded-lg p-3
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#BAE6FD] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-clock text-[#0369A1] text-base"></i>
                        <span class="text-sm font-medium text-[#0369A1] whitespace-nowrap">Bienes Delegados
                            (Pendientes)</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $bienesPendientes }}</p>
                </div>
            {{-- </button>  
        </div> --}}
    @endif

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'tablas')
                @livewire('menus.tablas')
            @elseif($tablaActual === 'bienes-pendientes')
                @livewire('activos.bienes.bienes-pendientes')
            @elseif($tablaActual === 'bienes-delegados')
                @livewire('activos.bienes.bienes-delegados')
            @endif
        </div>
    </div>
</div>

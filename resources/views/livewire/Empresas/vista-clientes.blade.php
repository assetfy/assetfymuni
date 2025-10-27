<div>
    <!-- Botones para cambiar entre tablas -->
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
        <!-- Mis Clientes -->
        <button wire:click="mostrarClientes"
            class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FCE7F3] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-group text-[#9D174D] text-base"></i>
                    <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Mis Clientes</span>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $clientes }}</p>
            </div>
        </button>
        <!-- Clientes sin Asignar -->
        <button wire:click=""
            class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#E0E7FF] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-slash text-[#3730A3] text-base"></i>
                    <span class="text-sm font-medium text-[#3730A3] whitespace-nowrap">Clientes sin Asignar</span>
                </div>
            </div>
        </button>
        <!-- Buscar Clientes -->
        @if (!$admin)
            <button wire:click="buscarClientes"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px] 
        hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Buscar Clientes</span>
                    </div>
                </div>
            </button>
        @endif
    </div>
    <!-- Carga del componente de tabla según la selección -->
    <div class="mt-4">
        @if ($tablaActual === 'mis-clientes')
            @livewire('empresas.clientes')
        @elseif ($tablaActual === 'buscar-clientes')
            @livewire('empresas.buscar-clientes')
        @endif
    </div>
</div>

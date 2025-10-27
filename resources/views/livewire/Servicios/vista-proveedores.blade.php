<div class="relative">
    <!-- Botones para cambiar entre tablas -->
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
        <!-- Mis Proveedores -->
        <button wire:click="mostrarMisProveedores"
            class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#E0E7FF] transition">
            <div class="flex flex-col items-center">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-users-gear text-[#3730A3] text-sm"></i>
                    <h3 class="text-sm font-medium text-[#3730A3] text-center">Mis Proveedores</h3>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#2B008E] text-center">{{ $cantProveedores }}</p>
            </div>
        </button>
        <!-- Buscar Nuevos Proveedores -->
        <button wire:click="mostrarProveedores"
            class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#FCE7F3] transition">
            <div class="flex flex-col items-center">
                <div class="flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-magnifying-glass-plus text-[#9D174D] text-sm"></i>
                    <h3 class="text-sm font-medium text-[#9D174D] text-center">Buscar Nuevos Proveedores</h3>
                </div>
            </div>
        </button>
    </div>
    <!-- Carga del componente de tabla según la selección -->
    <div class="mt-4">
        @if ($tablaActual === 'mis-provedores-favoritos')
            @livewire('servicios.mis-provedores-favoritos')
        @elseif ($tablaActual === 'proveedores')
            @livewire('servicios.proveedores')
        @endif
    </div>
</div>

<div class="relative">
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 px-4 py-4">
        @if ($Empresa->tipo == 2 && !$rolTecnico)
            <!-- Mis Ordenes -->
            <button wire:click="mostrarMisOrdenes"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-[#2B008E] text-base"></i>
                        <span class="text-sm font-medium text-[#2B008E] whitespace-nowrap">Mis Ordenes</span>
                    </div>
                    @if (!$rolManager)
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $ordenesTrabajo }}</p>
                    @endif
                </div>
            </button>
            <!-- Sin Asignar -->
            <button wire:click="OrdenesSinAsignar"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-slash text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Sin Asignar</span>
                    </div>
                    @if (!$rolManager)
                        <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $ordenesNoAsignadas }}</p>
                    @endif
                </div>
            </button>
            <!-- Asignadas CERRADAS -->
            @if ($rolManager)
                <button wire:click="OrdenesClientes"
                    class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#FCE7F3] transition">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex items-center justify-center gap-2">
                            <i class="fa-solid fa-check-double text-[#9D174D] text-base"></i>
                            <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Asignadas CERRADAS</span>
                        </div>
                    </div>
                </button>
            @endif
        @elseif($Empresa->tipo == 2 && $rolTecnico)
            <!-- Ordenes Nuevas -->
            <button wire:click="mostrarMisOrdenes"
                class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#E0E7FF] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bell text-[#2B008E] text-base"></i>
                        <span class="text-sm font-medium text-[#2B008E] whitespace-nowrap">Ordenes Nuevas</span>
                    </div>
                </div>
            </button>
            <!-- Ordenes en Curso -->
            <button wire:click="OrdenesClientes"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-spinner text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Ordenes en Curso</span>
                    </div>
                </div>
            </button>
        @elseif ($Empresa->tipo == 1 && ($apoderado || $rol))
            <!-- Mis Ordenes (Gestora) -->
            <button wire:click="misOrdenesSolicitadas"
                class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#FCE7F3] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-file-signature text-[#9D174D] text-base"></i>
                        <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Mis Ordenes</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantOrdenesGestora }}</p>
                </div>
            </button>
            <!-- Pendientes -->
            <button wire:click="misOrdenesPendientes"
                class="ajax-link bg-[#FDF2F2] shadow-md rounded-lg p-3
            w-full sm:w-[48%] md:w-[30%] lg:w-[220px]
            hover:bg-[#FECACA] transition">
                <div class="flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-2">
                        <i class="fa-solid fa-hourglass-half text-[#B91C1C] text-base"></i>
                        <span class="text-sm font-medium text-[#B91C1C] whitespace-nowrap">Pendientes</span>
                    </div>
                    <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $cantOrdenesPendientes }}</p>
                </div>
            </button>
        @endif
    </div>
    <!-- CONTENIDO DE LAS TABLAS -->
    <div class="flex items-center space-x-4 mb-4">
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'mostrarMisOrdenes')
                @livewire('servicios.ordenes-de-trabajo.mis-ordenes')
            @elseif($tablaActual === 'OrdenesSinAsignar')
                @livewire('servicios.ordenes-de-trabajo.ordenes-sin-asignar')
            @elseif ($tablaActual === 'misOrdenesSolicitadas')
                @livewire('servicios.ordenes-de-trabajo.mis-ordenes-solicitadas')
            @elseif ($tablaActual === 'misOrdenesPendientes')
                @livewire('servicios.ordenes-de-trabajo.ordenes-gestora-pendientes')
            @elseif($tablaActual === 'OrdenesClientes')
                @livewire('servicios.ordenes-de-trabajo.ordenes-clientes')
            @endif
        </div>
    </div>
</div>

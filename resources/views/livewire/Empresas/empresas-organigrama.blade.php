<div class="relative">
    {{-- TABS siempre visibles --}}
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 px-4 py-4">
        {{-- Mis Niveles (CrearOrganizacion) --}}
        <button wire:click="CrearOrganizacion"
            class="ajax-link rounded-lg p-3 w-full sm:w-[48%] md:w-[30%] lg:w-[220px] transition shadow-md
                   {{ $tablaActual === 'CrearOrganizacion'
                       ? 'bg-[#EEF2FF] hover:bg-[#E0E7FF]'
                       : 'bg-white hover:bg-[#EEF2FF] border' }}">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-[#2B008E] text-base"></i>
                    <span class="text-sm font-medium text-[#2B008E] whitespace-nowrap">Mis Niveles</span>
                </div>
            </div>
        </button>

        {{-- Ver organigrama Actual (verOrganizacion) --}}
        <button wire:click="verOrganizacion"
            class="ajax-link rounded-lg p-3 w-full sm:w-[48%] md:w-[30%] lg:w-[220px] transition shadow-md
                   {{ $tablaActual === 'verOrganizacion'
                       ? 'bg-[#FDF2F8] hover:bg-[#FCE7F3]'
                       : 'bg-white hover:bg-[#FDF2F8] border' }}">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-sitemap text-[#9D174D] text-base"></i>
                    <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Ver organigrama Actual</span>
                </div>
            </div>
        </button>
    </div>

    {{-- CONTENIDO --}}
    <div class="flex items-center space-x-4 mb-4">
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'CrearOrganizacion')
                @livewire('empresas.empresas-organizacion', key('CrearOrganizacion'))
            @elseif ($tablaActual === 'verOrganizacion')
                @livewire('empresas.organigrama', key('verOrganizacion'))
            @endif
        </div>
    </div>
</div>

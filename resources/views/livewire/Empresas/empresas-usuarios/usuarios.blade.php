<div class="relative">
    <div class="flex flex-wrap justify-center lg:justify-start gap-4 mt-4 px-4">
        <!-- Usuarios Registrados -->
        <button wire:click="mostrarUsuariosRegistrados"
            class="ajax-link bg-[#EEF2FF] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#E0E7FF] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus text-[#2B008E] text-base"></i>
                    <span class="text-sm font-medium text-[#2B008E] whitespace-nowrap">Usuarios Registrados</span>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#2B008E]">{{ $usuariosRegistrados }}</p>
            </div>
        </button>
        <!-- Usuarios Activos -->
        <button wire:click="mostrarUsuariosActivos"
            class="ajax-link bg-[#FDF2F8] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#FCE7F3] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-check text-[#9D174D] text-base"></i>
                    <span class="text-sm font-medium text-[#9D174D] whitespace-nowrap">Usuarios Activos</span>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#9D174D]">{{ $usuariosActivos }}</p>
            </div>
        </button>
        <!-- Usuarios Sin Permisos -->
        <button wire:click="mostrarUsuariosSinPermisos"
            class="ajax-link bg-[#FEF2F2] shadow-md rounded-lg p-3 
        w-full sm:w-[48%] md:w-[30%] lg:w-[250px]
        hover:bg-[#FECACA] transition">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-slash text-[#B91C1C] text-base"></i>
                    <span class="text-sm font-medium text-[#B91C1C] whitespace-nowrap">Usuarios Sin Permisos</span>
                </div>
                <p class="mt-1 text-2xl font-bold text-[#B91C1C]">{{ $usuariosSinPermisos }}</p>
            </div>
        </button>
    </div>

    <div class="flex items-center space-x-4 mb-4">
        <!-- Campo de búsqueda con Alpine.js para debounce -->
        <div class="lg:w-full flex flex-col lg:h-full">
            @if ($tablaActual === 'usuarios-empresas')
                @livewire('empresas.empresas-usuarios.usuarios-empresas')
            @elseif($tablaActual === 'usuarios-activos')
                @livewire('empresas.empresas-usuarios.usuarios-activos')
            @elseif($tablaActual === 'usuarios-sin-permisos')
                @livewire('empresas.empresas-usuarios.usuarios-sin-permisos')
            @endif
        </div>
    </div>
</div>

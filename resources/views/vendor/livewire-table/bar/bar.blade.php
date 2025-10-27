<div class="flex items-center justify-between flex-nowrap gap-2">
    <!-- Título (desaparece en pantallas pequeñas) -->
    <div class="hidden lg:block">
        <h2 class="text-2xl font-semibold leading-tight text-gray-700">{{ $title }}</h2>
    </div>

    <!-- Campo de búsqueda y botones juntos en pantallas pequeñas -->
    <div class="flex-grow flex items-center gap-2 min-w-0">
        <!-- Campo de búsqueda que se ajusta al espacio disponible -->
        <div class="flex-shrink-0 flex items-center w-full lg:w-auto">
            <input type="search" 
                placeholder="Buscar {{ $title }}" 
                class="border border-neutral-200 shadow-sm rounded-md outline-none focus:border-blue-300 px-3 py-2 bg-white text-black transition-all ease-in-out duration-300 w-full lg:w-auto"
                style="min-width: 150px; max-width: 200px;" 
                wire:model.live.debounce.500ms="globalSearch">
        </div>
    </div>
    <div class="justify-center items-center w-full border-y border-transparent" wire:loading.flex>
        <span class="inline-block border border-4 border-blue-500 border-r-transparent motion-safe:animate-spin rounded-full my-2 p-2"></span>
    </div>

    <!-- Controles adicionales alineados a la derecha en escritorio -->
    <div class="flex items-center gap-2 lg:ml-auto flex-shrink-0 min-w-0 flex-nowrap">
        @if ($createForm)
            <!-- Botón que cambia dinámicamente entre texto completo en escritorio y solo ícono en pantallas pequeñas -->
            <div class="transition-all ease-in-out duration-300 flex-shrink-0">
                <!-- Botón que cambia entre ícono y texto según el tamaño de la pantalla -->
                <button 
                    class="bg-blue-500 text-white lg:px-4 lg:py-2 h-10 w-10 lg:w-auto flex justify-center items-center rounded-md shadow hover:bg-blue-600 transition-all ease-in-out duration-300"
                    wire:click="{{ $createForm }}">
                    <!-- Ícono "+" para pantallas pequeñas -->
                    <span class="block lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </span>

                    <span class="hidden lg:block">
                        Crear {{ $title }}
                    </span>
                </button>
            </div>
        @endif

        <!-- Otros controles adicionales con transición -->
        @includeWhen($this->useReordering, 'livewire-table::bar.buttons.reordering')
        @include('livewire-table::bar.dropdowns.polling')
        @include('livewire-table::bar.dropdowns.columns')
        @include('livewire-table::bar.dropdowns.filters')
        @include('livewire-table::bar.dropdowns.actions')
        @include('livewire-table::bar.dropdowns.trashed')

        <!-- Selección de registros por página -->
        <select wire:model.live="perPage"
            class="h-10 w-10 border border-neutral-200 shadow-sm rounded-md outline-none focus:border-blue-300 bg-white text-black transition-all ease-in-out duration-300">
            @foreach ($perPageOptions as $perPage)
                <option value="{{ $perPage }}">{{ $perPage }}</option>
            @endforeach
        </select>
    </div>
</div>

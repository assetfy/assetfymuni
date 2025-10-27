<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center space-x-3 text-center">
                <h2 class="font-semibold text-xl text-gray-800">Crear Atributo Subcategoría</h2>
            </div>
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Mostrar Livewire Create-Atributos si Obligatorio es Si -->
                @if ($obligatorio)
                    <div class="mb-4">
                        @livewire('atributos.create-atributos')
                    </div>
                @else
                    <!-- Dropdown de Atributo con Búsqueda -->
                    <div class="mb-4" x-data="{ openDropdownAtributo: false }" @click.away="openDropdownAtributo = false">
                        <x-label value="Atributo" />
                        <!-- Dropdown Personalizado con Campo de Búsqueda -->
                        <div class="relative">
                            <!-- Botón para abrir/cerrar el dropdown -->
                            <button type="button" @click="openDropdownAtributo = !openDropdownAtributo"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <span>
                                    @if ($id_atributo && $selectedAtributoNombre)
                                        {{ $selectedAtributoNombre }}
                                    @else
                                        Seleccione un Atributo
                                    @endif
                                </span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            <!-- Dropdown con Campo de Búsqueda y Opciones -->
                            <div x-show="openDropdownAtributo" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <!-- Campo de Búsqueda -->
                                    <input type="text" wire:model.live="searchAtributo"
                                        @keydown.enter="openDropdownAtributo = false"
                                        @keydown.escape="openDropdownAtributo = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        placeholder="Buscar Atributo...">
                                </div>
                                <ul
                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    @forelse ($atributosCargados ?? [] as $atributoCargado)
                                        <li wire:click="setAtributo({{ $atributoCargado->id_atributo }})"
                                            @click="openDropdownAtributo = false; $wire.set('searchAtributo', '');"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span
                                                class="font-normal block truncate">{{ $atributoCargado->nombre }}</span>
                                        </li>
                                    @empty
                                        <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                            No se encontraron Atributos.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <!-- Campo oculto para mantener la vinculación con Livewire -->
                        <input type="hidden" wire:model="id_atributo" />
                        <x-input-error for="id_atributo" />
                    </div>
                @endif

                <!-- Dropdown de Tipo con Búsqueda -->
                <div class="mb-4" x-data="{ openDropdownTipo: false }" @click.away="openDropdownTipo = false">
                    <x-label value="Tipo" />
                    <!-- Dropdown Personalizado con Campo de Búsqueda -->
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button" @click="openDropdownTipo = !openDropdownTipo"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_tipo && $selectedTipoNombre)
                                    {{ $selectedTipoNombre }}
                                @else
                                    Seleccione un Tipo
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <!-- Dropdown con Campo de Búsqueda y Opciones -->
                        <div x-show="openDropdownTipo" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <!-- Campo de Búsqueda -->
                                <input type="text" wire:model.live="searchTipo"
                                    @keydown.enter="openDropdownTipo = false" @keydown.escape="openDropdownTipo = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Tipo...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($tiposCargados ?? [] as $tipoCargado)
                                    <li wire:click="setTipo({{ $tipoCargado->id_tipo }})"
                                        @click="openDropdownTipo = false; $wire.set('searchTipo', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">{{ $tipoCargado->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Tipos.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <!-- Campo oculto para mantener la vinculación con Livewire -->
                    <input type="hidden" wire:model="id_tipo" />
                    <x-input-error for="id_tipo" />
                </div>
                <!-- Dropdown de Categoría con Búsqueda (Deshabilitado hasta seleccionar Tipo) -->
                <div class="mb-4" x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false">
                    <x-label value="Categoría" />
                    <!-- Dropdown Personalizado con Campo de Búsqueda -->
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button"
                            @click="if({{ $id_tipo ? 'true' : 'false' }}) openDropdownCategoria = !openDropdownCategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$id_tipo ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$id_tipo ? 'disabled' : '' }}>
                            <span>
                                @if ($id_categoria && $selectedCategoriaNombre)
                                    {{ $selectedCategoriaNombre }}
                                @else
                                    Seleccione una Categoría
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <!-- Dropdown con Campo de Búsqueda y Opciones -->
                        <div x-show="openDropdownCategoria" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <!-- Campo de Búsqueda -->
                                <input type="text" wire:model.live="searchCategoria"
                                    @keydown.enter="openDropdownCategoria = false"
                                    @keydown.escape="openDropdownCategoria = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Categoría..." {{ !$id_tipo ? 'disabled' : '' }}>
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($categoriasCargadas ?? [] as $categoriaCargada)
                                    <li wire:click="setCategoria({{ $categoriaCargada->id_categoria }})"
                                        @click="openDropdownCategoria = false; $wire.set('searchCategoria', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span
                                            class="font-normal block truncate">{{ $categoriaCargada->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Categorías.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <!-- Campo oculto para mantener la vinculación con Livewire -->
                    <input type="hidden" wire:model="id_categoria" />
                    <x-input-error for="id_categoria" />
                </div>
                <!-- Dropdown de Subcategoría con Búsqueda (Deshabilitado hasta seleccionar Categoría) -->
                <div class="mb-4" x-data="{ openDropdownSubcategoria: false }" @click.away="openDropdownSubcategoria = false">
                    <x-label value="Subcategoría" />
                    <!-- Dropdown Personalizado con Campo de Búsqueda -->
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button"
                            @click="if({{ $id_categoria ? 'true' : 'false' }}) openDropdownSubcategoria = !openDropdownSubcategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$id_categoria ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$id_categoria ? 'disabled' : '' }}>
                            <span>
                                @if ($id_subcategoria && $selectedSubcategoriaNombre)
                                    {{ $selectedSubcategoriaNombre }}
                                @else
                                    Seleccione una Subcategoría
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <!-- Dropdown con Campo de Búsqueda y Opciones -->
                        <div x-show="openDropdownSubcategoria" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <!-- Campo de Búsqueda -->
                                <input type="text" wire:model.live="searchSubcategoria"
                                    @keydown.enter="openDropdownSubcategoria = false"
                                    @keydown.escape="openDropdownSubcategoria = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Subcategoría..." {{ !$id_categoria ? 'disabled' : '' }}>
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($subcategoriasCargadas ?? [] as $subcategoriaCargada)
                                    <li wire:click="setSubcategoria({{ $subcategoriaCargada->id_subcategoria }})"
                                        @click="openDropdownSubcategoria = false; $wire.set('searchSubcategoria', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span
                                            class="font-normal block truncate">{{ $subcategoriaCargada->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Subcategorías.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <!-- Campo oculto para mantener la vinculación con Livewire -->
                    <input type="hidden" wire:model="id_subcategoria" />
                    <x-input-error for="id_subcategoria" />
                </div>

                <!-- Campo de Selección de Carga Inicial -->
                <div class="mb-4">
                    <x-label value="Carga inicial" />
                    <select class="form-control" wire:model.lazy="obligatorio_carga_ini">
                        <option value="" hidden>Seleccione una opción</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="obligatorio_carga_ini" />
                </div>
                <!-- Campo de Selección de Si es Único -->
                <div class="mb-4">
                    <x-label value="Seleccionar si es único" />
                    <select class="form-control" wire:model.lazy="unico">
                        <option value="" hidden>Seleccione una opción</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="unico" />
                </div>
            </x-slot>
        </form>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="close">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                Crear Atributo-Subcategoría
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="save">
                Cargando...
            </span>
        </x-slot>
    </x-dialog-modal>
</div>

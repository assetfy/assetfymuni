<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            Crear Servicio Subcategoria
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Dropdown de Servicio -->
                <div class="mb-4" x-data="{ openDropdownServicio: false }" @click.away="openDropdownServicio = false">
                    <x-label value="Servicio" />
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button" @click="openDropdownServicio = !openDropdownServicio"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_servicio && $servicios->where('id_servicio', $id_servicio)->first())
                                    {{ $servicios->where('id_servicio', $id_servicio)->first()->nombre }}
                                @else
                                    Seleccione un Servicio
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
                        <div x-show="openDropdownServicio" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <!-- Campo de Búsqueda -->
                                <input type="text" wire:model.live="searchServicio"
                                    @keydown.enter="openDropdownServicio = false"
                                    @keydown.escape="openDropdownServicio = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Servicio...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($servicios as $servicio)
                                    <li wire:click="setServicio({{ $servicio->id_servicio }})"
                                        @click="openDropdownServicio = false; $wire.set('searchServicio', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">{{ $servicio->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Servicios.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <x-input-error for="id_servicio" />
                </div>
                <!-- Dropdown de Tipo -->
                <div class="mb-4" x-data="{ openDropdownTipo: false }" @click.away="openDropdownTipo = false">
                    <x-label value="Tipo" />
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button" @click="openDropdownTipo = !openDropdownTipo"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_tipo && $tipos->where('id_tipo', $id_tipo)->first())
                                    {{ $tipos->where('id_tipo', $id_tipo)->first()->nombre }}
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
                                    @keydown.enter="openDropdownTipo = false"
                                    @keydown.escape="openDropdownTipo = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Tipo...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($tipos as $tipo)
                                    <li wire:click="setTipo({{ $tipo->id_tipo }})"
                                        @click="openDropdownTipo = false; $wire.set('searchTipo', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">{{ $tipo->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Tipos.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <x-input-error for="id_tipo" />
                </div>
                <!-- Dropdown de Categoría (Deshabilitado hasta seleccionar Tipo) -->
                <div class="mb-4" x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false">
                    <x-label value="Categoría" />
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button"
                            @click="if({{ $id_tipo ? 'true' : 'false' }}) openDropdownCategoria = !openDropdownCategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$id_tipo ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$id_tipo ? 'disabled' : '' }}>
                            <span>
                                @if ($id_categoria && $categorias->where('id_categoria', $id_categoria)->first())
                                    {{ $categorias->where('id_categoria', $id_categoria)->first()->nombre }}
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
                                @forelse ($categorias as $categoria)
                                    <li wire:click="setCategoria({{ $categoria->id_categoria }})"
                                        @click="openDropdownCategoria = false; $wire.set('searchCategoria', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">{{ $categoria->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Categorías.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <x-input-error for="id_categoria" />
                </div>
                <!-- Dropdown de Subcategoría (Deshabilitado hasta seleccionar Categoría) -->
                <div class="mb-4" x-data="{ openDropdownSubcategoria: false }" @click.away="openDropdownSubcategoria = false">
                    <x-label value="Subcategoría" />
                    <div class="relative">
                        <!-- Botón para abrir/cerrar el dropdown -->
                        <button type="button"
                            @click="if({{ $id_categoria ? 'true' : 'false' }}) openDropdownSubcategoria = !openDropdownSubcategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$id_categoria ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$id_categoria ? 'disabled' : '' }}>
                            <span>
                                @if ($id_subcategoria && $subcategorias->where('id_subcategoria', $id_subcategoria)->first())
                                    {{ $subcategorias->where('id_subcategoria', $id_subcategoria)->first()->nombre }}
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
                                @forelse ($subcategorias as $subcategoria)
                                    <li wire:click="setSubcategoria({{ $subcategoria->id_subcategoria }})"
                                        @click="openDropdownSubcategoria = false; $wire.set('searchSubcategoria', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">{{ $subcategoria->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Subcategorías.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <x-input-error for="id_subcategoria" />
                </div>
                <!-- Otros Campos del Formulario -->
                <div class="mb-4">
                    <x-label value="Requiere foto" />
                    <select class="form-control w-full border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        wire:model.lazy="req_fotos_carga_inicial">
                        <option value="" hidden>Seleccione una Opción</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="req_fotos_carga_inicial" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button  wire:click="save" wire:loading.remove wire:target="save">
                    Crear Servicio Subcategoria
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">Cargando...</span>
            </x-slot>
        </x-dialog-modal>
    </div>

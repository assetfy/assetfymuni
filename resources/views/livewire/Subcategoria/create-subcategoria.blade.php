<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <!-- TÍTULO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Crear Subcategoría</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <form wire:submit.prevent="save" class="space-y-6 px-4 py-4">
                <!-- Dropdown de Tipo -->
                <div class="mb-4" x-data="{ openDropdownTipo: false }" @click.away="openDropdownTipo = false">
                    <x-label value="Tipo" />
                    <div class="relative">
                        <button type="button" @click="openDropdownTipo = !openDropdownTipo"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                        <div x-show="openDropdownTipo" x-transition
                            class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchTipo"
                                    @keydown.enter="openDropdownTipo = false" @keydown.escape="openDropdownTipo = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Tipo...">
                            </div>
                            <ul
                                class="max-h-60 overflow-auto rounded-md py-1 text-base ring-1 ring-black ring-opacity-5">
                                @forelse ($tipoPrueba ?? [] as $tipoCargado)
                                    <li wire:click="setTipo({{ $tipoCargado->id_tipo }})"
                                        @click="openDropdownTipo = false; $wire.set('searchTipo', '')"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="block truncate">{{ $tipoCargado->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Tipos.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" wire:model="id_tipo" />
                    <x-input-error for="id_tipo" />
                </div>

                <!-- Dropdown de Categoría -->
                <div class="mb-4" x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false">
                    <x-label value="Categoría" />
                    <div class="relative">
                        <button type="button"
                            @click="if({{ $id_tipo ? 'true' : 'false' }}) openDropdownCategoria = !openDropdownCategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm {{ !$id_tipo ? 'opacity-50 cursor-not-allowed' : '' }}"
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
                        <div x-show="openDropdownCategoria" x-transition
                            class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchCategoria"
                                    @keydown.enter="openDropdownCategoria = false"
                                    @keydown.escape="openDropdownCategoria = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    placeholder="Buscar Categoría..." {{ !$id_tipo ? 'disabled' : '' }}>
                            </div>
                            <ul
                                class="max-h-60 overflow-auto rounded-md py-1 text-base ring-1 ring-black ring-opacity-5">
                                @forelse ($categorias2 ?? [] as $categoriaCargada)
                                    <li wire:click="setCategoria({{ $categoriaCargada->id_categoria }})"
                                        @click="openDropdownCategoria = false; $wire.set('searchCategoria', '')"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="block truncate">{{ $categoriaCargada->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Categorías.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" wire:model="id_categoria" />
                    <x-input-error for="id_categoria" />
                </div>

                <!-- Campo de Nombre de la Subcategoría -->
                <div class="mb-4">
                    <x-label value="Nombre de la Subcategoría" />
                    <x-input type="text" class="w-full" wire:model.lazy="nombre" />
                    <x-input-error for="nombre" />
                </div>

                <!-- Campo de Sigla -->
                <div class="mb-4">
                    <x-label value="Sigla" />
                    <x-input type="text" class="w-full" wire:model.lazy="sigla" />
                    <x-input-error for="sigla" />
                </div>

                <!-- Campo de "Se Relaciona" -->
                <div class="mb-4">
                    <x-label value="Se relaciona" />
                    <select
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        wire:model.lazy="se_relaciona">
                        <option value="" selected>Seleccione una opción</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                    <x-input-error for="se_relaciona" />
                </div>

                <!-- Campo de "Móvil o Fijo" -->
                <div class="mb-4">
                    <x-label value="Móvil o Fijo" />
                    <select
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        wire:model.lazy="movil_o_fijo">
                        <option value="" selected>Seleccione una opción</option>
                        <option value="Movil">Móvil</option>
                        <option value="Fijo">Fijo</option>
                    </select>
                    <x-input-error for="movil_o_fijo" />
                </div>

                <!-- Campo de Descripción -->
                <div class="mb-4">
                    <x-label value="Descripción" />
                    <x-input type="text" class="w-full" wire:model.lazy="descripcion" />
                    <x-input-error for="descripcion" />
                </div>

                <!-- Contenedor de Carga de Foto con Vista Previa -->
                <div id="foto-container" class="mb-4 space-y-4">
                    <x-label value="Adjuntar Foto:" />
                    <div class="flex items-center justify-center w-32 h-32 bg-gray-50 rounded-lg border-2 border-dashed border-gray-400 cursor-pointer hover:bg-gray-100 transition"
                        onclick="document.getElementById('imagenSubcategoria').click()">
                        @if ($imagenSubcategoria)
                            <div class="relative w-full h-full">
                                <img src="{{ $imagenSubcategoria->temporaryUrl() }}"
                                    class="object-cover w-full h-full rounded-lg">
                                <button type="button"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                    wire:click="removeImagen">
                                    &times;
                                </button>
                            </div>
                        @else
                            <span class="text-2xl text-gray-500">+</span>
                        @endif
                    </div>
                    <!-- Input de archivo oculto -->
                    <input type="file" id="imagenSubcategoria" class="hidden" wire:model="imagenSubcategoria">
                    <x-input-error for="imagenSubcategoria" class="text-sm text-red-500" />
                    <div wire:loading wire:target="imagenSubcategoria" class="text-sm text-blue-500 mt-1">
                        Cargando foto...
                    </div>
                </div>
            </form>
        </x-slot>

        <!-- FOOTER -->
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Subcategoría
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">Cargando...</span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

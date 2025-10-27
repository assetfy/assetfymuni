<div class="w-full min-h-screen bg-white p-10">
    <!-- Encabezado -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-blue-700">Delegación de mis bienes</h2>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="space-y-4">

            <div class="mb-4" x-data="{ openDropdownTipo: false, tipoBloqueado: {{ $tipoInicial ? 'true' : 'false' }} }" @click.away="openDropdownTipo = false">
                <x-label value="Tipo" />
                <div class="relative">
                    <template x-if="tipoBloqueado">
                        <div
                            class="w-full bg-gray-100 border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left sm:text-sm cursor-not-allowed">
                            <span>{{ $selectedTipoNombre }}</span>
                        </div>
                    </template>
                    <template x-if="!tipoBloqueado">
                        <div>
                            <button type="button" @click="openDropdownTipo = !openDropdownTipo"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-sm">
                                <span>
                                    @if ($selectedTipoNombre)
                                        {{ $selectedTipoNombre }}
                                    @else
                                        Seleccione un Tipo
                                    @endif
                                </span>
                                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 01.832.445l3-5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 17z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </button>
                            <div x-show="openDropdownTipo" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchTipo"
                                        @keydown.enter="openDropdownTipo = false"
                                        @keydown.escape="openDropdownTipo = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                        placeholder="Buscar Tipo...">
                                </div>
                                <ul
                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    @forelse ($tipos ?? [] as $tipoCargado)
                                        <li wire:click="setTipo({{ $tipoCargado->id_tipo }})"
                                            @click="openDropdownTipo = false; tipoBloqueado = false;"
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
                    </template>
                </div>
                <input type="hidden" wire:model="id_tipo" />
                <x-input-error for="id_tipo" />
            </div>
        </div>

        <div class="space-y-4">
            <!-- Dropdown Categoría -->
            <div class="mb-4" x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false">
                <x-label value="Categoría" />
                <div class="relative">
                    <button type="button"
                        @click="if({{ $id_tipo ? 'true' : 'false' }}) openDropdownCategoria = !openDropdownCategoria"
                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-sm {{ !$id_tipo ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$id_tipo ? 'disabled' : '' }}>
                        <span>
                            @if ($id_categoria && $selectedCategoriaNombre)
                                {{ $selectedCategoriaNombre }}
                            @else
                                Seleccione una Categoría
                            @endif
                        </span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 01.832.445l3-5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 17z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    <div x-show="openDropdownCategoria" x-transition
                        class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                        <div class="p-2">
                            <input type="text" wire:model.live="searchCategoria"
                                @keydown.enter="openDropdownCategoria = false"
                                @keydown.escape="openDropdownCategoria = false"
                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                placeholder="Buscar Categoría..." {{ !$id_tipo ? 'disabled' : '' }}>
                        </div>
                        <ul
                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            @forelse ($categorias2 ?? [] as $categoriaCargada)
                                <li wire:click="setCategoria({{ $categoriaCargada->id_categoria }})"
                                    @click="openDropdownCategoria = false; $wire.set('searchCategoria', '');"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                    <span class="font-normal block truncate">{{ $categoriaCargada->nombre }}</span>
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
        </div>

        <div class="space-y-4">
            <!-- Dropdown Subcategoría -->
            <div class="mb-4" x-data="{ openDropdownSubcategoria: false }" @click.away="openDropdownSubcategoria = false">
                <x-label value="Subcategoría" />
                <div class="relative">
                    <button type="button"
                        @click="if({{ $id_categoria ? 'true' : 'false' }}) openDropdownSubcategoria = !openDropdownSubcategoria"
                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-sm {{ !$id_categoria || $subcategoria->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ !$id_categoria || $subcategoria->isEmpty() ? 'disabled' : '' }}>
                        <span>
                            @if ($id_subcategoria && $selectedSubcategoriaNombre)
                                {{ $selectedSubcategoriaNombre }}
                            @else
                                @if (!$id_categoria)
                                    Seleccione una Categoría primero
                                @elseif ($subcategoria->isEmpty())
                                    No hay subcategorías disponibles
                                @else
                                    Seleccione una Subcategoría
                                @endif
                            @endif
                        </span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 01.832.445l3-5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 17z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>
                    @if ($id_categoria)
                        <div x-show="openDropdownSubcategoria" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchSubcategoria"
                                    @keydown.enter="openDropdownCategoria = false"
                                    @keydown.escape="openDropdownCategoria = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                    placeholder="Buscar Subcategoria..." {{ !$id_tipo ? 'disabled' : '' }}>
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                @if ($id_categoria && !$subcategoria->isEmpty())
                                    @forelse ($subcategoria as $subcategoriaCargada)
                                        <li wire:click="setSubcategoria({{ $subcategoriaCargada->id_subcategoria }})"
                                            @click="openDropdownSubcategoria = false; $wire.set('searchSubcategoria', '');"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span
                                                class="font-normal block truncate">{{ $subcategoriaCargada->nombre }}</span>
                                        </li>
                                    @empty
                                        <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                            No hay subcategorías disponibles.
                                        </li>
                                    @endforelse
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>
                <input type="hidden" wire:model="id_subcategoria" />
                <x-input-error for="id_subcategoria" />
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="w-1/2">
            <h3 class="text-md font-semibold text-gray-600 mb-3">Seleccionar Activos</h3>
            <div class="flex flex-wrap gap-4">
                @foreach ($activos as $activo)
                    <div wire:key="activo-{{ $activo->id_activo }}" class="flex items-center">
                        <input type="checkbox" wire:model="activosSeleccionados.{{ $activo->id_activo }}"
                            value="{{ $activo->id_activo }}" id="activo-{{ $activo->id_activo }}"
                            class="h-4 w-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500" />
                        <label for="activo-{{ $activo->id_activo }}" class="ml-2 text-gray-700">
                            {{ $activo->nombre }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <!-- Contenedor del Dropdown -->
        <div class="w-1/2" x-data="{ openDropdownProveedor: false }" @click.away="openDropdownProveedor = false">
            <x-label value="Proveedores" />
            <div class="relative">
                <button type="button" @click="openDropdownProveedor = !openDropdownProveedor"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-sm {{ $proveedores->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $proveedores->isEmpty() ? 'disabled' : '' }}>
                    <span>
                        @if ($id_proveedor && $selectedProveedorNombre)
                            {{ $selectedProveedorNombre->razon_social }}
                        @else
                            @if ($proveedores->isEmpty())
                                No hay proveedores disponibles
                            @else
                                Seleccione un Proveedor
                            @endif
                        @endif
                    </span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 01.832.445l3-5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3-5A1 1 0 0110 17z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <div x-show="openDropdownProveedor" x-transition
                    class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                    <div class="p-2">
                        <input type="text" wire:model.live="searchProveedor"
                            @keydown.enter="openDropdownProveedor = false"
                            @keydown.escape="openDropdownProveedor = false"
                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                            placeholder="Buscar Proveedor...">
                    </div>
                    <ul
                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                        @forelse ($proveedores as $proveedor)
                            <li wire:click="setProveedor({{ $proveedor->id }})"
                                @click="openDropdownProveedor = false; $wire.set('searchProveedor', '');"
                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                <span class="font-normal block truncate">{{ $proveedor->razon_social }}</span>
                            </li>
                        @empty
                            <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                No hay proveedores disponibles.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="id_proveedor" />
            <x-input-error for="id_proveedor" />
        </div>

        <!-- Recuadro con los datos del proveedor -->
        <div class="w-1/2">
            @if ($selectedProveedorNombre)
                <div class="border border-gray-300 rounded-md p-4 bg-gray-100 shadow-md">
                    <p class="text-lg font-semibold text-gray-700">Detalles del Proveedor</p>
                    <p><strong>CUIT:</strong> {{ $selectedProveedorNombre->cuit }}</p>
                    <p><strong>Razón Social:</strong> {{ $selectedProveedorNombre->razon_social }}</p>
                    <p><strong>Localidad:</strong> {{ $selectedProveedorNombre->localidad }}</p>
                    <p><strong>Provincia:</strong> {{ $selectedProveedorNombre->provincia }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Botón para delegar bienes -->
    <div class="mt-6">
        <button wire:click="confirmarDelegacion"
            class="bg-blue-500 text-white py-2 px-4 rounded-md focus:outline-none hover:bg-blue-600">
            Delegar Bienes
        </button>
    </div>
</div>

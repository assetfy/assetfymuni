<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-layer-group text-cyan-600"></i> Asignación Inicial del Activo
    </h2>

    @if ($origen == 'bienes_aceptados')
        <!-- Selección de Empresa -->
        <div class="mb-4" x-data="{ openDropdownEmpresa: false }" @click.away="openDropdownEmpresa = false">
            <x-label value="Empresas" />
            <div class="relative">
                <button type="button" @click="openDropdownEmpresa = !openDropdownEmpresa"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-10 pr-10 py-2 text-left cursor-pointer 
                        focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm flex items-center">
                    <i class="fa-solid fa-building text-gray-400 absolute left-3"></i>
                    <span class="ml-2">
                        @if ($cuit && $empresaBusqueda)
                            {{ $empresaBusqueda->razon_social }}
                        @else
                            Seleccione una Empresa
                        @endif
                    </span>
                </button>
                <div x-show="openDropdownEmpresa" x-transition
                    class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                    <div class="p-2">
                        <input type="text" wire:model.live="searchEmpresas"
                            @keydown.enter="openDropdownEmpresa = false" @keydown.escape="openDropdownEmpresa = false"
                            class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Buscar Empresa...">
                    </div>
                    <ul
                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm">
                        @if (!$filteredEmpresas->isEmpty())
                            @foreach ($filteredEmpresas as $empresa)
                                <li wire:click="setCuitEmpresa('{{ $empresa->cuit }}')"
                                    @click="openDropdownEmpresa = false; @this.set('searchEmpresas', '');"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                    <span class="block truncate">{{ $empresa->razon_social }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                        @endif
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="empresa_titular" />
            <x-input-error for="empresa_titular" />
        </div>

        <!-- Ubicación (si empresa tiene ubicaciones) -->
        @if ($empresaBusqueda && $ubicacionesEmpresa->isNotEmpty())
            <div class="mb-4" x-data="{ openDropdownUbicacion: false }" @click.away="openDropdownUbicacion = false">
                <x-label value="Ubicación" />
                <div class="relative">
                    <button type="button" @click="openDropdownUbicacion = !openDropdownUbicacion"
                        class="mt-1 block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pl-10 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 flex items-center justify-between">
                        <i class="fa-solid fa-map-marker-alt absolute left-3 text-gray-400"></i>
                        <span class="ml-2">
                            @if ($id_ubicacion && $selectedUbicacionNombre)
                                {{ $selectedUbicacionNombre }}
                            @else
                                Seleccione una Ubicación
                            @endif
                        </span>
                        <svg class="h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M5.516 7.548l4.484 4.484 4.484-4.484L16 8.032l-6 6-6-6z" />
                        </svg>
                    </button>

                    <div x-show="openDropdownUbicacion" x-transition
                        class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                        <div class="p-2">
                            <input type="text" wire:model="searchUbicacionDelegada"
                                @keydown.enter="openDropdownUbicacion = false"
                                @keydown.escape="openDropdownUbicacion = false"
                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Buscar Ubicación...">
                        </div>
                        <ul
                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto sm:text-sm">
                            @foreach ($ubicacionesEmpresa as $ubicacion)
                                <li wire:click="setUbicacionDelegada({{ $ubicacion->id_ubicacion }})"
                                    @click="openDropdownUbicacion = false; $wire.set('searchUbicacionDelegada', '');"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                    <span class="block truncate">{{ $ubicacion->nombre }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <input type="hidden" wire:model="id_ubicacion" />
                <x-input-error for="id_ubicacion" />
            </div>
        @endif
        @if ($pisosOpcion)
            <!-- Piso -->
            <div class="mb-4">
                <x-label for="selectedPiso" value="Planta / Piso donde se encuentra el bien" />
                <div class="relative">
                    <i
                        class="fa-solid fa-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    <select id="selectedPiso" wire:model="selectedPiso"
                        class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="" hidden>Seleccione la planta o piso</option>
                        @foreach ($pisosOpcion as $piso)
                            <option value="{{ $piso }}">{{ $piso }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error for="selectedPiso" />
            </div>
        @endif
    @endif

    <!-- Nombre del Activo -->
    <div class="mb-4">
        <x-label value="Nombre del Activo" />
        <div class="relative">
            <i class="fa-solid fa-box absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <x-input type="text"
                class="mt-1 block w-full px-4 py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                wire:model.lazy="nombre" />
        </div>
        <x-input-error for="nombre" />
    </div>

    <!-- Tipo -->
    @if ($tipoInicial)
        <div class="mb-4">
            <x-label value="Tipo" />
            <div class="relative">
                <i class="fa-solid fa-tags absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md pl-10 pr-4 py-2 cursor-not-allowed text-gray-600"
                    value="{{ $selectedTipoNombre }}" disabled />
            </div>
        </div>
    @else
        <x-searchable-dropdown label="Tipo" icon="fa-solid fa-tags" :options="$tipoPrueba" model="id_tipo"
            search-model="searchTipo" select-method="setTipo" value-key="id_tipo" label-key="nombre"
            :selected="$selectedTipoNombre" />
    @endif

    <!-- Categoría -->
    @if (!$id_tipo)
        <div class="mb-4">
            <x-label value="Categoría" />
            <div class="relative">
                <i class="fa-solid fa-folder absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md pl-10 pr-4 py-2 cursor-not-allowed text-gray-600"
                    value="Seleccione un Tipo primero" disabled />
            </div>
        </div>
    @else
        <x-searchable-dropdown label="Categoría" icon="fa-solid fa-folder" :options="$categorias" model="id_categoria"
            search-model="searchCategoria" select-method="setCategoria" value-key="id_categoria" label-key="nombre"
            :selected="$selectedCategoriaNombre" />
    @endif

    <!-- Subcategoría -->
    @if (!$id_categoria)
        <div class="mb-4">
            <x-label value="Subcategoría" />
            <div class="relative">
                <i class="fa-solid fa-folder-open absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text"
                    class="mt-1 block w-full bg-gray-100 border border-gray-300 rounded-md pl-10 pr-4 py-2 cursor-not-allowed text-gray-500"
                    value="Seleccione primero una categoría" disabled />
            </div>
        </div>
    @else
        <x-searchable-dropdown label="Subcategoría" icon="fa-solid fa-folder-open" :options="$subcategoria"
            model="id_subcategoria" search-model="searchSubcategoria" select-method="setSubcategoria"
            value-key="id_subcategoria" label-key="nombre" :selected="$selectedSubcategoriaNombre" />
    @endif

    <!-- Error general de paso -->
    @if ($errors->has('step1'))
        <div class="text-red-500 font-semibold mt-2">{{ $errors->first('step1') }}</div>
    @endif

    <!-- UBICACIÓN DEL BIEN -->
    @if ($origen != 'bienes_aceptados' && !$inmueble)
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-location-dot text-blue-500"></i> Ubicación del Bien <span
                    class="text-sm text-gray-500 font-normal">(Opcional)</span>
            </h2>

            <!-- Dropdown de Ubicación -->
            <div class="mb-2">
                <x-searchable-dropdown label="Ubicación" icon="fa-solid fa-map-marker-alt" :options="$ubicacionesList"
                    model="id_ubicacion" search-model="searchUbicacion" select-method="setUbicacion"
                    value-key="id_ubicacion" label-key="nombre" :selected="$selectedUbicacionNombre ?? 'Sin Ubicación'" />
            </div>

            <!-- Botón Nueva Ubicación abajo, alineado a la izquierda -->
            <div class="mb-4">
                <button wire:click="crearUbicaciones"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md px-4 py-2 flex items-center gap-2 shadow">
                    <i class="fas fa-plus"></i> Nueva Ubicación
                </button>
            </div>

            @if ($pisosOpcion)
                <!-- Piso -->
                <div>
                    <x-label for="selectedPiso" value="Planta / Piso donde se encuentra el bien" />
                    <div class="relative">
                        <i
                            class="fa-solid fa-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        <select id="selectedPiso" wire:model="selectedPiso"
                            class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="" hidden>Seleccione la planta o piso</option>
                            @foreach ($pisosOpcion as $piso)
                                <option value="{{ $piso }}">{{ $piso }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input-error for="selectedPiso" />
                </div>
            @endif
        </div>
    @endif

</div>

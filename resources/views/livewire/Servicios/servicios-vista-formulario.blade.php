<div class="w-full min-h-screen bg-white p-10">
    <h2 class="text-2xl font-semibold text-blue-700">Solicitud de Cotización</h2>

    <!-- Formulario -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nombre de la Solicitud -->
        <div>
            <label for="nombre_solicitud" class="block text-sm font-medium text-gray-700">Titulo de la Solicitud</label>
            <input type="text" id="nombre_solicitud" wire:model="nombre_solicitud"
                class="w-full mt-1 p-2 text-sm border border-blue-400 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none">
            <x-input-error for="nombre_solicitud" class="mt-1 text-red-500 text-sm" />
        </div>

        <!-- Tipo de Servicio -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Tipo de Servicio</label>
            <div class="flex items-center space-x-6 mt-2">
                @foreach ($tiposServicio as $tipo)
                    <label class="flex items-center">
                        <input type="radio" wire:model="selectedTipoServicio" value="{{ $tipo->id_tipo_solicitud }}"
                            class="h-5 w-5 text-blue-600 border-gray-300 focus:ring focus:ring-blue-400">
                        <span class="ml-2 text-gray-700">{{ $tipo->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <!-- Fecha y Hora -->
        <div class="space-y-4">
            <label for="fechaHora" class="block font-medium text-gray-700">Fecha y Hora:</label>
            <input type="datetime-local" id="fechaHora" wire:model.live="fechaHora" min="{{ date('Y-m-d\TH:i') }}"
                class="input-lg mt-1 block w-60 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3" />
            @error('fechaHora')
                <x-input-error for="fechaHora" class="mt-1 text-red-500 text-sm" />
            @enderror
        </div>

    </div>

    <!-- Descripción de la Solicitud -->
    <div class="mt-6">
        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción de la Solicitud</label>
        <textarea id="descripcion" wire:model="descripcion" rows="4"
            class="w-full mt-1 p-2 text-sm border border-blue-400 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none"></textarea>
        @error('descripcion')
            <x-input-error for="descripcion" class="mt-1 text-red-500 text-sm" />
        @enderror
    </div>

    <!-- Dropdowns de Activo: Tipo, Categoría y Subcategoría -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Dropdown Tipo Activo --}}
        <div x-data="{ openTipo: false }" @click.away="openTipo = false" class="mb-4">
            <x-label value="Tipo Bien" />
            <div class="relative">
                <!-- El botón siempre habilitado -->
                <button type="button" @click="openTipo = !openTipo"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    {{ $selectedTipoNombre ?? 'Seleccione un Tipo' }}
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.586l3.71-4.356a.75.75 0 111.14.98l-4.25 5a.75.75 0 01-1.14 0l-4.25-5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <!-- Lista -->
                <div x-show="openTipo" x-transition
                    class="absolute mt-1 w-full bg-white border rounded-md shadow-lg z-10">
                    <div class="p-2">
                        <input type="text" wire:model.live="searchTipo" @keydown.enter="openTipo = false"
                            @keydown.escape="openTipo = false"
                            class="w-full border rounded px-2 py-1 mb-2 focus:outline-none"
                            placeholder="Buscar Tipo..." />
                    </div>
                    <ul class="max-h-40 overflow-auto">
                        @if (!empty($tipoPrueba) && $tipoPrueba->count())
                            @foreach ($tipoPrueba as $t)
                                <li wire:click="setTipo({{ $t->id_tipo }})" @click="openTipo = false"
                                    class="px-3 py-2 hover:bg-blue-100 cursor-pointer truncate">
                                    {{ $t->nombre }}
                                </li>
                            @endforeach
                        @else
                            <li class="px-3 py-2 text-gray-500">No hay tipos.</li>
                        @endif
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="id_tipo" />
            <x-input-error for="id_tipo" />
        </div>


        <!-- Dropdown Categoría Activo -->
        <div x-data="{ openCat: false }" @click.away="openCat = false">
            <x-label value="Categoría Bien" />
            <div class="relative">
                <button type="button" @click="if({{ $id_tipo ? 'true' : 'false' }}) openCat = !openCat"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                {{ $id_tipo ? 'border border-gray-300' : 'border border-gray-200 opacity-50 cursor-not-allowed' }}"
                    {{ $id_tipo ? '' : 'disabled' }}>
                    {{ $selectedCategoriaNombre ?? 'Seleccione una Categoría' }}
                </button>
                <div x-show="openCat" x-transition
                    class="absolute mt-1 w-full bg-white border rounded-md shadow-lg z-10">
                    <div class="p-2">
                        <input type="text" wire:model.live="searchCategoria" @keydown.enter="openCat = false"
                            @keydown.escape="openCat = false"
                            class="w-full border rounded px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Buscar Categoría..." />
                    </div>
                    <ul class="max-h-40 overflow-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                        @if (!empty($categorias2) && count($categorias2))
                            @foreach ($categorias2 as $c)
                                <li wire:click="setCategoria({{ $c->id_categoria }})" @click="openCat=false"
                                    class="px-3 py-2 hover:bg-blue-100 cursor-pointer truncate">
                                    {{ $c->nombre }}
                                </li>
                            @endforeach
                        @else
                            <li class="px-3 py-2 text-gray-500">No hay categorías.</li>
                        @endif
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="id_categoria" />
            <x-input-error for="id_categoria" />
        </div>

        <!-- Dropdown Subcategoría Activo -->
        <div x-data="{ openSub: false }" @click.away="openSub = false">
            <x-label value="Subcategoría Bien" />
            <div class="relative">
                <button type="button"
                    @click="if({{ $id_categoria && $subcategoria->isNotEmpty() ? 'true' : 'false' }}) openSub = !openSub"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                    {{ $id_categoria && $subcategoria->isNotEmpty() ? 'border border-gray-300' : 'border border-gray-200 opacity-50 cursor-not-abled' }}"
                    {{ $id_categoria && $subcategoria->isNotEmpty() ? '' : 'disabled' }}>
                    @if ($selectedSubcategoriaNombre)
                        {{ $selectedSubcategoriaNombre }}
                    @elseif(!$id_categoria)
                        Seleccione una Categoría primero
                    @else
                        No hay subcategorías
                    @endif
                </button>
                <div x-show="openSub" x-transition
                    class="absolute mt-1 w-full bg-white border rounded-md shadow-lg z-10">
                    <div class="p-2">
                        <input type="text" wire:model.live="searchSubcategoria" @keydown.enter="openSub = false"
                            @keydown.escape="openSub = false"
                            class="w-full border rounded px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Buscar Subcategoría..." />
                    </div>
                    <ul class="max-h-40 overflow-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                        @if (!empty($subcategoria) && count($subcategoria))
                            @foreach ($subcategoria as $s)
                                <li wire:click="setSubcategoria({{ $s->id_subcategoria }})" @click="openSub=false"
                                    class="px-3 py-2 hover:bg-blue-100 cursor-pointer truncate">
                                    {{ $s->nombre }}
                                </li>
                            @endforeach
                        @else
                            <li class="px-3 py-2 text-gray-500">No hay subcategorías.</li>
                        @endif
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="id_subcategoria" />
            <x-input-error for="id_subcategoria" />
        </div>
    </div>

    <!-- Activos -->
    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        <div class="" x-data="{ openDropdownActivo: false }" @click.away="openDropdownActivo = false">
            <x-label value="Nombre del Bien" />

            <div class="relative">
                <button type="button" @click="openDropdownActivo = !openDropdownActivo"
                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <span>{{ $activoBusqueda->nombre ?? 'Seleccione un Bien' }}</span>
                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>

                <div x-show="openDropdownActivo" x-transition
                    class="absolute mt-1 w-full bg-white rounded-md shadow-lg z-10" style="min-width: 16rem;">
                    <div class="flex items-center p-2 border-b border-gray-200">
                        <button wire:click="crearActivos"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold rounded-full h-8 w-8 flex items-center justify-center text-xl mr-2"
                            title="Crear Bien">+</button>
                        <input type="text" wire:model.live="searchActivo"
                            @keydown.enter="openDropdownActivo = false" @keydown.escape="openDropdownActivo = false"
                            class="flex-1 border border-blue-400 rounded-md px-2 py-1 focus:outline-none"
                            placeholder="Buscar Activo..." />
                    </div>
                    <ul class="max-h-60 overflow-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                        @if ($filteredActivos->isEmpty())
                            <li class="px-3 py-2 text-gray-500">Sin resultados</li>
                        @else
                            @foreach ($filteredActivos as $activo)
                                <li wire:click="setIdActivo({{ $activo->id_activo }})"
                                    @click="openDropdownActivo = false; @this.set('searchActivo','')"
                                    class="px-3 py-2 hover:bg-blue-100 cursor-pointer truncate">{{ $activo->nombre }}
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <input type="hidden" wire:model="id_activo" />
            <x-input-error for="id_activo" class="mt-1 text-red-500 text-sm" />
        </div>
        <!-- Resumen del Activo -->
    </div>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        <div class="p-4 bg-blue-100 rounded-md shadow-md text-gray-700">
            <p class="italic text-sm">Esta información es la que verá el proveedor sobre su bien para cotizar:</p>
            <ul class="mt-2 space-y-1">
                <li><strong>Nombre:</strong> {{ $activoBusqueda->nombre ?? '-' }}</li>
                <li><strong>Tipo:</strong> {{ $activoBusqueda->tipo->nombre ?? '-' }}</li>
                <li><strong>Categoría:</strong> {{ $activoBusqueda->categoria->nombre ?? '-' }}</li>
                <li><strong>Ubicación:</strong> {{ $activoBusqueda->ubicacion->nombre ?? '-' }}</li>
            </ul>
        </div>
    </div>

    <!-- Selección de Servicio, Activos y Resumen -->
    <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        <!-- Servicio -->
        <div class="space-y-4">
            <x-label class="text-xl" value="Servicio" />
            <div class="mb-4" x-data="{ openDropdownActividad: false }" @click.away="openDropdownActividad = false">
                <div class="relative">
                    <button type="button" @click="openDropdownActividad = !openDropdownActividad"
                        :disabled="@json(!$id_activo)"
                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <span>
                            @if ($cod_actividad && $selectedActividadNombre)
                                {{ $selectedActividadNombre->nombre }}
                            @else
                                Seleccione un Servicio
                            @endif
                        </span>
                        <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </button>

                    @if ($id_activo)
                        <div x-show="openDropdownActividad" x-transition
                            class="absolute mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10">
                            <!-- Buscador -->
                            <div class="p-2">
                                <input type="text" wire:model.live="searchActividad"
                                    @keydown.enter="openDropdownActividad = false"
                                    @keydown.escape="openDropdownActividad = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Buscar Servicio..." />
                            </div>
                            <!-- Lista de servicios -->
                            <ul class="max-h-60 overflow-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                                @forelse ($filteredActividad as $actividad)
                                    <li wire:click="setIdActividad({{ $actividad->COD_ACTIVIDAD }})"
                                        @click="openDropdownActividad = false; $wire.set('searchActividad','')"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="block truncate">{{ $actividad->nombre }}</span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No se encontraron Servicios.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Especialidad -->
        <div class="space-y-4">
            <x-label class="text-xl" value="Especialidad" />
            @if ($actividadIncompatible)
                <p class="text-sm text-red-600">Debe seleccionar una actividad acorde al bien.</p>
            @endif
            @if (!$actividadIncompatible)
                <div class="mb-4" x-data="{ openDropdownServicio: false }" @click.away="openDropdownServicio = false">
                    <div class="relative">
                        <button type="button" @click="openDropdownServicio = !openDropdownServicio"
                            :disabled="@json(!$cod_actividad)"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer disabled:opacity-50 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_servicio && $selectedServicioNombre)
                                    {{ $selectedServicioNombre->nombre }}
                                @else
                                    Seleccione una Especialidad
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </button>

                        @if ($id_activo)
                            <div x-show="openDropdownServicio" x-transition
                                class="absolute mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg z-10">
                                <!-- Buscador -->
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchServicio"
                                        @keydown.enter="openDropdownServicio = false"
                                        @keydown.escape="openDropdownServicio = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        placeholder="Buscar Especialidad..." />
                                </div>
                                <!-- Lista de servicios -->
                                <ul
                                    class="max-h-60 overflow-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                                    @forelse ($filteredServicios as $servicio)
                                        <li wire:click="setIdServicio({{ $servicio->id_servicio }})"
                                            @click="openDropdownServicio = false; $wire.set('searchActividad','')"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span class="block truncate">{{ $servicio->nombre }}</span>
                                        </li>
                                    @empty
                                        <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                            No se encontraron Especialidades.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <!-- Selección de Prestadoras -->
        <div class="col-span-2 space-y-4 p-4 border-2 border-blue-300 rounded-md shadow-md">
            @if ($id_servicio)
                <x-label class="text-md" value="Buscar Prestadora" />
                <input type="text"
                    class="form-control h-auto max-h-96 overflow-auto text-lg p-2 border border-blue-400 rounded-md w-full focus:ring-2 focus:ring-blue-400 focus:outline-none"
                    wire:model.live="searchPrestadora" placeholder="Buscar prestadora..." />

                <div class="flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6 mt-4">
                    <!-- Lista de Prestadoras Disponibles -->
                    <div class="md:w-1/2 overflow-auto max-h-64 border p-4 rounded-md bg-white">
                        <x-label class="text-xl" value="Prestadoras Disponibles" />
                        @forelse ($prestadoras as $prestadora)
                            <div class="flex items-center my-2">
                                <input type="checkbox" value="{{ $prestadora->cuit }}"
                                    wire:model.live="id_prestadora" id="prestadora_{{ $prestadora->cuit }}"
                                    class="mr-2">
                                <label for="prestadora_{{ $prestadora->cuit }}">
                                    {{ $prestadora->razon_social }}
                                </label>
                            </div>
                        @empty
                            <div class="text-gray-500">No hay prestadoras disponibles.</div>
                        @endforelse

                        <!-- Prestadoras Invitadas -->
                        @forelse ($prestadorasInvitadas as $prestadoraInvitada)
                            <div class="flex items-center my-2 relative group">
                                <input type="checkbox" value="{{ $prestadoraInvitada->cuit }}" disabled
                                    id="prestadora_inv_{{ $prestadoraInvitada->cuit }}"
                                    class="mr-2 cursor-not-allowed">
                                <label for="prestadora_inv_{{ $prestadoraInvitada->cuit }}"
                                    class="cursor-not-allowed">
                                    {{ $prestadoraInvitada->razon_social }} (Invitada)
                                </label>
                            </div>
                        @empty
                        @endforelse
                    </div>

                    <!-- Prestadoras Seleccionadas -->
                    <div class="md:w-1/2 overflow-auto max-h-64 border p-4 rounded-md bg-blue-100">
                        <x-label class="text-xl" value="Prestadoras Seleccionadas" />
                        @if ($this->prestadorasSeleccionadas->isNotEmpty())
                            <div class="flex flex-col space-y-2">
                                @foreach ($this->prestadorasSeleccionadas as $prestadoraSeleccionada)
                                    <div class="flex items-center justify-between p-2 bg-white rounded-md shadow">
                                        <span>{{ $prestadoraSeleccionada->razon_social }}</span>
                                        <button type="button"
                                            wire:click="removePrestadora('{{ $prestadoraSeleccionada->cuit }}')"
                                            class="text-red-500 hover:text-red-700">&times;</button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-500">No hay prestadoras seleccionadas.</div>
                        @endif
                    </div>

                </div>
                @error('id_prestadora')
                    <div class="mt-2 text-red-500 text-sm">{{ $message }}</div>
                @enderror
            @endif
        </div>
    </div>

    <div class="flex justify-end space-x-4 mt-8">
        <x-secondary-button class="mr-2" wire:click="close">Cancelar</x-secondary-button>
        <x-danger-button wire:click="save" wire:loading.attr="disabled" wire:target="save">Enviar
            Cotizacion</x-danger-button>
        <span
            class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
            wire:loading wire:target="save">Cargando...</span>
    </div>
</div>

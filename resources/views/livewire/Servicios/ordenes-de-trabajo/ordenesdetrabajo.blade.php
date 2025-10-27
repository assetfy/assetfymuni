<div class="w-full min-h-screen bg-white p-10">
    <!-- Encabezado -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-blue-700">Nueva Orden de Trabajo</h2>
    </div>

    <!-- Contenedor principal con grid para organizar la información -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- COLUMNA 1: CLIENTE -->
        <div class="space-y-4">
            <x-label class="text-xl text-black" value="Cliente" />
            <div x-data="{ openDropdownEmpresa: false }" @click.away="openDropdownEmpresa = false">
                <div class="relative">
                    <button type="button" @click="openDropdownEmpresa = !openDropdownEmpresa"
                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer 
                               focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <span>
                            @if ($cuit && $empresaBusqueda)
                                {{ $empresaBusqueda->razon_social }}
                            @else
                                Seleccione a un Cliente
                            @endif
                        </span>
                    </button>
                    <div x-show="openDropdownEmpresa" x-transition
                        class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                        <div class="p-2">
                            <input type="text" wire:model.live="searchEmpresa"
                                @keydown.enter="openDropdownEmpresa = false"
                                @keydown.escape="openDropdownEmpresa = false"
                                class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2 
                                       focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="Buscar Empresa...">
                        </div>
                        <ul
                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 
                                   overflow-auto focus:outline-none sm:text-sm">
                            @forelse ($filteredEmpresas as $empresa)
                                <li wire:click="setCuitEmpresa({{ $empresa->cuit }})"
                                    @click="openDropdownEmpresa = false; @this.set('searchEmpresa', '');"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                    <span class="font-normal block truncate">
                                        {{ $empresa->razon_social }}
                                    </span>
                                </li>
                            @empty
                                <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                    <span class="font-normal block truncate">Sin resultado</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            @error('cuit')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- COLUMNA 2: CATEGORÍA + ACTIVO -->
        <div class="space-y-4">
            <!-- Dropdown CATEGORÍA -->
            <x-label class="text-xl text-black" value="CATEGORÍA" />
            @if ($cuit)
                <div x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false">
                    <div class="relative">
                        <button type="button" @click="openDropdownCategoria = !openDropdownCategoria"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer
                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_categoria && $categoriaBusqueda)
                                    {{ $categoriaBusqueda->nombre }}
                                @else
                                    Seleccione una Categoría
                                @endif
                            </span>
                        </button>
                        <div x-show="openDropdownCategoria" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchCategoria"
                                    @keydown.enter="openDropdownCategoria = false"
                                    @keydown.escape="openDropdownCategoria = false"
                                    class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2 
                                           focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Buscar Categoría...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 
                                       overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($filteredCategorias as $cat)
                                    <li wire:click="setIdCategoria({{ $cat->id_categoria }})"
                                        @click="openDropdownCategoria = false; @this.set('searchCategoria', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">
                                            {{ $cat->nombre }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        <span class="font-normal block truncate">Sin resultado</span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-black-500 mt-[4rem]">Seleccione un Cliente para habilitar la selección de categoría.</p>
            @endif
            @error('id_categoria')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror

            <!-- Dropdown ACTIVO -->
            <x-label class="text-xl text-black" value="Bien" />
            @if ($cuit)
                <div x-data="{ openDropdownActivo: false }" @click.away="openDropdownActivo = false">
                    <div class="relative">
                        <button type="button" @click="openDropdownActivo = !openDropdownActivo"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer
                                   focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span>
                                @if ($id_activo && $activoBusqueda)
                                    {{ $activoBusqueda->nombre }}
                                @else
                                    Seleccione un Bien
                                @endif
                            </span>
                        </button>
                        <div x-show="openDropdownActivo" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" wire:model="searchActivo"
                                    @keydown.enter="openDropdownActivo = false"
                                    @keydown.escape="openDropdownActivo = false"
                                    class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2
                                           focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Buscar Activo...">
                            </div>
                            <ul
                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 
                                       overflow-auto focus:outline-none sm:text-sm">
                                @forelse ($filteredActivos as $activo)
                                    <li wire:click="setIdActivo({{ $activo->id_activo }})"
                                        @click="openDropdownActivo = false; @this.set('searchActivo', '');"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                        <span class="font-normal block truncate">
                                            {{ $activo->nombre }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                        <span class="font-normal block truncate">Sin resultado</span>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-back-500">Seleccione un Cliente para habilitar la selección de Bienes.</p>
            @endif
            @error('id_activo')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- COLUMNA 3: Detalles del activo -->
        <div>
            <div class="bg-blue-100 p-4 rounded-md mt-2">
                <p class="font-semibold text-lg mb-4">Detalles del Bien</p>
                <p class="mb-2"><strong>Nombre:</strong> {{ $activoBusqueda ? $activoBusqueda->nombre : 'N/A' }}</p>
                <p class="mb-2">
                    <strong>Ubicación:</strong>
                    {{ $activoBusqueda && $activoBusqueda->ubicacion ? $activoBusqueda->ubicacion->nombre : 'N/A' }}
                </p>
                <p class="mb-2">
                    <strong>Estado General:</strong>
                    {{ $activoBusqueda && $activoBusqueda->estadoGeneral ? $activoBusqueda->estadoGeneral->nombre : 'N/A' }}
                </p>
                <p class="mb-2">
                    <strong>Responsable:</strong>
                    @if ($activoBusqueda && $activoBusqueda->asignaciones->count() > 0)
                        @php
                            $responsableId = $activoBusqueda->asignaciones->first()->responsable;
                            $responsableNombre = $responsableId ? \App\Models\User::find($responsableId)->name : 'N/A';
                        @endphp
                        {{ $responsableNombre }}
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Separador -->
    <div class="border-t border-gray-300 my-6"></div>

    @if ($id_activo)
        <!-- Tipo de Servicio -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Tipo de Servicio</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="selectedTipoServicio" value="Correctivo/Reparación"
                            class="h-5 w-5 text-blue-600 border-gray-300 focus:ring focus:ring-blue-400">
                        <span class="ml-2 text-gray-700">Correctivo/Reparación</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model.live="selectedTipoServicio" value="Preventivo"
                            class="h-5 w-5 text-blue-600 border-gray-300 focus:ring focus:ring-blue-400">
                        <span class="ml-2 text-gray-700">Preventivo</span>
                    </label>
                </div>
                @error('selectedTipoServicio')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            @if ($selectedTipoServicio == 'Correctivo/Reparación')
                <!-- Preventivo: Asignar a Técnico -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Asignar a</label>
                    <div x-data="{ openDropdownTecnico: false }" @click.away="openDropdownTecnico = false">
                        <div class="relative">
                            <button type="button" @click="openDropdownTecnico = !openDropdownTecnico"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer
                                       focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <span>
                                    @if ($id_tecnico && $tecnicoBusqueda)
                                        {{ $tecnicoBusqueda->usuarios->name }}
                                    @else
                                        Seleccione un Técnico
                                    @endif
                                </span>
                            </button>
                            <div x-show="openDropdownTecnico" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchTecnico"
                                        @keydown.enter="openDropdownTecnico = false"
                                        @keydown.escape="openDropdownTecnico = false"
                                        class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2 
                                               focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        placeholder="Buscar Técnico...">
                                </div>
                                <ul
                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 
                                           overflow-auto focus:outline-none sm:text-sm">
                                    @forelse ($filteredTecnicos as $tecnico)
                                        <li wire:click="setIdTecnico({{ $tecnico->id_usuario }})"
                                            @click="openDropdownTecnico = false; @this.set('searchTecnico', '');"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span class="font-normal block truncate">
                                                {{ $tecnico->usuarios->name }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                            <span class="font-normal block truncate">Sin resultado</span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @error('id_tecnico')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Descripción de la Solicitud con estilos mejorados -->
                <div class="space-y-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción de la
                        Solicitud</label>
                    <div class="border border-blue-300 bg-blue-50 rounded-md p-3">
                        <textarea id="descripcion" wire:model.live="descripcion" rows="6"
                            class="w-full resize-y border border-blue-500 rounded-md px-3 py-2 
                   focus:outline-none focus:ring-2 focus:ring-blue-300 text-gray-800 bg-white"
                            placeholder="Ingrese la descripción..." style="min-height: 120px;"></textarea>
                    </div>
                    @error('descripcion')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Subida de imágenes debajo -->
                <div class="border border-dashed border-blue-400 bg-blue-50 rounded p-4 space-y-3 text-sm mt-4">
                    <h3 class="font-semibold text-blue-700 flex items-center">
                        📸 Subir Imagen(es)
                    </h3>
                    <input type="file" wire:model="newImages" multiple class="w-full text-sm text-blue-700" />
                    @error('newImages.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($imagenesTrabajo && count($imagenesTrabajo) > 0)
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            @foreach ($imagenesTrabajo as $index => $file)
                                <li class="flex justify-between items-center">
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        class="text-red-500 hover:text-red-700 font-semibold">×</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <!-- Preventivo: SLA Checkboxes -->
                <div class="mt-6 p-4 border rounded-md border-blue-500 col-span-3">
                    <h3 class="text-blue-700 font-semibold text-lg mb-2">Acuerdo Nivel de Servicio (SLA)</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_4hs')" wire:model="sla_4hs"
                                class="text-blue-500 border-blue-500 focus:ring-blue-500"
                                :disabled="$sla_8hs || $sla_24hs || $sla_12hs">
                            <label>Menos de 4 hs.</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_8hs')" wire:model="sla_8hs"
                                class="text-blue-500 border-blue-500 focus:ring-blue-500"
                                :disabled="$sla_4hs || $sla_24hs || $sla_12hs">
                            <label>Dentro de las 8 hs.</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_24hs')" wire:model="sla_24hs"
                                class="text-blue-500 border-blue-500 focus:ring-blue-500"
                                :disabled="$sla_4hs || $sla_8hs || $sla_12hs">
                            <label>Dentro de las 24 hs.</label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_12hs')" wire:model="sla_12hs"
                                class="text-blue-500 border-blue-500 focus:ring-blue-500"
                                :disabled="$sla_4hs || $sla_8hs || $sla_24hs">
                            <label>Dentro de las 12 hs.</label>
                        </div>
                    </div>
                </div>
            @elseif ($selectedTipoServicio == 'Preventivo')
                <!-- Correctivo: Asignar a Técnico -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Asignar a</label>
                    <div x-data="{ openDropdownTecnico: false }" @click.away="openDropdownTecnico = false">
                        <div class="relative">
                            <button type="button" @click="openDropdownTecnico = !openDropdownTecnico"
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer
                                       focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <span>
                                    @if ($id_tecnico && $tecnicoBusqueda)
                                        {{ $tecnicoBusqueda->usuarios->name }}
                                    @else
                                        Seleccione un Técnico
                                    @endif
                                </span>
                            </button>
                            <div x-show="openDropdownTecnico" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchTecnico"
                                        @keydown.enter="openDropdownTecnico = false"
                                        @keydown.escape="openDropdownTecnico = false"
                                        class="w-full border border-blue-400 rounded-md px-2 py-1 mb-2 
                                               focus:outline-none focus:ring-2 focus:ring-blue-400"
                                        placeholder="Buscar Técnico...">
                                </div>
                                <ul
                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 
                                           overflow-auto focus:outline-none sm:text-sm">
                                    @forelse ($filteredTecnicos as $tecnico)
                                        <li wire:click="setIdTecnico({{ $tecnico->id_usuario }})"
                                            @click="openDropdownTecnico = false; @this.set('searchTecnico', '');"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span class="font-normal block truncate">
                                                {{ $tecnico->usuarios->name }}
                                            </span>
                                        </li>
                                    @empty
                                        <li class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                            <span class="font-normal block truncate">Sin resultado</span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @error('id_tecnico')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Correctivo: Descripción -->
                <div class="space-y-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción de la
                        Solicitud</label>
                    <div class="border border-blue-300 bg-blue-50 rounded-md p-3">
                        <textarea id="descripcion" wire:model.live="descripcion" rows="6"
                            class="w-full resize-y border border-blue-500 rounded-md px-3 py-2 
                   focus:outline-none focus:ring-2 focus:ring-blue-300 text-gray-800 bg-white"
                            placeholder="Ingrese la descripción..." style="min-height: 120px;"></textarea>
                    </div>
                    @error('descripcion')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Subida de imágenes debajo -->
                <div class="border border-dashed border-blue-400 bg-blue-50 rounded p-4 space-y-3 text-sm mt-4">
                    <h3 class="font-semibold text-blue-700 flex items-center">
                        📸 Subir Imagen(es)
                    </h3>
                    <input type="file" wire:model="newImages" multiple class="w-full text-sm text-blue-700" />
                    @error('newImages.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($imagenesTrabajo && count($imagenesTrabajo) > 0)
                        <ul class="mt-2 list-disc list-inside space-y-1">
                            @foreach ($imagenesTrabajo as $index => $file)
                                <li class="flex justify-between items-center">
                                    <span>{{ $file->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeImage({{ $index }})"
                                        class="text-red-500 hover:text-red-700 font-semibold">×</button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <!-- Correctivo: SLA Programado/Periódico -->
                <div class="mt-6 p-4 border rounded-md border-blue-500 col-span-3">
                    <div class="text-blue-700 font-semibold text-lg mb-2">Acuerdo Nivel de Servicio (SLA):</div>
                    <div class="grid grid-cols-3 gap-4 items-start">
                        <div class="col-span-1 space-y-4">
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="slaTipo" wire:model.live="slaTipo" value="programado"
                                    class="form-radio text-blue-600 focus:ring-blue-500">
                                <span>Programado</span>
                            </label>
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="slaTipo" wire:model.live="slaTipo" value="periodico"
                                    class="form-radio text-blue-600 focus:ring-blue-500">
                                <span>Periódico</span>
                            </label>
                        </div>
                        <div class="col-span-2">
                            @if ($slaTipo === 'programado')
                                <div class="flex items-center space-x-2 mb-4">
                                    <label class="text-blue-700 whitespace-nowrap">Definir Fecha</label>
                                    <input type="date" wire:model="fechaProgramada" min="{{ date('Y-m-d') }}"
                                        class="border border-blue-600 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-300 text-blue-700">
                                </div>
                                @error('fechaProgramada')
                                    <span class="text-red-600 text-sm">{{ $message }}</span>
                                @enderror
                            @endif

                            @if ($slaTipo === 'periodico')
                                <div class="grid grid-cols-2 gap-6 mb-4">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-blue-700 whitespace-nowrap">Periodicidad</label>
                                        <select wire:model.live="periodicidad"
                                            class="border border-blue-500 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-300 text-blue-700">
                                            <option value="">Seleccione</option>
                                            <option value="diario">Cada día</option>
                                            <option value="semana">Cada semana</option>
                                            <option value="2semanas">Cada 2 semanas</option>
                                            <option value="mes">Cada mes</option>
                                        </select>
                                    </div>
                                    <div x-data="{ inicio: @entangle('fechaInicio') }">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <label class="text-blue-700 whitespace-nowrap">Inicia</label>
                                            <input type="date" x-model="inicio" wire:model="fechaInicio"
                                                min="{{ date('Y-m-d') }}"
                                                class="border border-blue-500 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-300 text-blue-700">
                                        </div>
                                        @error('fechaInicio')
                                            <span class="text-red-600 text-sm">{{ $message }}</span>
                                        @enderror
                                        <div class="flex items-center space-x-2">
                                            <label class="text-blue-700 whitespace-nowrap mr-4">Fin</label>
                                            <input type="date" wire:model="fechaFin" :min="inicio"
                                                class="border border-blue-500 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-300 text-blue-700">
                                        </div>
                                        @error('fechaFin')
                                            <span class="text-red-600 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                @if (in_array($periodicidad, ['semana', '2semanas', 'mes']))
                                    <div class="mt-6">
                                        <label class="text-blue-700 block mb-2">Días de la semana:</label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                            @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                                <label class="inline-flex items-center space-x-2">
                                                    <input type="checkbox" value="{{ $dia }}"
                                                        wire:model="diasSeleccionados"
                                                        class="text-blue-600 rounded focus:ring-blue-500">
                                                    <span>{{ $dia }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Botón Enviar reposicionado a la derecha -->
        <div class="flex justify-end mt-6">
            <button wire:click="save"
                class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Enviar
            </button>
        </div>
    @endif
</div>

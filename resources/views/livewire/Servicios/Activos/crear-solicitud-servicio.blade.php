<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center space-x-2 mb-6">
                <!-- Icono de documento en azul (Heroicons, MIT License) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6m2 12H7a2 2 0 01-2-2V6a2
             2 0 012-2h5l5 5v11a2 2 0 01-2 2z" />
                </svg>

                <!-- Texto del título -->
                <h2 class="text-2xl font-bold text-gray-800">
                    Solicitud de Cotización
                </h2>
            </div>
        </x-slot>
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <x-slot name="content">
                <!-- Selector de Servicios con Búsqueda -->
                <div class="space-y-6">
                    <div>
                        <label for="nombre_solicitud" class="block text-sm font-medium text-gray-700">Titulo de la
                            Solicitud</label>
                        <input type="text" id="nombre_solicitud" wire:model="nombre_solicitud"
                            class="w-full mt-1 p-2 text-sm border border-blue-400 rounded-md focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <x-input-error for="nombre_solicitud" class="mt-1 text-red-500 text-sm" />
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start md:space-x-8 space-y-4 md:space-y-0">
                        <!-- Tipo de Servicio -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Servicio</label>
                            <div class="flex items-center space-x-6 mt-2">
                                @foreach ($tiposServicio as $tipo)
                                    <label class="flex items-center">
                                        <input type="radio" wire:model="selectedTipoServicio"
                                            value="{{ $tipo->id_tipo_solicitud }}"
                                            class="h-5 w-5 text-blue-600 border-gray-300 focus:ring focus:ring-blue-400">
                                        <span class="ml-2 text-gray-700">{{ $tipo->nombre }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Fecha y Hora -->
                        <div>
                            <label for="fechaHora" class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
                            <input type="datetime-local" id="fechaHora" wire:model.live="fechaHora"
                                min="{{ date('Y-m-d\TH:i') }}"
                                class="w-48 mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1 text-sm" />
                            <x-input-error for="fechaHora" />
                        </div>
                    </div>

                    <div class="relative" x-data="{ openDropdownServicio: false }" @click.away="openDropdownServicio = false">
                        <label class="text-sm font-medium text-gray-700">Servicio</label>
                        <button type="button" @click="openDropdownServicio = !openDropdownServicio"
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg shadow-sm px-4 py-2 text-left text-gray-700 focus:ring focus:ring-blue-300">
                            <span>
                                @if ($id_servicio && $selectedActividadNombre)
                                    {{ $selectedActividadNombre->nombre }}
                                @else
                                    Seleccione un Servicio
                                @endif
                            </span>
                            <svg class="w-5 h-5 text-gray-500 absolute top-1/2 right-4 transform -translate-y-1/2"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.65a.75.75 0 01-1.08 0l-4.25-4.65a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        @if ($filteredServicios->isNotEmpty() || $searchServicio)
                            <div x-show="openDropdownServicio" x-transition
                                class="absolute mt-2 w-full bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchServicio"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                        placeholder="Buscar Servicio...">
                                </div>
                                <ul class="max-h-60 overflow-auto">
                                    @forelse ($filteredServicios as $servicio)
                                        <li wire:click="setIdServicio({{ $servicio->COD_ACTIVIDAD }})"
                                            @click="openDropdownServicio = false; @this.set('searchServicio', '');"
                                            class="cursor-pointer px-4 py-2 hover:bg-blue-100">
                                            {{ $servicio->nombre }}
                                        </li>
                                    @empty
                                        <li class="px-4 py-2 text-gray-500">No se encontraron Servicios.</li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4" x-data="{ openDropdownEspecializacion: false }" @click.away="openDropdownEspecializacion = false">
                        <x-label value="Especialización" />
                        <div class="relative">
                            <button type="button"
                                @click="if({{ $id_servicio ? 'true' : 'false' }}) openDropdownEspecializacion  = !openDropdownEspecializacion "
                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out sm:text-sm {{ !$id_servicio ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ !$id_servicio ? 'disabled' : '' }}>
                                <span>
                                    @if ($id_servicio && $selectedEspecializacion)
                                        {{ $selectedEspecializacionNombre }}
                                    @else
                                        Seleccione una especialización
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
                            <div x-show="openDropdownEspecializacion" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchEspecializacion"
                                        @keydown.enter="openDropdownEspecializacion = false"
                                        @keydown.escape="openDropdownEspecializacion = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                        placeholder="Buscar Especializacion..." {{ !$id_servicio ? 'disabled' : '' }}>
                                </div>
                                <ul class="h-48 overflow-y-auto text-base ring-1 ring-black ring-opacity-5 sm:text-sm">
                                    @forelse ($filteredEspecializacion as $servicio)
                                        <li wire:click="setIdEspecializacion({{ $servicio['id_servicio'] }})"
                                            @click="openDropdownEspecializacion = false; $wire.set('searchEspecializacion','')"
                                            class="cursor-pointer py-2 pl-3 hover:bg-blue-100">
                                            {{ $servicio['nombre'] }}
                                        </li>
                                    @empty
                                        <li class="py-2 px-3 text-gray-700">No se encontraron Especialidades.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" wire:model="id_servicio" />
                        <x-input-error for="id_servicio" />
                    </div>

                    <div class="space-y-4 mt-6">
                        <div>
                            <label for="descripcion"
                                class="block text-sm font-medium text-gray-700">Descripción</label>
                            <textarea id="descripcion"
                                class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-300"
                                wire:model.lazy="descripcion"></textarea>
                            <x-input-error for="descripcion" />
                        </div>
                    </div>
                </div>
                <!-- Selección múltiple de Prestadoras -->
                @if (($prestadoras && $prestadoras->isNotEmpty()) || ($prestadorasInvitadas && $prestadorasInvitadas->isNotEmpty()))
                    <div class="mt-6">
                        <label class="block text-lg font-medium text-gray-700">Prestadoras</label>
                        <div class="flex flex-wrap gap-2 mt-2">
                            {{-- Mostrar prestadoras registradas habilitadas --}}
                            @foreach ($prestadoras as $empresa)
                                <label for="prestadora_{{ $empresa->cuit }}"
                                    class="relative flex items-center px-4 py-2 border rounded-lg cursor-pointer shadow-sm transition-all w-full sm:w-auto
                                   @if (in_array($empresa->cuit, $id_prestadora)) bg-blue-100 border-blue-300 text-blue-900
                                   @else bg-gray-100 border-gray-300 text-gray-700 @endif">
                                    <input type="checkbox" id="prestadora_{{ $empresa->cuit }}"
                                        value="{{ $empresa->cuit }}" wire:model.lazy="id_prestadora" class="hidden">
                                    <span class="flex-1">{{ $empresa->razon_social }}</span>
                                    {{-- Ícono de selección --}}
                                    @if (in_array($empresa->cuit, $id_prestadora))
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="w-5 h-5 text-blue-500 ml-4 flex-none" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </label>
                            @endforeach
                            {{-- Mostrar prestadoras invitadas deshabilitadas --}}
                            @foreach ($prestadorasInvitadas as $invitado)
                                <label for="prestadora_invitado_{{ $invitado->cuit }}"
                                    class="relative flex items-center px-4 py-2 border rounded-lg bg-gray-200 text-gray-500 shadow-sm transition-all w-full sm:w-auto cursor-not-allowed">
                                    <input type="checkbox" id="prestadora_invitado_{{ $invitado->cuit }}"
                                        value="{{ $invitado->cuit }}" disabled class="hidden">
                                    <span class="flex-1">
                                        {{ $invitado->razon_social ?? $invitado->cuit }}
                                        <span class="text-sm font-light">(invitada)</span>
                                    </span>
                                    {{-- Aquí podrías agregar algún ícono o indicador adicional si lo deseas --}}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @elseif ($id_servicio && $selectedEspecializacion && (!$prestadoras || $prestadoras->isEmpty()))
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-600 text-sm font-medium">No hay prestadoras que ofrezcan este servicio.</p>
                    </div>
                @endif
            </x-slot>
            <x-slot name="footer">
                <div class="flex items-center space-x-3">
                    <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
                    @if ($prestadoras && $prestadoras->isNotEmpty())
                        <x-danger-button wire:click="save" wire:loading.remove wire:target="save">Solicitar
                            Servicio</x-danger-button>
                    @endif
                    <span class="inline-flex items-center px-2 py-1 text-sm text-blue-700 bg-blue-50 rounded-md"
                        wire:loading wire:target="save">Cargando...</span>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

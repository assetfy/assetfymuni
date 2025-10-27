<div>
    <!-- Modal personalizado para Carga de Ubicaciones -->
    <x-dialog-modal wire:model="open" maxWidth="custom" class="modal-ubicacion">
        <!-- Título del modal centrado -->
        <x-slot name="title">
            <div class="flex justify-center items-center">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-map-marked-alt text-blue-600"></i>
                    Carga de Ubicaciones
                </h2>
            </div>
        </x-slot>

        <!-- Inicio del formulario -->
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Modo de carga única -->
                <div x-show="$wire.modo === 'single'" class="transition-all duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">

                        <!-- Contenedor del mapa -->
                        <div class="lg:col-span-1 h-[850px]">
                            <div class="h-full w-full rounded-lg shadow-md p-2">
                                <div id="mapModal2" class="h-full w-full rounded-lg" wire:ignore></div>
                                <x-input type="hidden" wire:model="lat" />
                                <x-input type="hidden" wire:model="long" />
                            </div>
                        </div>

                        <!-- Contenedor del formulario de información -->
                        <div class="lg:col-span-1 h-[850px] p-6 bg-white rounded-lg shadow-md overflow-y-auto">

                            <!-- Información General -->
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i> Información General
                                </h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Los datos de ubicación (País, Provincia, Ciudad y Calle) se completan
                                    automáticamente al seleccionar un punto en el mapa.
                                </p>

                                @if ($origen === 'ubicaciones_delegadas')
                                <!-- Dropdown de Clientes -->
                                <div class="form-group mb-4" x-data="{ openDropdownClientes: false }"
                                    @click.away="openDropdownClientes = false">
                                    <x-label value="Seleccione un cliente" />
                                    <div class="relative w-full">
                                        <button type="button" @click="openDropdownClientes = !openDropdownClientes"
                                            class="w-full bg-white border border-gray-300 rounded-lg h-10 px-4 py-2 flex justify-between items-center">
                                            <span>{{ $selectedCuit ?: 'Seleccione cliente' }}</span>
                                            <svg class="h-4 w-4 text-gray-700" viewBox="0 0 20 20">
                                                <path d="M5.516 7.548l4.484 4.484 4.484-4.484L16 8.032l-6 6-6-6z" />
                                            </svg>
                                        </button>
                                        <div x-show="openDropdownClientes" x-transition
                                            class="absolute mt-1 w-full rounded-lg bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" wire:model.live="searchClientes"
                                                    @keydown.enter="openDropdownClientes = false"
                                                    @keydown.escape="openDropdownClientes = false"
                                                    class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2 mb-2"
                                                    placeholder="Buscar cliente..." />
                                            </div>
                                            <ul class="max-h-60 overflow-auto">
                                                @forelse ($clientesLista as $cliente)
                                                <li wire:click="setClientes({{ $cliente->cuit }})"
                                                    @click="openDropdownClientes = false"
                                                    class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                    {{ $cliente->razon_social }}
                                                </li>
                                                @empty
                                                <li class="py-2 px-3 text-gray-700">No hay clientes disponibles.
                                                </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                    <x-input-error for="cuit_empresa" />
                                </div>
                                @endif

                                <!-- Nombre -->
                                <div>
                                    <x-label value="Nombre de la Ubicación" />
                                    <x-input type="text" wire:model.lazy="nombre"
                                        class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                    <x-input-error for="nombre" />
                                </div>
                                <div class="col-span-2 mt-4">
                                    <div
                                        class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2 flex items-center space-x-3">
                                        <input type="checkbox" class="form-checkbox text-blue-600 h-5 w-5"
                                            wire:model.live="MultiplePiso" />
                                        <label class="text-sm text-gray-800">¿Tiene múltiples pisos?</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-map-marker-alt mr-2 text-red-500"></i> Dirección
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <x-label value="País" />
                                        <x-input type="text" wire:model="pais" readonly
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                    </div>
                                    <div>
                                        <x-label value="Provincia" />
                                        <x-input type="text" wire:model="provincia" readonly
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                    </div>
                                    <div>
                                        <x-label value="Ciudad" />
                                        <x-input type="text" wire:model="ciudad" readonly
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                    </div>
                                    <div class="col-span-2">
                                        <x-label value="Calle" />
                                        <x-input type="text" wire:model.lazy="calle" readonly
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                        <x-input-error for="calle" />
                                    </div>
                                    <div>
                                        <x-label value="Altura" />
                                        <x-input type="text" wire:model.lazy="altura"
                                            class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                        <x-input-error for="altura" />
                                    </div>
                                    <div>
                                        <x-label value="Piso" />
                                        <x-input type="number" wire:model.lazy="piso" min="1"
                                            class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2"
                                            oninput="this.value = Math.max(1, this.value)" />
                                        <x-input-error for="piso" />
                                        @if ($MultiplePiso)
                                        <p class="mt-1 text-blue-600 text-sm">
                                            Por favor coloque la cantidad de plantas de la ubicación.
                                        </p>
                                        @endif
                                    </div>
                                    <div>
                                        <x-label value="Departamento" />
                                        <x-input type="text" wire:model.lazy="depto"
                                            class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2" />
                                        <x-input-error for="depto" />
                                    </div>
                                     <div>
                                        <x-label value="Subsuelo" />
                                        <x-input type="number" wire:model.lazy="subsuelo" min="0"
                                            class="w-full border border-gray-300 rounded-lg h-10 px-4 py-2"
                                            oninput="this.value = Math.max(1, this.value)" />
                                        <x-input-error for="subsuelo" />
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de ubicación -->
                            <div class="p-4 bg-gray-50 rounded-lg shadow-sm mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-building mr-2 text-green-500"></i> Configuración de Ubicación
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="col-span-2">
                                        <x-label value="Tipo de Ubicación" />
                                        <select wire:model="tipo"
                                            class="form-select w-full rounded-lg h-10 px-4 py-2">
                                            <option value="" hidden>Seleccione un Tipo de Ubicación</option>
                                            @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="tipo" />
                                    </div>
                                    <div class="col-span-2">
                                        <x-label value="Propiedad" />
                                        <x-input type="text"
                                            class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2"
                                            value="{{ $origen !== 'ubicaciones_delegadas' ? 'Propio' : 'Cliente' }}"
                                            disabled />
                                        <x-input type="hidden" wire:model="propiedad"
                                            value="{{ $origen !== 'ubicaciones_delegadas' ? 'Propio' : 'Cliente' }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-slot>
            <!-- Footer con botones -->
            <x-slot name="footer">
                <div class="flex justify-end space-x-3">
                    <button type="button" class="px-4 py-2 bg-gray-300 rounded-lg" wire:click="close">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-lg" wire:click="save">
                        <i class="fas fa-save"></i> Crear Ubicación
                    </button>
                </div>
            </x-slot>
        </form>

        <!-- Z-index for modal -->
        <style>
            .modal-ubicacion {
                z-index: 60 !important;
            }
        </style>
    </x-dialog-modal>
</div>
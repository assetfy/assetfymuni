<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <!-- Título -->
        <x-slot name="title">
            <div class="flex items-center gap-3 text-gray-800">
                <i class="fa-solid fa-file-signature text-red-500 text-2xl"></i>
                <span class="text-xl font-semibold">Solicitar Cotización</span>
            </div>
        </x-slot>

        <!-- Contenido -->
        <x-slot name="content">
            <div class="space-y-6">
                <!-- Servicio seleccionado -->
                <div class="flex items-center gap-2 text-lg font-medium">
                    <i class="fa-solid fa-cogs text-blue-500"></i>
                    @if ($nombreServicio)
                        <span>{{ $nombreServicio->nombre }}</span>
                    @else
                        <span class="text-gray-500">Sin servicio</span>
                    @endif
                </div>

                <!-- Selección de Activos -->
                @if ($activos->isNotEmpty())
                    <div>
                        <x-label class="text-xl" value="Activos" />
                        <div class="relative" x-data="{ openDropdownActivo: false }">
                            <button type="button" @click="openDropdownActivo = !openDropdownActivo"
                                class="w-full flex items-center justify-between bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 text-left cursor-pointer focus:ring focus:ring-blue-300">
                                <span>
                                    @if ($id_activo && $activoBusqueda)
                                        {{ $activoBusqueda->nombre }}
                                    @else
                                        Seleccione un Activo
                                    @endif
                                </span>
                                <i class="fa-solid fa-chevron-down text-gray-500"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="openDropdownActivo" x-transition
                                class="absolute mt-2 w-full bg-white border border-gray-300 rounded-md shadow-md z-10">
                                <input type="text" wire:model.live="searchActivo"
                                    class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none"
                                    placeholder="Buscar Activo..." />
                                <ul class="max-h-60 overflow-auto">
                                    @forelse ($filteredActivos as $activo)
                                        <li wire:click="setIdActivo({{ $activo->id_activo }})"
                                            @click="openDropdownActivo = false; @this.set('searchActivo', '');"
                                            class="cursor-pointer px-4 py-2 hover:bg-blue-100 flex items-center gap-2">
                                            <i class="fa-solid fa-box-archive text-gray-500"></i>
                                            <span>{{ $activo->nombre }}</span>
                                        </li>
                                    @empty
                                        <li class="px-4 py-2 text-gray-500">
                                            <i class="fa-solid fa-exclamation-circle"></i> No se encontraron activos.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Información del Activo -->
                        @if ($activoBusqueda)
                            <div class="p-4 bg-gray-100 rounded-md shadow-md mt-4">
                                <p><i class="fa-solid fa-box"></i> <strong>Nombre:</strong> {{ $activoBusqueda->nombre }}</p>
                                <p><i class="fa-solid fa-tag"></i> <strong>Tipo:</strong> {{ $activoBusqueda->tipo->nombre }}</p>
                                <p><i class="fa-solid fa-layer-group"></i> <strong>Categoría:</strong> {{ $activoBusqueda->categoria->nombre }}</p>
                                <p><i class="fa-solid fa-map-marker-alt"></i> <strong>Ubicación:</strong>
                                    {{ $activoBusqueda->ubicacion->nombre ?? 'Sin ubicación' }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-red-500 uppercase font-semibold text-center">
                        <i class="fa-solid fa-triangle-exclamation"></i> No hay activos disponibles.
                    </p>
                @endif

                <div class="space-y-4">
                    <label for="fechaHora" class="block text-xl font-medium text-gray-700">Fecha y Hora:</label>
                    <input type="datetime-local" id="fechaHora"
                        class="input-lg mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 text-xl"
                        wire:model.live="fechaHora" />
                    @error('fechaHora')
                        <x-input-error for="fechaHora" class="mt-2" />
                    @enderror
                </div>

                <!-- Descripción de la tarea -->
                <div>
                    <label for="validationTextarea" class="block text-xl font-medium text-gray-700">
                        <i class="fa-solid fa-list"></i> Descripción de la tarea
                    </label>
                    <textarea class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        id="validationTextarea" wire:model.live="descripcion"
                        placeholder="Especifique la tarea a realizar..." required></textarea>
                    @error('descripcion')
                        <x-input-error for="descripcion" class="mt-2" />
                    @enderror
                </div>
            </div>
        </x-slot>

        <!-- Footer -->
        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-secondary-button wire:click="close" class="flex items-center gap-2 px-4 py-2">
                    <i class="fa-solid fa-times"></i> Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white">
                    <i class="fa-solid fa-paper-plane"></i> Solicitar cotización
                </x-danger-button>

                <span wire:loading wire:target="save"
                    class="inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-md">
                    <i class="fa-solid fa-spinner animate-spin"></i> Guardando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

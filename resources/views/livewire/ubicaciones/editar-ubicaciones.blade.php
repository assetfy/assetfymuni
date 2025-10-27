<div>
    <x-dialog-modal wire:model="open" maxWidth="custom" class="modal-edicion-ubicacion">
        <!-- Título centrado -->
        <x-slot name="title">
            <div class="flex justify-center items-center">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                    Editar Ubicación
                </h2>
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full">
                {{-- Mapa --}}
                <div class="h-[850px]" wire:ignore>
                    <div id="mapModal1" class="h-full w-full rounded-lg" wire:ignore></div>
                    <x-input type="hidden" wire:model="lat" />
                    <x-input type="hidden" wire:model="long" />
                </div>

                {{-- Formulario --}}
                <div class="h-[850px] p-6 bg-white rounded-lg shadow-md overflow-y-auto">
                    {{-- Información General --}}
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Información General
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-label value="Nombre" />
                                <x-input type="text" wire:model.defer="nombre" class="w-full" />
                                <x-input-error for="nombre" />
                            </div>
                            <div>
                                <x-label value="País" />
                                <x-input type="text" wire:model.defer="pais" class="w-full" />
                                <x-input-error for="pais" />
                            </div>
                            <div>
                                <x-label value="Provincia" />
                                <x-input type="text" wire:model.defer="provincia" class="w-full" />
                                <x-input-error for="provincia" />
                            </div>
                            <div>
                                <x-label value="Ciudad" />
                                <x-input type="text" wire:model.defer="ciudad" class="w-full" />
                                <x-input-error for="ciudad" />
                            </div>
                        </div>
                    </div>

                    {{-- Dirección --}}
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                            Dirección
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-label value="Calle" />
                                <x-input type="text" wire:model.defer="calle" class="w-full" />
                                <x-input-error for="calle" />
                            </div>
                            <div>
                                <x-label value="Altura" />
                                <x-input type="text" wire:model.defer="altura" class="w-full" />
                                <x-input-error for="altura" />
                            </div>
                            <div>
                                <x-label value="Piso" />
                                <x-input type="number" wire:model.defer="piso" class="w-full" />
                                <x-input-error for="piso" />
                            </div>
                            <div>
                                <x-label value="Departamento" />
                                <x-input type="text" wire:model.defer="depto" class="w-full" />
                                <x-input-error for="depto" />
                            </div>
                            <div>
                                <x-label value="Código Postal" />
                                <x-input type="text" wire:model.defer="codigo_postal" class="w-full" />
                                <x-input-error for="codigo_postal" />
                            </div>
                            <div class="col-span-2">
                                <div
                                    class="w-full bg-gray-100 border border-gray-300 rounded-lg h-10 px-4 py-2 flex items-center space-x-3">
                                    <input type="checkbox" class="form-checkbox text-blue-600 h-5 w-5"
                                        wire:model="MultiplePiso" />
                                    <label class="text-sm text-gray-800">¿Tiene múltiples pisos?</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Configuración de Ubicación --}}
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-building mr-2 text-green-500"></i>
                            Configuración de Ubicación
                        </h3>
                        @if ($tiposUbicacion)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <x-label value="Tipo de Ubicación" />
                                    <select wire:model="tipo" class="form-select w-full">
                                        <option value="" hidden>Seleccione un Tipo</option>
                                        @foreach ($tiposUbicacion as $t)
                                            <option value="{{ $t->id_tipo }}"
                                                @if ($t->id_tipo == $tipo) selected @endif>
                                                {{ $t->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <x-input-error for="tipo" />
                                </div>
                                <div class="col-span-2">
                                    <x-label value="Propiedad" />
                                    <x-input type="text"
                                        class="w-full bg-gray-100 border-gray-300 text-gray-700 rounded-md"
                                        :value="$propiedad" disabled />
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-slot>

        {{-- Footer con botones --}}
        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <button type="button" wire:click="close" class="px-4 py-2 bg-gray-300 rounded-lg">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <!-- Botón que dispara update() y muestra/oculta el spinner -->
                <x-danger-button wire:click="actualizar" wire:loading.remove class="px-4 py-2 text-sm">
                    Actualizar
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="update">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

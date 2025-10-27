<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            Registra las rutas
        </x-slot>
        <x-slot name="content">
            @if ($permisos)
                <div class="mb-4">
                    <label for="selectedPermiso" class="block text-sm font-medium text-gray-700">Permiso:</label>
                    <select id="selectedPermiso" class="form-select" wire:model="selectedPermiso"
                        wire:change="cargarRutas($event.target.value)">
                        <option value="">Selecciona un permiso</option>
                        @foreach ($permisos as $permiso)
                            <option value="{{ $permiso->id_permiso }}">{{ $permiso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($rutas->isNotEmpty())
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Rutas:</label>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ($rutas as $ruta)
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" value="{{ $ruta->id_ruta }}" wire:model.live="selectedRutas"
                                    class="form-checkbox">
                                <span>{{ $ruta->nombre }}</span>
                                <!-- Si la ruta está seleccionada, mostramos el selector de configuración -->
                                @if (in_array($ruta->id_ruta, $selectedRutas))
                                    <select wire:model="configuracionPorRuta.{{ $ruta->id_ruta }}"
                                        class="form-select text-sm">
                                        <option value="No">No</option>
                                        <option value="Si">Si</option>
                                    </select>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end space-x-4">
                <x-danger-button wire:click="save">
                    Crear Permiso por Empresa
                </x-danger-button>
                <x-secondary-button wire:click="close">
                    Cancelar
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="text-center text-2xl font-bold text-gray-800">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2"> Editar Atributos</h2>
            </div>
        </x-slot>

        <x-slot name="content">
            @if ($atributo)
                <!-- Tipo de Campo -->
                <div class="mb-4">
                    <x-label value="Tipo de Campo" class="text-sm font-medium text-gray-700 mb-1" />
                    @foreach ($tipos as $tipo)
                        @if ($atributo->tipo_campo == $tipo->id_tipo_campo)
                            <div class="text-gray-900 text-base">{{ $tipo->nombre }}</div>
                        @endif
                    @endforeach
                </div>
                <!-- Unidad de Medida -->
                @if ($unidad2)
                    <div class="mb-4">
                        <x-label for="updatedMedida" value="Unidad de Medida" />
                        <select id="updatedMedida" wire:model.defer="updatedMedida" class="form-control">
                            @foreach ($unidad2 as $unidad)
                                <option value="{{ $unidad->id_unidad_medida }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <!-- Nombre -->
                <div class="mb-4">
                    <x-label for="updatedNombre" value="Nombre" class="text-sm font-medium text-gray-700 mb-1" />
                    <x-input id="updatedNombre" type="text" wire:model.defer="updatedNombre"
                        class="w-full bg-gray-100 text-gray-800 rounded-lg px-4 py-2 focus:outline-none" />
                    <x-input-error for="updatedNombre" />
                </div>
                <!-- Descripción -->
                <div class="mb-4">
                    <x-label for="updatedDescripcion" value="Descripción"
                        class="text-sm font-medium text-gray-700 mb-1" />
                    <x-input id="updatedDescripcion" type="text" wire:model.defer="updatedDescripcion"
                        class="w-full bg-gray-100 text-gray-800 rounded-lg px-4 py-2 focus:outline-none" />
                    <x-input-error for="updatedDescripcion" />
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2 w-full">
                <x-secondary-button wire:click="$set('open', false)">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="dispatch('check')" wire:loading.remove>
                    Actualizar
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="actualizar">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

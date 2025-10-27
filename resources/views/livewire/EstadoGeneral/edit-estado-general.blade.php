<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar Estado General
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <select class="form-control" wire:model.defer="updatedNombre">
                    <option value="Baja">Baja</option>
                    <option value="Service">Service</option>
                    <option value="Normal/Funcionando">Normal/Funcionando</option>
                </select>
                <x-input-error for="updatedNombre" />
            </div>
            <div class="mb-4">
                <x-label value="Descripcion" />
                <x-input type="text" class="w-full" value="{{ $estado->descripcion }}" wire:model.defer="updatedDescripcion" />
                <x-input-error for="updatedDescripcion" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="guardarCambios" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>
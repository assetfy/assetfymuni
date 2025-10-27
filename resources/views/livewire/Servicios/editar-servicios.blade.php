<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar Servicio
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <x-input type="text" class="w-full" value="{{ $servicio->nombre }}" wire:model.defer="updatedNombre" />
                <x-input-error for="updatedNombre" />
            </div>
            <div class="mb-4">
                <x-label value="Descripcion" />
                <x-input type="text" class="w-full" value="{{ $servicio->descripcion }}" wire:model.defer="updatedDescripcion" />
                <x-input-error for="updatedDescripcion" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="guardarCambios">
                Actualizar
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>
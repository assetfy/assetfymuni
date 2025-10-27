<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar Control
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <x-input type="text" class="w-full" value="{{ $control->nombre }}" wire:model.lazy="updatedNombre " id="controlnombre"/>
                <x-input-error for="updatedNombre " oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
            </div>
            <div class="mb-4">
                <x-label value="Descripcion" />
                <x-input type="text" class="w-full" value="{{ $control->descripcion }}" wire:model.lazy="updatedDescripcion" id="descripcioncontrol" />
                <x-input-error for="updatedDescripcion" oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="dispatch('check')" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="actualizarControl">Cargando...
            </span>
        </x-slot>
    </x-dialog-modal>
</div>
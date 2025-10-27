<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            Crear Control
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <div class="mb-4">
                    <x-label for="nombre" value="Nombre" />
                    <x-input id="nombre" type="text" class="w-full" wire:model.lazy="nombre" oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="nombre" />
                </div>
                <div class="mb-4">
                    <x-label for="descripcion" value="Descripcion" />
                    <x-input id="descripcion" type="text" class="w-full" wire:model.lazy="descripcion" oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')"/>
                    <x-input-error for="descripcion" />
                </div>
                <x-input-error for="id_tipo_activo" />
            </x-slot>
        </form>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Control
                </x-danger-button>
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

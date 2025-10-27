<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Crear Nuevo Tipos de Ubicaciones
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <x-input type="text" class="w-full" wire:model.lazy="nombre" />
                <x-input-error for="nombre" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Nuevo Tipos de Ubicaciones
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">Cargando...</span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

<div>
    <a class="btn btn-primary flex justify-center items-center gap-1 px-3 py-2 transition ease-in-out rounded-md shadow-sm h-full text-sm" wire:click="$set('open', true)" style="font-size: 14px; color: #fff; background-color: #2E5EEF; border-color: #2E5EEF; width: auto;">
        Crear Nuevo Tipo de Usuario
    </a>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Crear Nuevo Tipo de Usuario
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <x-input type="text" class="w-full" wire:model.lazy="nombre" />
                <x-input-error for="nombre" />
            </div>
            <div class="mb-4">
                <x-label value="Descripcion" />
                <x-input type="text" class="w-full" wire:model.lazy="descripcion" />
                <x-input-error for="descripcion" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Nueva Tipo de Usuario
                </x-danger-button>

                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">Cargando...</span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Crear Estado General
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <div class="mb-4">
                    <x-label value="Nombre" />
                    <select class="form-control" wire:model.defer="nombre">
                        <option value=""hidden>Selecione Estado</option>
                        <option value="Baja">Baja</option>  
                        <option value="Service">Service</option>
                        <option value="Normal/Funcionando">Normal/Funcionando</option>
                    </select>
                    <x-input-error for="nombre" />
                </div>
                <div class="mb-4">
                    <x-label value="Descripcion" />
                    <x-input type="text" class="w-full" wire:model.lazy="descripcion" />
                    <x-input-error for="descripcion" />
                </div>
        </form>
        </x-slot>
        <x-slot name="footer">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <x-secondary-button class="close" wire:click="close">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                Crear Estado Gerenal
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="save">Cargando...</span>
            </div>
        </x-slot>
    </x-dialog-modal>

</div>

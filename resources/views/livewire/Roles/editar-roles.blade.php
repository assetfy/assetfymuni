<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar Rol
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Razon Social" />
                @foreach($tipos as $empresa)
                @if($cuit == $empresa->cuit)
                <div class="fw-bold fs-6">{{ $empresa->razon_social }}</div>
                @endif
                @endforeach
                <x-input-error for="cuit" />
            </div>
            <div class="mb-4">
                <x-label value="Nombre" />
                <x-input type="text" class="w-full" wire:model.defer="upNombre " id="nombreRol" />
                <x-input-error for="upNombre" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="update" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>
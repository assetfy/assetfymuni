<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar Estado de actividad
        </x-slot>
        <x-slot name="content">
            <div class="mb-4">
                <x-label value="Nombre" />
                <div class="fw-bold fs-6">{{ $actividad->nombre }}</div>
            </div>
            <div class="mb-4">
                <x-label value="Descripcion" />
                <div class="fw-bold fs-6">{{$actividad->descripcion }}</div>
            </div>
            <div class="mb-4">
                <x-label value="Estado de la actividad" />
                <select class="form-control" wire:model.defer="updateEstado">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
                <x-input-error for="updateEstado" />
            </div>
            <div class="mb-4">
                <label for="logo">Foto:</label>
                <input type="file" wire:model.lazy="logo">
                @error('logo')
                    <span class="error">{{ $message }}</span>
                @enderror
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
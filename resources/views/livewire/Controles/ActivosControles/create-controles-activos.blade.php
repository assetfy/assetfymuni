<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            Crear nuevo control
        </x-slot>
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <x-slot name="content">
                <div class="mb-4">
                    @if ($controlesFaltantes)
                        <x-label for="control-select" value="Control" />
                        <select id="control-select" class="form-control" wire:model.defer="id_control" wire:change="filtro($event.target.value)">
                            <option value="" hidden>Seleccione un control</option>
                            @foreach ($controlesFaltantes as $control)
                                <option value="{{ $control->id_control }}">{{ $control->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="id_control" />
                    @endif
                </div>
                @if ($opcion)
                    <div class="mb-4">
                        <x-label for="imagen-input" value="Fotos" />
                        <input id="imagen-input" wire:model.lazy="imagen" type="file" name="imagen" accept="image/*"
                            class="form-control-file" multiple>
                        @error('imagen.*')
                            <div class="text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
                <div class="mb-4">
                    <x-label for="fecha_inicio" value="Fecha de inicio" />
                    <input id="fecha_inicio" type="date" class="w-full" wire:model.lazy="fecha_inicio" />
                    <x-input-error for="fecha_inicio" />
                </div>
                <div class="mb-4">
                    <x-label for="fecha_final" value="Fecha de Final" />
                    <input id="fecha_final" type="date" class="w-full" wire:model.lazy="fecha_final" />
                    <x-input-error for="fecha_final" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Control
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">
                    Cargando...
                </span>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

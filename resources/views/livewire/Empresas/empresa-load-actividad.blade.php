<div>
    <a class="btn btn-outline-light rounded-pill" style="color: #e4dfdf !important; background-color: transparent !important; border-color: transparent !important;" wire:click="$set('open', true)" wire:loading.attr="disabled" wire:target="BuscarActividad">
        Registra Otra Actividad
    </a>
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            Actividad
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                @if ($actividad !== null && $actividad->isNotEmpty())
                <div class="mb-4">
                    <label for="selectActividad">Seleccionar Actividad:</label>
                    <select id="selectActividad" wire:model="selectedActividad" wire:loading.attr="disabled">
                        <option value="">Seleccione una actividad</option>
                        @foreach ($actividad as $act)
                        <option value="{{ $act->COD_ACTIVIDAD }}">{{ $act->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="alert alert-danger" wire:loading.remove>
                    Actividad no encontrada
                </div>
                @endif
                <div class="mb-4">
                    <x-label value="Nombre" />
                    <x-input type="text" class="w-full" wire:model.lazy="razon_social" />
                    <x-input-error for="updatedNombre" />
                </div>
                <div class="mb-4">
                    <label for="constancia_afip">Adjuntar Documentos:</label>
                    <input type="file" wire:model="constancia_afip">
                    @error('imagen')
                    <span class="error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-4">
                    <x-label value="Localidad" />
                    <x-input type="text" class="w-full" wire:model.lazy="localidad" />
                    <x-input-error for="localidad" />
                </div>
                <div class="mb-4">
                    <x-label value="Domicilio" />
                    <x-input type="text" class="w-full" wire:model.lazy="domicilio" />
                    <x-input-error for="domicilio" />
                </div>
                <div class="mb-4">
                    <x-label value="Piso" />
                    <x-input type="text" class="w-full" wire:model.lazy="piso" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                    <x-input-error for="piso" />
                </div>
                <div class="mb-4">
                    <x-label value="Codigo Postal" />
                    <x-input type="text" class="w-full" wire:model.lazy="codigo_postal" oninput="this.value = this.value.replace(/[^0-9]/g, '')"/>
                    <x-input-error for="codigo_postal" />
                </div>
            </x-slot>
        </form>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                @if ($actividad != null && count($actividad) > 0 && !$actividad->contains('nombre'))
                <!-- Este botón solo se mostrará si hay actividades y el nombre de la actividad no coincide con el valor de $cod -->
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Registrar Empresa
                </x-danger-button>
                @endif
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">Cargando...</span>
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

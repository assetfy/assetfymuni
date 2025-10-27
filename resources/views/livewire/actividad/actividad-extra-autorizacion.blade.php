<div>
    <x-dialog-modal wire:model.live="open" maxWidth=3xl>
        <x-slot name="title">
            Editar Actividad
        </x-slot>
        <x-slot name="content">
            @if ($empresa)
                <div class="flex mb-4">
                    <div class="flex-1">
                        <x-label value="Nombre" />
                        <div class="fw-bold fs-6">{{ $empresa->cuit }}</div>
                        <br>
                        <x-label value="Codigo de Actividad" />
                        <div class="fw-bold fs-6">{{ $empresa->cod_actividad }}</div>
                        <br>
                        <x-label value="Estado de la actividad" />
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="estadoDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $empresa->estado == 'Aceptado' ? 'Aceptado' : 'Rechazado' }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="estadoDropdown">
                                <li><a class="dropdown-item" href="#"
                                        wire:click="setSelectedEstado('1')">Aceptado</a></li>
                                <li><a class="dropdown-item" href="#"
                                        wire:click="setSelectedEstado('2')">Rechazado</a></li>
                            </ul>
                        </div>
                        <x-input-error for="selectedEstado" />
                    </div>
                </div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>

            <x-danger-button wire:click="updateEstado" wire:loading.remove>
                Actualizar
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>

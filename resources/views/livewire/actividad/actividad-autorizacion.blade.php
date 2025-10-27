<div>
    <a class="border border-neutral-200 shadow-sm rounded-md outline-none focus:border-blue-300 px-4 py-2 bg-blue-500 text-white transition ease-in-out 
        hover:bg-blue-600 dark:bg-blue-700 dark:border-blue-600 dark:focus:border-blue-600 dark:text-white dark:hover:bg-blue-800 dark:hover:border-blue-600"
        wire:click="$set('open', true)" style="text-decoration: none;"> Editar 
        <i class="fa-solid fa-pen-to-square"></i> 
        {{  $empresa->estado == '1' ? 'Aceptado' : ( $empresa->estado == '0' ? 'En Revisión' : 'Rechazado') }}
    </a>
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            Editar Estado
        </x-slot>
        <x-slot name="content">
            <div class="flex mb-4">
                <div class="flex-1">
                    <x-label value="Nombre" />
                    <div class="fw-bold fs-6">{{ $empresa->cuit }}</div>
                    <br>
                    <x-label value="Codigo de Actividad" />
                    <div class="fw-bold fs-6">{{$empresa->COD_ACTIVIDAD}}</div>
                    <br>
                    <x-label value="Estado de la actividad" />
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="estadoDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $empresa->estado == 'Aceptado' ? 'Aceptado' : ($empresa->estado == 'En Revision' ? 'En Revisión' : 'Rechazado') }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="estadoDropdown">
                            <li><a class="dropdown-item" href="#" wire:click="setSelectedEstado('1')">Aceptado</a></li>
                            <li><a class="dropdown-item" href="#" wire:click="setSelectedEstado('2')">Rechazado</a></li>
                        </ul>
                    </div>
                    <x-input-error for="selectedEstado" />
                    <div>
                        @if ($selectedEstado == '2')
                            <x-label value="Descripción del rechazo" />
                            <textarea wire:model="descripcionRechazo" class="mt-1 block w-full" rows="3"></textarea>
                            <x-input-error for="descripcionRechazo" />
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <x-label value="Constancia de Afip" />
                    <div>
                        <img src="{{ $empresa->constancia_afip }}" alt="Constancia AFIP" style="width: 100%; height: auto;">
                    </div>
                </div>
            </div>
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

<div>
    <a class="btn btn-primary" wire:click="$set('open', true)" wire:ignore>
        Editar <i class="fas fa-edit"></i>
    </a>

    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Editar atributos {{$atributo->id_atributo}}
        </x-slot>
    
        <x-slot name="content">
            @if(empty($resultado))
                <div class="mb-4">
                    <x-label for="update_campo" value="Campo" />
                    <x-input id="update_campo" type="text" class="w-full" value="{{ $this->update_campo }}"
                        wire:model.live="update_campo" />
                    <x-input-error for="update_campo" />
                </div>
                <div class="mb-4">
                    <x-label for="update_numerico" value="Campo Numerico" />
                    <x-input id="update_numerico" type="text" class="w-full" value="{{ $this->update_numerico }}"
                        wire:model.live="update_numerico" />
                    <x-input-error for="update_numerico" />
                </div>
            @else
                <div class="text-red-500">El atributo no puede ser editado.</div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open', false)" wire:ignore>
                Cancelar
            </x-secondary-button>

            @if(empty($resultado))
                <x-danger-button wire:click="dispatch('editar')" wire:loading.remove wire:target="actualizarNuevoAtributo">
                    Actualizar
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="actualizar">Cargando...</span>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>

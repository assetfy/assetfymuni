<div class="flex justify-center items-center flex-col">
    <x-dialog-modal wire:model.live="open"  maxWidth=xl>
        <x-slot name="title">
            Crear Nuevo/s Atributo/s
        </x-slot>
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <x-slot name="content">
                @if(collect($atributosFaltantes)->isEmpty())
                    <div class="text-red-500">No hay atributos para cargar</div>
                @else
                    <div class="mb-4">
                        <x-label value="Atributos" />
                        @foreach($atributosFaltantes as $valor)
                            <div class="flex items-center mb-2 mt-2"> <!-- Modificado -->
                                <input type="checkbox" wire:model.live="selectedAtributos.{{ $valor->id_atributo }}" class="mr-2">
                                <label for="{{ $valor->nombre }}">{{ $valor->nombre }}</label>
                            </div>
                            @if(isset($selectedAtributos[$valor->id_atributo]) && $selectedAtributos[$valor->id_atributo])
                                <div class="mb-4 mt-4"> <!-- Modificado -->
                                    <x-label value="Campo {{ $valor->nombre }}" class="mb-1" />
                                    <x-input type="text" class="w-full" wire:model="campo.{{ $valor->id_atributo }}" />
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </x-slot>
            <x-slot name="footer">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <x-secondary-button wire:click="close">
                        Cancelar
                    </x-secondary-button>
    
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                        Añadir Nuevo/s Atributo/s
                    </x-danger-button>
    
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">Cargando...
                    </span>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

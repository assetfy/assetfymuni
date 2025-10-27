<div>
    <a class="btn btn-primary" wire:click="$set('open', true)"> Editar <i class="fas fa-edit"></i></a>
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            Detalle
        </x-slot>
        <x-slot name="content">
            @if($empresas)
                <div class="mb-4">
                    <p>Sin comentarios</p>
                </div>
            @else
                <div class="mb-4">
                    @foreach ($empresas as $empresa)
                        @if ($empresa->cuit == $emisora)
                            <h4>Rechazado Por {{ $empresa->razon_social}}</h4>
                        @endif
                    @endforeach
                </div>
                <div class="mb-4">
                    <h4>Motivo: {{$descripcion}}</h4>
                </div>
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="visto" wire:loading.remove>
                Visto
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="actualizar">Cargando...</span>
        </x-slot>
    </x-dialog-modal>
</div>

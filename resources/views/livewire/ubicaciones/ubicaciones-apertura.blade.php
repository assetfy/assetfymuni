<div>
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            @if ($aperturas1->count() > 0)
                 Registre una nueva Apertura o elija algunas de los demas bloques
            @else
                Crear Nueva Apertura
            @endif
        </x-slot>
        <form wire:submit.prevent="save">
              <x-slot name="content">
            <form class="row g-3">
                <div class="col-md-12">
                    <x-label value="Nombre" />
                    <x-input type="text" class="w-full" wire:model.lazy="nombre" />
                    <x-input-error for="nombre" />
                </div>
                <div class="col-md-4">
                    @if ($aperturas1->count() > 0)
                       <select class="form-control" wire:model.lazy="id_apertura2">
                           <option value="" hidden>Bloque 2</option>
                           @foreach ($aperturas1 as $apertura)
                               <option value="{{ $apertura->id_apertura_1 }}">{{ $apertura->nombre }}</option>
                           @endforeach
                       </select>
                       <x-input-error for="id_apertura2" />
                   @endif
                </div>
                <div class="col-md-4">
                    @if ($aperturas2->count() > 0)
                    <select class="form-control" wire:model.lazy="id_apertura3">
                        <option value="" hidden>Bloque 3 </option>
                        @foreach ($aperturas2 as $apertura)
                            <option value="{{ $apertura->id_apertura_2 }}"> {{ $apertura->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="id_apertura3" />
                    @endif
                </div>
                <div class="col-md-4">
                    @if ($aperturas3->count() > 0)
                    <select class="form-control" wire:model.lazy="id_apertura4">
                        <option value="" hidden>Bloque 4 </option>
                        @foreach ($aperturas3 as $apertura)
                            <option value="{{ $apertura->id_apertura_3 }}"> {{ $apertura->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="id_apertura4" />
                    @endif
                </div>
              
        </form>
    </x-slot>
        <x-slot name="footer">
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <x-secondary-button class="close" wire:click="close">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                Crear Aperturas
            </x-danger-button>
            <span
                class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                wire:loading wire:target="save">Cargando...</span>
        </div>
        </x-slot>
    </x-dialog-modal>

 
</div>

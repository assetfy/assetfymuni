<div>
    <button type="button" class="btn btn-info btn-block" wire:click="$set('open', true)">
        Asignar Encargado
    </button>
    <x-dialog-modal wire:model="open" maxWidth=3xl>
        <x-slot name="title">
            Encargado
        </x-slot>
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <x-slot name="content">
                <div class="mb-4">
                    @if ($servicios)
                        <x-label value="Servicios" />
                        <select class="form-control" wire:model="id_servicio">
                            <option value="" hidden>Seleccione El servicio</option>
                            @foreach ($servicios as $servicio)
                                <option value="{{ $servicio->id_servicio }}">{{ $servicio->descripcion }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="id_servicio" />
                    @endif
                </div>
                <div class="mb-4">
                    <x-label value="Seleccionar si es único" />
                    <select class="form-control" wire:model.live="representante">
                        <option value="" hidden>único</option>
                        <option value="Si">Si</option>
                        <option value="No">No</option>
                    </select>
                    <x-input type="hidden" class="w-full" wire:model="representante" />
                    <x-input-error for="representante" />
                </div>
                <div class="mb-4">
                    <x-label value="CUIL" />
                    <div class="flex">
                        <x-input type="text" class="w-full" wire:model.live="cuil"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <x-button wire:click="buscarUsuario" class="ml-2">Buscar</x-button>
                    </div>
                    <x-input-error for="cuil" />
                </div>
                @if ($usuarios)
                    <div>
                        <p>Nombre De usuario: <span class="fw-bold text-primary">{{ $usuarios->name }}</span></p>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Solicitar Servicio
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

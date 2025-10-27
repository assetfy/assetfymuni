<div>
    <x-dialog-modal wire:model.live="open" maxWidth=3xl>
        <x-slot name="title">
            <div class="text-center text-2xl font-bold text-gray-800">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2"> Crear Servicios Actividades</h2>
            </div>
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <div class="mb-4">
                    <x-label value="Servicio" />
                    <select wire:model.lazy="id_servicio" class="form-control">
                        <option value="" hidden>Seleccione un Servicio</option>
                        @foreach ($servicios as $servicio)
                            <option value="{{ $servicio->id_servicio }}">{{ $servicio->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="id_servicio" />
                </div>
                <div class="mb-4">
                    <x-label value="Actividades economicas" />
                    <select wire:model="id_actividad" class="form-control">
                        <option value="" hidden>Seleccione una Actividad</option>
                        @foreach ($actividades as $actividad)
                            <option value="{{ $actividad->COD_ACTIVIDAD }}">{{ $actividad->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="id_actividad" />
                </div>
                @if ($panel == '1')
                    <div class="mb-4">
                        <x-label value="Tiene Vencimiento" />
                        <select wire:model.live="tiene_vencimiento" class="form-control">
                            <option value="" hidden>Seleccione</option>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                        <x-input-error for="tiene_vencimiento" />
                    </div>
                    @if ($tiene_vencimiento === 'Si')
                        <div class="mb-4">
                            <x-label value="Tipo de Vencimiento" />
                            <select wire:model="mensual_o_x_dias" class="form-control">
                                <option value="" hidden>Seleccione si vence por día o por mes</option>
                                <option value="Mes">Meses</option>
                                <option value="Dias">Días</option>
                            </select>
                            <x-input-error for="mensual_o_x_dias" />
                        </div>
                        <div class="mb-4" wire:ignore.self>
                            <x-label value="Cantidad" />
                            <input type="number" wire:model="cantidad_dias_o_meses" class="form-control"
                                placeholder="Ingrese el número de días o meses" min="1" />
                            <x-input-error for="cantidad_dias_o_meses" />
                        </div>
                    @endif
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button class="mr-2" wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                    Crear Servicios Actividades
                </x-danger-button>
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">Cargando...</span>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

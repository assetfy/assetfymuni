<div>
    <x-dialog-modal wire:model.live="open"  maxWidth=3xl>
        <x-slot name="title">
            Crear Actividad Regulada
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <div class="mb-3">
                    <x-label value="Provincia" />
                    <select wire:model="selectedProvincia" class="form-control">
                        <option value="" hidden>Seleccione Provincia</option>
                        @foreach ($estados as $estado)
                        <option value="{{ $estado->cuit }}">{{ $estado->razon_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <x-label value="Seleccionar Actividad" />
                    @if ($actividades !== null && $actividades->isNotEmpty())
                    <select id="selectActividad" class="form-control" wire:model="selectedActividad" wire:loading.attr="disabled" wire:change="loadEmpresas">
                        <option value="">Seleccione una actividad</option>
                        @foreach ($actividades as $act)
                        <option value="{{ $act->COD_ACTIVIDAD }}">{{ $act->nombre }}</option>
                        @endforeach
                    </select>
                    @endif
                    <x-input-error for="selectedActividad" />
                </div>

                <!-- Mostrar la selección de empresa solo si hay actividad seleccionada -->
                @if($selectedActividad)
                    <div class="mb-3">
                        <x-label value="Seleccionar Empresa" />
                        @if($empresas->isNotEmpty())
                            <select wire:model="cuit" class="form-control">
                                <option value="" hidden>Seleccione una empresa</option>
                                @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->cuit }}">{{ $empresa->razon_social }}</option>
                                @endforeach
                            </select>
                        @else
                            <p class="text-gray-500">No existen empresas para esta actividad</p>
                        @endif
                        <x-input-error for="cuit" />
                    </div>
                @endif

                <div class="mb-3">
                    <x-label value="La renovación cada cuántos días se realiza" class="mb-2" />
                    <div class="flex items-center">
                        <x-input type="number" class="w-full mr-2" wire:model="renovacion" />
                        <span class="text-muted">días</span>
                    </div>
                    <x-input-error for="renovacion" />
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                        Crear Reguladora
                    </x-danger-button>
                    <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10" wire:loading wire:target="save">Cargando...</span>
                    <x-secondary-button class="close" wire:click="close">
                        Cancelar
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

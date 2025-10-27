<div>
    <x-dialog-modal wire:model.live="open" maxWidth=3xl>
        <x-slot name="title">
            Registrar Actividad
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <div class="mb-4">
                    @if ($actividad !== null)
                        @if ($actividad->isNotEmpty())
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
                    @else
                        <div class="alert alert-danger" wire:loading.remove>
                            No tienes actividades activas o para la provincia
                        </div>
                    @endif
                </div>
                <div class="mb-4">
                    @if ($ubicaciones)
                        <label for="ubicaciones">Seleccionar una Ubicacion:</label>
                        <select id="ubicaciones" wire:model="ubicaciones" wire:loading.attr="disabled">
                            <option value="">Ubicaciones</option>
                            @foreach ($ubicaciones as $ubicacion)
                                <option value="{{ $ubicacion->id_ubicacion }}">{{ $ubicacion->nombre }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </x-slot>
        </form>
        <x-slot name="footer">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                @if ($actividad != null && count($actividad) > 0 && !$actividad->contains('nombre'))
                    <!-- Este botón solo se mostrará si hay actividades y el nombre de la actividad no coincide con el valor de $cod -->
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                        Registrar Nueva Actividad
                    </x-danger-button>
                @endif
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">Cargando...</span>
                <x-secondary-button class="close" wire:click="close">
                    Cancelar
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

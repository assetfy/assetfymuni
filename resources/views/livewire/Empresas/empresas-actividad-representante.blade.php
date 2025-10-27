<div class="flex justify-center items-center flex-col">
    <x-dialog-modal wire:model.live="open">
        <x-slot name="title">
            <h2 class="text-lg font-semibold">Seleccionar Actividades</h2>
        </x-slot>
        <form wire:submit.prevent="save">
            <x-slot name="content">
                @if ($datos)
                    <p class="text-gray-700">ID Usuario: <span class="font-bold">{{ $datos->name }}</span></p>
                    <p class="text-gray-700 mb-4">Asigna las actividades habilitadas a tu representante técnico dentro de
                        tu empresa.</p>
                @endif
                @if ($actividades && $actividades->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data="{ selectedActividades: @entangle('selectedActividades') }">
                        @foreach ($actividades as $codigo)
                            @if (is_object($codigo) && isset($codigo->cod_actividad) && isset($codigo->nombre))
                                <label
                                    @click="selectedActividades.includes('{{ $codigo->cod_actividad }}') ? selectedActividades = selectedActividades.filter(id => id !== '{{ $codigo->cod_actividad }}') : selectedActividades.push('{{ $codigo->cod_actividad }}')"
                                    :class="{ 'bg-gray-300': selectedActividades.includes('{{ $codigo->cod_actividad }}') }"
                                    class="cursor-pointer block p-4 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <input type="checkbox" wire:model="selectedActividades"
                                        value="{{ $codigo->cod_actividad }}" id="actividad_{{ $codigo->cod_actividad }}"
                                        class="hidden">
                                    <div class="text-center">
                                        <h3 class="text-gray-900 font-semibold">{{ $codigo->nombre }}</h3>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('selectedActividades')
                        <span class="error text-red-500">{{ $message }}</span>
                    @enderror
                @else
                    <p class="text-gray-700">No hay actividades para asignar.</p>
                @endif

                @if ($actividadesCargadas && $actividadesCargadas->count() > 0)
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data="{ selectedActividades: @entangle('selectedActividades') }">
                        @foreach ($actividadesCargadas as $codigo)
                            @if (is_object($codigo) && isset($codigo->cod_actividad))
                                <label
                                    @click="selectedActividades.includes('{{ $codigo->cod_actividad }}') ? selectedActividades = selectedActividades.filter(id => id !== '{{ $codigo->cod_actividad }}') : selectedActividades.push('{{ $codigo->cod_actividad }}')"
                                    :class="{ 'bg-gray-300': selectedActividades.includes('{{ $codigo->cod_actividad }}') }"
                                    class="cursor-pointer block p-4 border border-gray-300 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                    <input type="checkbox" wire:model="selectedActividades"
                                        value="{{ $codigo->cod_actividad }}"
                                        id="actividad_{{ $codigo->cod_actividad }}" class="hidden">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-900 font-semibold">{{ $codigo->cod_actividad }}</span>
                                        <button wire:click.prevent="eliminarRegistro('{{ $codigo->cod_actividad }}')"
                                            class="text-red-600 hover:text-red-800">Eliminar</button>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('selectedActividades')
                        <span class="error text-red-500">{{ $message }}</span>
                    @enderror
                @else
                    <p class="text-gray-700">No hay actividades para eliminar.</p>
                @endif
            </x-slot>
            <x-slot name="footer">
                <div class="flex justify-end space-x-2">
                    @if ($actividades && $actividades->count() > 0)
                        <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                            Guardar
                        </x-danger-button>
                    @endif
                    <span
                        class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                        wire:loading wire:target="save">Cargando...</span>
                    <x-secondary-button wire:click="close">
                        Cancelar
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

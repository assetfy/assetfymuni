<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl">

        <!-- TÍTULO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Asignar Actividad Económica</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <div class="px-4 pt-2 space-y-6">

                <!-- Resumen de la empresa -->
                @if ($empresa)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600"><strong>Razón social:</strong> {{ $empresa->razon_social }}</p>
                        <p class="text-sm text-gray-600"><strong>CUIT:</strong> {{ $empresa->cuit }}</p>
                    </div>
                @endif
                <!-- Select de actividades económicas -->
                <div>
                    @if ($actividades)
                        <label for="actividad" class="block text-sm font-medium text-gray-700 mb-1">Actividad
                            Económica</label>
                        <select id="actividad" wire:model.defer="codActividad"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition">
                            <option value="">-- Selecciona una actividad --</option>
                            @foreach ($actividades as $act)
                                <option value="{{ $act->COD_ACTIVIDAD }}">
                                    {{ $act->COD_ACTIVIDAD }} — {{ $act->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="codActividad" class="mt-1 text-sm text-red-500" />
                    @endif
                </div>
            </div>
        </x-slot>

        <!-- FOOTER -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save" class="px-4 py-2 text-sm">
                    Actualizar
                </x-danger-button>
            </div>
        </x-slot>

    </x-dialog-modal>
</div>

<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">

        <!-- TÍTULO CENTRADO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Editar Contrato</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <form wire:submit.prevent="actualizarContrato" class="space-y-6 px-2 pt-2">

                <!-- EMPRESA -->
                <div>
                    <p class="text-lg font-semibold text-gray-700">
                        Empresa: {{ $nombreEmpresa }}
                    </p>
                </div>

                <!-- NÚMERO DE CONTRATO -->
                <div>
                    <x-label for="numeroContrato" value="Número de Contrato" class="text-gray-700 font-medium" />
                    <input type="text" id="numeroContrato" wire:model.lazy="numeroContrato" maxlength="10"
                        placeholder="Ingrese el número de contrato"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="numeroContrato" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- FECHA DEL CONTRATO -->
                <div>
                    <x-label for="fecha" value="Fecha del Contrato" class="text-gray-700 font-medium" />
                    <input type="date" id="fecha" wire:model.lazy="fecha"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="fecha" class="mt-1 text-sm text-red-500" />
                </div>
            </form>
        </x-slot>

        <!-- FOOTER -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="actualizarContrato" wire:loading.remove wire:target="actualizarContrato"
                    class="px-4 py-2 text-sm">
                    Guardar
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="actualizarContrato">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl">

        <!-- Title -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Editar Cliente</h2>
        </x-slot>

        <!-- Content -->
        <x-slot name="content">
            <div class="space-y-6 px-4 py-2">
                <!-- Nombre del Cliente -->
                <div>
                    <label for="nombreCliente" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre del Cliente
                    </label>
                    <input type="text" id="nombreCliente" wire:model.defer="nombreCliente"
                        @if (optional($clientes)->verificado === 'Si') disabled
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg shadow-sm cursor-not-allowed"
                        @else
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" @endif />
                    <x-input-error for="nombreCliente" class="mt-1 text-sm text-red-500" />
                    @if (optional($clientes)->verificado === 'Si')
                        <p class="text-sm text-gray-500 mt-1">
                            Cliente verificado: nombre no editable.
                        </p>
                    @endif
                </div>

                <!-- Números de Contrato -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Números de Contrato
                    </label>

                    <div class="flex space-x-2 mb-4">
                        <x-button wire:click="agregarContrato" class="h-9">
                            Agregar contrato
                        </x-button>
                    </div>

                    <div class="space-y-2">
                        @foreach ($contratos as $c)
                            <div class="flex items-center space-x-2" wire:key="cco-{{ $c['id_contrato'] ?? 'new' }}">
                                <x-input type="text" wire:model.defer="contratos.{{ $loop->index }}.contrato"
                                    class="w-full sm:w-1/2" placeholder="Número de contrato" />
                                <button type="button" wire:click="eliminarContrato({{ $c['id_contrato'] ?? 0 }})"
                                    class="w-8 h-8 bg-red-500 text-white rounded-full"
                                    title="Eliminar contrato">✖</button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-slot>

        <!-- Footer -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="actualizarCliente" class="px-4 py-2 text-sm">
                    Actualizar
                </x-danger-button>
            </div>
        </x-slot>

    </x-dialog-modal>
</div>

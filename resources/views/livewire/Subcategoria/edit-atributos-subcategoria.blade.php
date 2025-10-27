<div>
    <x-dialog-modal wire:model.live="open" maxWidth=3xl>
        <x-slot name="title">
            <!-- Título centrado con ícono elegante -->
            <div class="flex items-center justify-center space-x-3 text-center">
                <h2 class="font-semibold text-xl text-gray-800">Editar Atributos Subcategorías</h2>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Información agrupada en tarjeta visual con grid -->
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    @if ($tipos)
                        <div>
                            <x-label value="Tipo" class="text-sm text-gray-500" />
                            <div class="font-semibold text-md text-gray-700">{{ $tipos->nombre }}</div>
                        </div>
                    @endif
                    @if ($categoria)
                        <div>
                            <x-label value="Categoría" class="text-sm text-gray-500" />
                            <div class="font-semibold text-md text-gray-700">{{ $categoria->nombre }}</div>
                        </div>
                    @endif
                    @if ($subcategorias)
                        <div>
                            <x-label value="Subcategoría" class="text-sm text-gray-500" />
                            <div class="font-semibold text-md text-gray-700">{{ $subcategorias->nombre }}</div>
                        </div>
                    @endif
                    @if ($atributoNombre)
                        <div>
                            <x-label value="Atributo" class="text-sm text-gray-500" />
                            <div class="font-semibold text-md text-gray-700">{{ $atributoNombre->nombre }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Campos del formulario bien espaciados -->
            <div class="space-y-4">
                <div>
                    <x-label value="Obligatorio Inicial" />
                    <select class="form-control rounded-md" wire:model.defer="updateObligatorio"
                        id="ObligatorioCargaIniciarAtributo">
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="updateObligatorio" />
                </div>

                <div>
                    <x-label value="Carga Inicial" />
                    <select class="form-control rounded-md" wire:model.defer="updateUnico" id="unicoInicial">
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="updateUnico" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end w-full space-x-2">
                <x-secondary-button wire:click="$set('open', false)">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="guardarCambios" wire:loading.remove>
                    Actualizar
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="guardarCambios">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

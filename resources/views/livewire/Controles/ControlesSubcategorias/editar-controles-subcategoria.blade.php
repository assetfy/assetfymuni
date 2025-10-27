<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            Editar Controles Subcategoria
        </x-slot>
        <x-slot name="content">
            <form wire:submit.prevent="guardarCambios">
                <div class="flex flex-col items-center space-y-6">
                    <div class="w-full grid grid-cols-1 justify-center">
                        <div class="text-center">
                            <x-label value="Control" />
                            <div class="font-bold text-lg">
                                {{ $controles->firstWhere('id_control', $controlessub->id_control)->nombre ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <div class="w-full grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tipo -->
                        <div class="text-center">
                            <x-label value="Tipo" />
                            <div class="font-bold text-lg">
                                {{ $tipos->firstWhere('id_tipo', $controlessub->id_tipo)->nombre ?? 'N/A' }}
                            </div>
                        </div>
                        <!-- Categoria -->
                        <div class="text-center">
                            <x-label value="Categoria" />
                            <div class="font-bold text-lg">
                                {{ $categorias->firstWhere('id_categoria', $controlessub->id_categoria)->nombre ?? 'N/A' }}
                            </div>
                        </div>
                        <!-- Subcategoria -->
                        <div class="text-center">
                            <x-label value="Subcategoria" />
                            <div class="font-bold text-lg">
                                {{ $subcategorias->firstWhere('id_subcategoria', $controlessub->id_subcategoria)->nombre ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                    <!-- Carga Inicial y Periodico en una fila -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Carga Inicial -->
                        <div>
                            <x-label value="Carga Inicial" />
                            <select class="form-control w-full" wire:model.defer="upobligatorio_carga_ini">
                                <option value="No">No</option>
                                <option value="Si">Si</option>
                            </select>
                            <x-input-error for="upobligatorio_carga_ini" />
                        </div>
                        <!-- Periodico -->
                        <div>
                            <x-label value="Periodico" />
                            <select class="form-control w-full" wire:model.defer="upes_periodico">
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                            <x-input-error for="upes_periodico" />
                        </div>
                    </div>
                    <!-- Unico y Requiere Fotos en una fila -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Unico -->
                        <div>
                            <x-label value="Unico" />
                            <select class="form-control w-full" wire:model.defer="upunico">
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                            <x-input-error for="upunico" />
                        </div>
                        <!-- Requiere Fotos -->
                        <div>
                            <x-label value="Requiere Fotos" />
                            <select class="form-control w-full" wire:model.defer="upreq_foto">
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                            <x-input-error for="upreq_foto" />
                        </div>
                    </div>
                    <!-- Frecuencia Control y Cantidad Estandar de Control en una fila -->
                    <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Frecuencia Control -->
                        <div>
                            <x-label value="Frecuencia Control" />
                            <x-input type="number" class="w-full" wire:model.defer="upfrecuencia_control"
                                min="0" placeholder="Ingrese un número" />
                            <x-input-error for="upfrecuencia_control" />
                        </div>
                        <!-- Cantidad Estandar de Control -->
                        <div>
                            <x-label value="Cantidad Estandar de Control" />
                            <x-input type="number" class="w-full" wire:model.defer="upcantidad_estandar" min="0"
                                placeholder="Ingrese un número" />
                            <x-input-error for="upcantidad_estandar" />
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
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

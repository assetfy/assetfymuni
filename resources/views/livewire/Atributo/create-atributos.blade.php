<div>
    <x-dialog-modal wire:model.live="open" maxWidth=3xl>
        <x-slot name="title">
            <div class="text-center text-2xl font-bold text-gray-800">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2">Crear Atributo</h2>
            </div>
        </x-slot>

        <form wire:submit.prevent="save" class="space-y-6">
            <x-slot name="content">
                <!-- Nombre -->
                <div class="mb-4">
                    <x-label for="nombre" value="Nombre" />
                    <x-input type="text" id="nombre"
                        class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                        placeholder="Ejemplo: Nombre del atributo" wire:model.defer="nombre" />
                    <x-input-error for="nombre" />
                </div>

                <!-- Tipo de Campo -->
                <div class="mb-4">
                    <x-label for="tipo_campo" value="Tipo de Campo" />
                    <select id="tipo_campo"
                        class="form-control w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                        wire:model.live="tipo_campo">
                        <option value="" hidden>Seleccionar Tipo de Campo</option>
                        @foreach ($tiposCampos as $tiposCampo)
                            <option value="{{ $tiposCampo->id_tipo_campo }}">{{ $tiposCampo->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="tipo_campo" />
                </div>

                <!-- Unidad de Medida -->
                @if ($categorias2)
                    <div class="mb-4">
                        <x-label for="unidad_medida" value="Unidad de Medida" />
                        <select id="unidad_medida"
                            class="form-control w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                            wire:model.defer="unidad_medida">
                            <option value="" hidden>Seleccionar Unidad de Medida</option>
                            @foreach ($categorias2 as $unidad)
                                <option value="{{ $unidad->id_unidad_medida }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="unidad_medida" />
                    </div>
                @endif

                <!-- Descripción -->
                <div class="mb-4">
                    <x-label for="descripcion" value="Descripción" />
                    <x-input type="text" id="descripcion"
                        class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                        placeholder="Ejemplo: Detalle del atributo" wire:model.defer="descripcion" />
                    <x-input-error for="descripcion" />
                </div>

                <!-- Atributos Predefinidos -->
                @if ($this->tipo_campo == 1 || $this->tipo_campo == 2)
                    <div class="mb-4">
                        <x-label for="esPredefinido" value="¿Es un atributo predefinido?" />
                        <select id="esPredefinido"
                            class="form-control w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                            wire:model.live="esPredefinido">
                            <option value="" hidden>Seleccionar</option>
                            <option value="Si">Sí</option>
                            <option value="No">No</option>
                        </select>
                        <x-input-error for="esPredefinido" />
                    </div>

                    <div class="mb-4">
                        <x-label for="esMultiple" value="¿Es un atributo de selecion multiple?" />
                        <select id="esMultiple"
                            class="form-control w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                            wire:model.live="esMultiple">
                            <option value="" hidden>Seleccionar</option>
                            <option value="Si">Sí</option>
                            <option value="No">No</option>
                        </select>
                        <x-input-error for="esMultiple" />
                    </div>

                    <!-- Agregar Valores -->
                    @if ($esPredefinido === 'Si')
                        <div class="mb-4">
                            <button wire:click.prevent="agregarValor"
                                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-lg">
                                + Agregar Valor
                            </button>
                        </div>

                        @if (count($valores) > 0)
                            <div class="space-y-4">
                                @foreach ($valores as $index => $valor)
                                    <div class="flex items-center space-x-3">
                                        @if ($tipo_campo == 2)
                                            <!-- Campo numérico -->
                                            <x-input type="number" wire:model="valores.{{ $index }}"
                                                class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                                                placeholder="Ejemplo: 12345" />
                                        @else
                                            <!-- Campo alfanumérico -->
                                            <x-input type="text" wire:model="valores.{{ $index }}"
                                                class="w-full rounded-lg border-gray-300 focus:ring focus:ring-blue-300 focus:outline-none text-lg py-3 px-4"
                                                placeholder="Ejemplo: Texto aquí" />
                                        @endif
                                        <button wire:click.prevent="eliminarValor({{ $index }})"
                                            class="bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 text-lg">
                                            Eliminar
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                @endif
            </x-slot>

            <!-- Footer -->
            <x-slot name="footer">
                <div class="flex justify-end space-x-4">
                    <x-secondary-button wire:click="close"
                        class="px-5 py-3 rounded-lg bg-gray-300 text-gray-700 hover:bg-gray-400 text-lg">
                        Cancelar
                    </x-secondary-button>

                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                        class="px-5 py-3 rounded-lg bg-red-500 text-white hover:bg-red-600 text-lg">
                        Crear Atributo
                    </x-danger-button>

                    <span
                        class="inline-flex items-center rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                        wire:loading wire:target="save">
                        Cargando...
                    </span>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

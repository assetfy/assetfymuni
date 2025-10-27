<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">

        <!-- TÍTULO CENTRADO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Editar Subcategoría</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <div class="space-y-6 px-2 pt-2">

                <!-- TIPO -->
                <div>
                    <x-label value="Tipo" class="text-gray-700 font-medium" />
                    @if ($tipoPruebas)
                        <div class="text-base text-gray-900 font-semibold">{{ $tipoPruebas->nombre }}</div>
                    @endif
                </div>

                <!-- CATEGORÍA -->
                <div>
                    <x-label value="Categoría" class="text-gray-700 font-medium" />
                    @if ($categorias)
                        <div class="text-base text-gray-900 font-semibold">{{ $categorias->nombre }}</div>
                    @endif
                </div>

                <!-- NOMBRE -->
                <div>
                    <x-label for="nombre" value="Nombre" class="text-gray-700 font-medium" />
                    <input type="text" id="nombre" wire:model.defer="updatedNombre"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedNombre" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- SIGLA -->
                <div>
                    <x-label for="sigla" value="Sigla" class="text-gray-700 font-medium" />
                    <input type="text" id="sigla" wire:model.defer="updatedSigla"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedSigla" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- MOVIL O FIJO (NO SE CAMBIA) -->
                <div class="mb-4">
                    <x-label value="Movil o Fijo" />
                    <select class="form-control" wire:model="updatedMovilofijo" id="MoviloFijo"
                        @if ($hasActivos) disabled @endif>
                        <option value="Movil">Movil</option>
                        <option value="Fijo">Fijo</option>
                    </select>
                    @if ($hasActivos)
                        <p class="text-red-500 text-sm mt-1">No se pueden editar este campo debido a que existen activos
                            creados a partir de esta subcategoría.</p>
                    @endif
                    <x-input-error for="updatedMovilofijo" />
                </div>

                <!-- SE RELACIONA (NO SE CAMBIA) -->
                <div class="mb-4">
                    <x-label value="Se relaciona" />
                    <select class="form-control" wire:model.defer="updatedRelacion" id="relacionRol"
                        @if ($hasActivos) disabled @endif>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                    @if ($hasActivos)
                        <p class="text-red-500 text-sm mt-1">No se pueden editar este campo debido a que existen activos
                            creados a partir de esta subcategoría.</p>
                    @endif
                    <x-input-error for="updatedRelacion" />
                </div>

                <!-- DESCRIPCIÓN -->
                <div>
                    <x-label for="descripcionSubcategoria" value="Descripción" class="text-gray-700 font-medium" />
                    <input type="text" id="descripcionSubcategoria" wire:model.defer="updatedDescripcion"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedDescripcion" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- IMAGEN -->
                <div>
                    <x-label value="Imagen" class="text-gray-700 font-medium" />
                    <div class="mt-2 flex items-center space-x-4">
                        @if ($updatedImagen)
                            <img src="{{ $updatedImagen->temporaryUrl() }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 shadow"
                                alt="Vista previa" />
                        @elseif ($imagen)
                            <img src="{{ asset('storage/' . $imagen) }}"
                                class="w-24 h-24 object-cover rounded-lg border border-gray-200 shadow"
                                alt="{{ $subcategoria->nombre }}" />
                        @else
                            <p class="text-sm text-gray-500">No hay imagen disponible.</p>
                        @endif
                    </div>
                    <div class="mt-3">
                        <input type="file" wire:model="updatedImagen"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-medium
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100 transition" />
                        <x-input-error for="updatedImagen" class="mt-1 text-sm text-red-500" />
                    </div>
                </div>
            </div>
        </x-slot>

        <!-- FOOTER -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="dispatch('check')" wire:loading.remove class="px-4 py-2 text-sm">
                    Actualizar
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="actualizar">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

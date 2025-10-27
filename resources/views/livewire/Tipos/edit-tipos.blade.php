<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl">

        <!-- TITULO CENTRADO Y BONITO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Editar Tipo</h2>
        </x-slot>

        <!-- CONTENIDO DEL MODAL -->
        <x-slot name="content">
            <div class="space-y-6 px-2 pt-2">

                <!-- SIGLA -->
                <div>
                    <label for="tiposigla" class="block text-sm font-medium text-gray-700 mb-1">Sigla</label>
                    <input type="text" id="tiposigla" wire:model.defer="updatedSigla"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" />
                    <x-input-error for="updatedSigla" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- NOMBRE -->
                <div>
                    <label for="tiponombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="tiponombre" wire:model.defer="updatedNombre"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" />
                    <x-input-error for="updatedNombre" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- DESCRIPCION -->
                <div>
                    <label for="tipodescripcion"
                        class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <input type="text" id="tipodescripcion" wire:model.defer="updatedDescripcion"
                        class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm transition" />
                    <x-input-error for="updatedDescripcion" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- IMAGEN -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen</label>
                    <div class="flex items-center space-x-4">
                        @if ($updatedImagen)
                            <img src="{{ $updatedImagen->temporaryUrl() }}"
                                class="w-24 h-24 object-cover rounded-lg shadow border border-gray-200"
                                alt="Vista previa" />
                        @elseif ($imagen)
                            <img src="{{ asset('storage/' . $imagen) }}"
                                class="w-24 h-24 object-cover rounded-lg shadow border border-gray-200" />
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
        <!-- FOOTER DEL MODAL -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="$dispatch('check')" class="px-4 py-2 text-sm">
                    Actualizar
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

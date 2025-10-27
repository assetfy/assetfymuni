<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">

        <!-- TÍTULO CENTRADO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800 tracking-tight">EDITAR CATEGORÍA</h2>
        </x-slot>

        <!-- CONTENIDO DEL MODAL -->
        <x-slot name="content">
            <div class="space-y-6 px-2 pt-2">

                <!-- TIPO (Solo lectura) -->
                @if ($categoria)
                    <div>
                        <x-label value="Tipo" class="text-gray-700 font-medium mb-1" />
                        @foreach ($tipoPrueba as $tipo)
                            @if ($categoria->id_tipo == $tipo->id_tipo)
                                <div class="text-base text-gray-900 font-semibold">{{ $tipo->nombre }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
                <!-- SIGLA -->
                <div>
                    <x-label for="Categoriasigla" value="Sigla" class="text-gray-700 font-medium" />
                    <input type="text" id="Categoriasigla" wire:model.defer="updatedSigla"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedSigla" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- NOMBRE -->
                <div>
                    <x-label for="Categorianombre" value="Nombre" class="text-gray-700 font-medium" />
                    <input type="text" id="Categorianombre" wire:model.defer="updatedNombre"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedNombre" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- DESCRIPCIÓN -->
                <div>
                    <x-label for="Categoriadescripcion" value="Descripción" class="text-gray-700 font-medium" />
                    <input type="text" id="Categoriadescripcion" wire:model.defer="updatedDescripcion"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <x-input-error for="updatedDescripcion" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- IMAGEN -->
                <div>
                    <x-label value="Imagen" class="text-gray-700 font-medium" />
                    <div class="mt-2 flex items-center space-x-4">
                        <div class="flex items-center space-x-4">
                            @if ($updatedImagenCategoria)
                                <img src="{{ $updatedImagenCategoria->temporaryUrl() }}"
                                    class="w-24 h-24 object-cover rounded-lg shadow border border-gray-200"
                                    alt="Vista previa" />
                            @elseif ($imagen)
                                <img src="{{ asset('storage/' . $imagen) }}"
                                    class="w-24 h-24 object-cover rounded-lg shadow border border-gray-200" />
                            @else
                                <p class="text-sm text-gray-500">No hay imagen disponible.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <input type="file" wire:model="updatedImagenCategoria"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-medium
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100 transition" />
                        <x-input-error for="updatedImagenCategoria" class="mt-1 text-sm text-red-500" />
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

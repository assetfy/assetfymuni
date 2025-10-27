<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">

        <!-- TÍTULO CENTRADO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">Crear nuevo Tipo</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <form wire:submit.prevent="save" class="space-y-6 px-2 pt-2">

                <!-- SIGLA -->
                <div>
                    <x-label for="sigla" value="Sigla" class="text-gray-700 font-medium" />
                    <input type="text" id="sigla" wire:model="sigla"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s-.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="sigla" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- NOMBRE -->
                <div>
                    <x-label for="nombre" value="Nombre" class="text-gray-700 font-medium" />
                    <input type="text" id="nombre" wire:model.lazy="nombre"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s-.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="nombre" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- DESCRIPCIÓN -->
                <div>
                    <x-label for="descripcion" value="Descripción" class="text-gray-700 font-medium" />
                    <input type="text" id="descripcion" wire:model.lazy="descripcion"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="descripcion" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- IMAGEN -->
                <div class="space-y-4">
                    <x-label value="Imagen" class="text-gray-700 font-medium" />
                    <div class="flex items-center justify-start">
                        <div class="relative w-32 h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer"
                            onclick="document.getElementById('imagen').click()">
                            @if ($imagen)
                                <img src="{{ $imagen->temporaryUrl() }}" alt="Preview"
                                    class="w-full h-full object-cover rounded-lg" />
                                <button type="button" title="Eliminar Imagen"
                                    class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition"
                                    wire:click="removeImagen">
                                    &times;
                                </button>
                            @else
                                <span class="text-3xl text-gray-500">+</span>
                            @endif
                        </div>
                    </div>

                    <!-- INPUT DE ARCHIVO OCULTO -->
                    <input type="file" id="imagen" class="hidden" wire:model="imagen" />

                    <!-- ERRORES Y CARGANDO -->
                    <x-input-error for="imagen" class="text-sm text-red-500" />
                    <div wire:loading wire:target="imagen" class="text-sm text-blue-500 mt-1">Cargando imagen...</div>
                </div>
            </form>
        </x-slot>

        <!-- FOOTER -->
        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-3 px-2">
                <x-secondary-button wire:click="close" class="px-4 py-2 text-sm">
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save" wire:loading.remove wire:target="save" class="px-4 py-2 text-sm">
                    Crear Tipo
                </x-danger-button>

                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

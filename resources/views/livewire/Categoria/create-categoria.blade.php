<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <!-- TÍTULO -->
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">CREAR CATEGORÍA</h2>
        </x-slot>

        <!-- CONTENIDO -->
        <x-slot name="content">
            <form wire:submit.prevent="save" class="space-y-6 px-2 pt-2">
                <!-- Dropdown de Tipo -->
                <div class="mb-4" x-data="{
                    openDropdown: false,
                    searchTimeout: null,
                    iniciarBusqueda() {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            $wire.call('iniciarBusqueda');
                        }, 500);
                    }
                }" @click.away="openDropdown = false">
                    <x-label value="Tipo" class="text-gray-700 font-medium mb-1" />
                    <div class="relative">
                        <button type="button" @click="openDropdown = !openDropdown"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <span>
                                @if ($id_tipo && $selectedTipoNombre)
                                    {{ $selectedTipoNombre }}
                                @else
                                    Seleccione un tipo
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="openDropdown" x-transition
                            class="absolute z-10 mt-1 w-full bg-white rounded-md shadow-lg">
                            <div class="p-2">
                                <input type="text" wire:model.defer="searchTipo" @input="iniciarBusqueda"
                                    @keydown.enter.prevent="$event.target.blur(); iniciarBusqueda();"
                                    @keydown.escape="openDropdown = false; $wire.searchTipo = ''"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-indigo-500"
                                    placeholder="Buscar tipo...">
                            </div>
                            <ul class="max-h-60 overflow-auto rounded-md py-1 text-sm ring-1 ring-black ring-opacity-5">
                                @forelse ($tipoPrueba as $tipoCargado)
                                    <li wire:click="setTipo({{ $tipoCargado->id_tipo }})"
                                        @click="openDropdown = false; $wire.set('searchTipo', '')"
                                        class="cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-indigo-100">
                                        {{ $tipoCargado->nombre }}
                                    </li>
                                @empty
                                    <li class="cursor-default py-2 px-3 text-gray-700">No se encontraron tipos.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" wire:model="id_tipo" />
                    <x-input-error for="id_tipo" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Campo de Sigla -->
                <div class="mb-4">
                    <x-label for="sigla" value="Sigla" class="text-gray-700 font-medium" />
                    <x-input type="text" id="sigla" wire:model.defer="sigla"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="sigla" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Campo de Nombre -->
                <div class="mb-4">
                    <x-label for="nombre" value="Nombre" class="text-gray-700 font-medium" />
                    <x-input type="text" id="nombre" wire:model.defer="nombre"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="nombre" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Campo de Descripción -->
                <div class="mb-4">
                    <x-label for="descripcion" value="Descripción" class="text-gray-700 font-medium" />
                    <x-input type="text" id="descripcion" wire:model.defer="descripcion"
                        class="mt-1 block w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition"
                        oninput="this.value = this.value.replace(/[^a-zA-Z\s\-_.,!¡¿?áéíóúÁÉÍÓÚñÑ]+/g, '')" />
                    <x-input-error for="descripcion" class="mt-1 text-sm text-red-500" />
                </div>

                <!-- Campo de Imagen -->
                <div class="space-y-4">
                    <x-label value="Imagen" class="text-gray-700 font-medium" />
                    <div class="flex items-center justify-start">
                        <div class="relative w-32 h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center cursor-pointer"
                            onclick="document.getElementById('imagenCategoria').click()">
                            @if ($imagenCategoria)
                                <img src="{{ $imagenCategoria->temporaryUrl() }}" alt="Preview"
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
                    <input type="file" id="imagenCategoria" class="hidden" wire:model="imagenCategoria">
                    <x-input-error for="imagenCategoria" class="text-sm text-red-500" />
                    <div wire:loading wire:target="imagenCategoria" class="text-sm text-blue-500 mt-1">
                        Cargando imagen...
                    </div>
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
                    Crear Nueva Categoría
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

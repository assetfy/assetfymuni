<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl" aria-hidden="true">
        <x-slot name="title">
            <div class="text-center space-y-2">
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fa-solid fa-layer-group text-indigo-600 mr-2"></i>
                    Crear Nivel de Organización
                </h2>
                <p class="text-sm text-gray-500">Configura la jerarquía organizacional con facilidad</p>
            </div>
        </x-slot>

        <x-slot name="content">
            <form wire:submit.prevent="save" class="space-y-8 px-6 py-4">
                {{-- Dropdown buscable --}}
                <div>
                    <x-searchable-dropdown label="Depende de (opcional)" icon="fa-solid fa-sitemap" :options="$nivelesPlano"
                        model="padreId" search-model="searchPadre" select-method="setPadre" value-key="Id"
                        label-key="Nombre" :selected="$padreNombre" />
                    @error('padreId')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                {{-- Campo de nombre --}}
                <div>
                    <x-label for="nombre" value="Nombre del Nivel" class="font-semibold text-gray-700" />
                    <div class="relative mt-1">
                        <input id="nombre" type="text" wire:model.defer="nombre"
                            class="block w-full pl-8 pr-4 py-2.5 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-gray-800 text-sm"
                            placeholder="Escribe el nombre del nivel" />
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </span>
                    </div>
                    @error('nombre')
                        <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div wire:ignore class="mt-4">
                    <div class="max-h-[400px] overflow-auto border rounded">
                        <div id="orgchart_google_1"></div>
                    </div>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <div class="w-full flex justify-end space-x-4 px-6 pb-4">
                <x-secondary-button wire:click="close" class="px-5 py-2 text-sm flex items-center">
                    <i class="fa-solid fa-xmark mr-2"></i> Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                    class="px-5 py-2 text-sm flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Crear Nivel
                </x-danger-button>

                <span wire:loading wire:target="save"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    Creando...
                </span>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

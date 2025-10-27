<div>
    <x-dialog-modal wire:model.live="open" maxWidth=2xl>
        <x-slot name="title">
            Editar ruta
        </x-slot>
        <x-slot name="content">
            <!-- Nombre -->
            <div class="mb-6">
                <x-label for="nombre" value="Nombre de la ruta" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <x-input id="nombre" type="text"
                        class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        wire:model.defer="nombre" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A11.963 11.963 0 0112 15c2.28 0 4.419.64 6.242 1.746M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    Nombre de la ruta actual: <span class="text-red-500">{{ $nombre }}</span>
                </p>
            </div>

            <!-- Ruta -->
            <div class="mb-6">
                <x-label for="ruta" value="Ruta del sistema" class="text-sm font-semibold text-gray-700" />
                <div class="relative">
                    <x-input id="ruta" type="text"
                        class="pl-10 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        wire:model.defer="ruta" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-500">
                    URL del componente (vista o modal): <span class="text-red-500">{{ $ruta }}</span>
                </p>
            </div>

            <!-- Configurable -->
            <div class="mb-6">
                <x-label for="configurable" value="¿Ruta configurable?" class="text-sm font-semibold text-gray-700" />
                <p class="text-sm text-gray-500 mb-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L6 21h12l-3.75-4M12 3v12" />
                    </svg>
                    Actualmente, esta ruta es <span class="text-red-500">{{ $configurable }}</span> configurable.
                </p>
                <select id="configurable"
                    class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    wire:model.defer="configurable">
                    <option value="" hidden>Seleccione una opción</option>
                    <option value="Si">Sí</option>
                    <option value="No">No</option>
                </select>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button class="mr-2" wire:click="$set('open',false)">
                Cancelar
            </x-secondary-button>
            <x-danger-button wire:click="actualizar">
                Actualizar
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>

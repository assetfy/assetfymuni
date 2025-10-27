<div>
    <!-- Modal -->
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <!-- Título del Modal -->
        <x-slot name="title">
            <div class="flex items-center space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />
                </svg>
                <h2 class="text-xl font-semibold text-gray-800">Registrar nueva ruta</h2>
            </div>
        </x-slot>

        <!-- Contenido del Modal -->
        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Campo para Nombre -->
                <div class="mb-6">
                    <x-label for="nombre" value="Nombre" class="text-sm font-semibold text-gray-700" />
                    <div class="relative">
                        <x-input id="nombre" type="text"
                            class="pl-10 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            wire:model.lazy="nombre" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <!-- Icono -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A11.963 11.963 0 0112 15c2.28 0 4.419.64 6.242 1.746M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Este es el nombre visible que verá el usuario. Debe reflejar claramente la función o destino de
                        la ruta.
                    </p>
                    <x-input-error for="nombre" class="mt-2 text-sm text-red-600" />
                </div>

                <!-- Campo para Ruta -->
                <div class="mb-6">
                    <x-label for="ruta" value="Ruta (URL interna)" class="text-sm font-semibold text-gray-700" />
                    <div class="relative">
                        <x-input id="ruta" type="text"
                            class="pl-10 w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                            wire:model.lazy="ruta" />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <!-- Icono -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Ingresa aquí la ruta técnica (por ejemplo, <code>/admin/usuarios</code>) que hace referencia a
                        la vista o modal en el sistema.
                    </p>
                    <x-input-error for="ruta" class="mt-2 text-sm text-red-600" />
                </div>

                <!-- Campo Configurable -->
                <div class="mb-6">
                    <x-label for="configurable" value="¿Ruta configurable?"
                        class="text-sm font-semibold text-gray-700" />
                    <p class="text-sm text-gray-500 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.75 17L6 21h12l-3.75-4M12 3v12" />
                        </svg>
                        Indica si esta ruta tendrá permisos personalizados para distintos usuarios.
                    </p>
                    <select id="configurable"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        wire:model.lazy="configurable">
                        <option value="" hidden>Seleccione una opción</option>
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                    <x-input-error for="configurable" class="mt-2 text-sm text-red-600" />
                </div>
            </x-slot>

            <!-- Footer del Modal -->
            <x-slot name="footer">
                <div class="flex justify-end space-x-4">
                    <!-- Botón Registrar -->
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-1 -mt-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Registrar ruta
                    </x-danger-button>

                    <!-- Cargando -->
                    <span
                        class="inline-flex items-center rounded-md bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 ring-1 ring-inset ring-blue-700/10"
                        wire:loading wire:target="save">
                        <svg xmlns="http://www.w3.org/2000/svg" class="animate-spin h-4 w-4 mr-1 text-blue-500"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v4m0 8v4m8-8h-4m-8 0H4" />
                        </svg>
                        Cargando...
                    </span>

                    <!-- Botón Cancelar -->
                    <x-secondary-button wire:click="$set('open', false)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1 -mt-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancelar
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <!-- Título centrado con ícono -->
        <x-slot name="title">
            <div class="flex flex-col items-center text-center space-y-2">
                <h2 class="text-xl font-bold text-gray-800">Crear Nuevo Grupo</h2>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Nombre del Grupo -->
            <div class="mb-6">
                <x-label value="Nombre del Grupo" class="font-semibold text-gray-700" />
                <x-input type="text" class="w-full mt-1" wire:model="nombre_grupo"
                    placeholder="Ej: Equipo de Marketing" />
                <x-input-error for="nombre_grupo" class="mt-1" />
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <x-label value="Descripción del Grupo" class="font-semibold text-gray-700" />
                <x-input type="text" class="w-full mt-1" wire:model="descripcion"
                    placeholder="Ej: Encargados de campañas y contenido" />
                <x-input-error for="descripcion" class="mt-1" />
            </div>

            <!-- Selección de Usuarios -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Usuarios disponibles -->
                <div>
                    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2 text-gray-700">
                        <svg class="w-5 h-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Usuarios Disponibles
                    </h3>
                    <div class="border rounded shadow-sm bg-white max-h-64 overflow-y-auto">
                        @if (count($filteredUsuarios) > 0)
                            <ul>
                                @foreach ($filteredUsuarios as $usuario)
                                    <li wire:click="selectUsuario({{ $usuario->id_usuario }})"
                                        class="cursor-pointer px-4 py-2 hover:bg-blue-100 transition border-b">
                                        {{ $usuario->usuarios->name }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="p-4 text-gray-500 text-sm">No se encontraron usuarios.</div>
                        @endif
                    </div>
                </div>

                <!-- Usuarios seleccionados -->
                <div>
                    <h3 class="text-lg font-semibold mb-2 flex items-center gap-2 text-gray-700">
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Usuarios Seleccionados
                    </h3>

                    @if (!empty($selectedUsers))
                        <div
                            class="border rounded shadow-sm bg-white max-h-64 overflow-y-auto p-3 flex flex-wrap gap-2">
                            @foreach ($selectedUsers as $userId)
                                @php
                                    $usuario = $usuarios->firstWhere('id_usuario', $userId);
                                @endphp
                                @if ($usuario)
                                    <div
                                        class="flex items-center bg-indigo-100 text-indigo-900 rounded-full px-3 py-1 text-sm shadow hover:bg-indigo-200 transition">
                                        <span>{{ $usuario->usuarios->name }}</span>
                                        <button type="button" wire:click="removeUsuario({{ $userId }})"
                                            class="ml-2 focus:outline-none text-indigo-700 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-gray-500 text-sm bg-white rounded">No hay usuarios seleccionados.</div>
                    @endif
                </div>
            </div>
        </x-slot>

        <!-- Footer -->
        <x-slot name="footer">
            <div class="flex justify-end space-x-4 mt-4">
                <x-secondary-button wire:click="close">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="save">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Crear Grupo
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

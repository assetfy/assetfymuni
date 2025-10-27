<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <!-- Título centrado + Icono -->
        <x-slot name="title">
            <div class="flex flex-col items-center text-center space-y-2">
                <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L5 12.25l1.5-1.5L9.75 14l7.75-7.75L19 8l-9.25 9z" />
                </svg>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Usuarios del Grupo</h2>
                    <p class="text-sm text-gray-600 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A9 9 0 1116.95 6.05M15 12h.01" />
                        </svg>
                        Rol asignado:
                        <strong class="text-indigo-600">{{ $nombreDelrol }}</strong>
                    </p>
                </div>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Nombre del grupo -->
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">{{ $nombreGrupo }}</h1>

            <!-- Tabla de usuarios -->
            @if ($usuarios && $usuarios->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-md font-semibold text-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-9a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                        Lista de Usuarios del Grupo
                    </h2>
                    <div class="overflow-x-auto rounded-lg shadow border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nombre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Acción
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">{{ $usuario->name }}</td>
                                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $usuario->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button wire:click="removeUsuario({{ $usuario->id }})"
                                                class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-1.5 px-3 rounded transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Remover
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Buscar y agregar usuarios -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Agregar Usuario</label>
                <div class="relative">
                    <x-input type="text" class="w-full pr-10" wire:model.debounce.300ms="searchUsuario"
                        placeholder="Buscar por nombre..." />
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            @if ($filteredUsuarios)
                <div class="border border-gray-200 rounded bg-white shadow-sm max-h-60 overflow-auto">
                    <ul>
                        @forelse ($filteredUsuarios as $usuario)
                            <li wire:click="addUsuario({{ $usuario->id }})"
                                class="cursor-pointer px-4 py-2 hover:bg-blue-100 transition-all border-b last:border-b-0">
                                <span class="font-medium">{{ $usuario->name }}</span>
                                <span class="text-sm text-gray-500">({{ $usuario->email }})</span>
                            </li>
                        @empty
                            <li class="px-4 py-2 text-gray-500">Sin resultados</li>
                        @endforelse
                    </ul>
                </div>
            @endif
        </x-slot>

        <!-- Footer con botón -->
        <x-slot name="footer">
            <div class="flex justify-end gap-3 mt-4">
                <x-secondary-button wire:click="close">
                    <svg class="w-4 h-4 inline-block mr-1 text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cerrar
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

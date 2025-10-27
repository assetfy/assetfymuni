<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <!-- Título centrado con icono -->
        <x-slot name="title">
            <div class="flex items-center justify-center space-x-2 text-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L5 12.25l1.5-1.5L9.75 14l7.75-7.75L19 8l-9.25 9z" />
                </svg>
                <span class="text-xl font-bold text-blue-700">Asignar Permiso al Grupo</span>
            </div>
        </x-slot>

        <x-slot name="content">
            <!-- Nombre del grupo -->
            <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">{{ $nombreGrupo }}</h1>

            <!-- Rol actualmente asignado -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol Actualmente Asignado</label>
                <div class="flex items-center border border-gray-300 bg-gray-50 rounded-md px-4 py-2 shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-gray-800">{{ $assignedRoleName }}</span>
                </div>
            </div>

            <!-- Usuarios del grupo -->
            @if ($usuarios && $usuarios->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-md font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-1 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-9a4 4 0 11-8 0 4 4 0 018 0zm6 4a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                        Usuarios del Grupo
                    </h2>
                    <div class="overflow-x-auto rounded-md shadow-md border">
                        <table class="min-w-full bg-white divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Nombre</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($usuarios as $usuario)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">{{ $usuario->name }}</td>
                                        <td class="px-6 py-4 text-gray-600 whitespace-nowrap">{{ $usuario->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Selección de rol -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccione un Rol</label>
                <div class="relative">
                    <select wire:model.live="selectedRole"
                        class="w-full border border-gray-300 bg-white rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @if (!$roles || $roles->isEmpty())
                            <option value="">No existen roles disponibles</option>
                        @else
                            <option value="">Seleccione un rol</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Permisos del rol -->
            @if ($selectedRole && $permisos && $permisos->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-md font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-1 text-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Permisos del Rol
                    </h2>
                    <ul class="list-disc list-inside text-gray-600 space-y-1">
                        @foreach ($permisos as $permiso)
                            <li>{{ $permiso->nombre }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-slot>

        <!-- Botones del modal -->
        <x-slot name="footer">
            <div class="flex justify-end space-x-3 mt-4">
                <x-secondary-button wire:click="$set('open', false)">
                    <svg class="w-4 h-4 inline-block mr-1 text-red-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelar
                </x-secondary-button>

                <x-danger-button wire:click="save">
                    <svg class="w-4 h-4 inline-block mr-1 text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

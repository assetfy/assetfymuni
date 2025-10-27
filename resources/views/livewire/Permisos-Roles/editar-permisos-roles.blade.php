<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center gap-2 text-center">
                <i class="fas fa-id-card-clip text-blue-500 text-lg"></i>
                <span class="font-semibold text-blue-800">Licencias y permisos</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="actualizar">
            <x-slot name="content">
                <!-- Info de usuario y licencia actual -->
                <div class="space-y-3">
                    <p class="flex items-center gap-2 text-gray-800">
                        <i class="fas fa-user-circle text-blue-500"></i>
                        <strong>Usuario:</strong>
                        <span class="ml-1">{{ $user->name ?? 'Usuario no encontrado' }}</span>
                    </p>

                    <div class="flex items-center gap-2">
                        <i class="fas fa-key text-blue-400"></i>
                        <p class="text-gray-800">
                            <strong>Licencia asignada:</strong>
                            @if (!empty($roles?->nombre))
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i class="fas fa-check-circle text-emerald-500"></i> {{ $roles->nombre }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                                    <i class="fas fa-circle-exclamation text-blue-400"></i> Licencia no encontrada
                                </span>
                            @endif
                        </p>
                    </div>

                    <!-- Selector de licencias disponibles -->
                    <div class="mt-4">
                        <p class="flex items-center gap-2 text-gray-800 mb-1">
                            <i class="fas fa-clipboard-check text-blue-500"></i>
                            <strong>Licencias disponibles:</strong>
                        </p>

                        @if (!empty($rolesDisponibles) && count($rolesDisponibles) > 0)
                            <div class="relative">
                                <i
                                    class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                                <select wire:model="id_rol" wire:change="updatePermisos($event.target.value)"
                                    class="w-full pl-10 mt-1 rounded-lg border border-blue-200 bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2">
                                    <option value="" hidden>Seleccione una Licencia</option>
                                    @foreach ($rolesDisponibles as $rol)
                                        <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div
                                class="mt-2 inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                                <i class="fas fa-info-circle text-blue-400"></i>
                                No existen Licencias para asignar.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Permisos del rol (nombre completo, sin truncar) -->
                <div class="mt-6">
                    @if (!empty($permiso) && count($permiso) > 0)
                        <label class="flex items-center gap-2 text-blue-800 font-medium mb-3">
                            <i class="fas fa-shield-halved text-emerald-500"></i>
                            <span>Permisos disponibles</span>
                        </label>

                        <!-- Grid responsivo en pastel -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                            @foreach ($permiso as $permisoItem)
                                <label
                                    class="flex items-start gap-2 rounded-xl border border-blue-100 bg-blue-50/40 px-3 py-2 shadow-sm min-w-0">
                                    <i class="fas fa-lock-open mt-0.5 text-emerald-500"></i>
                                    <input type="checkbox" value="{{ $permisoItem->id_permiso }}" checked disabled
                                        class="mt-0.5 rounded text-blue-600 focus:ring-blue-300">
                                    <span class="text-gray-800 whitespace-normal break-words leading-snug">
                                        {{ $permisoItem->nombre }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error for="id_permiso" class="mt-2" />
                    @else
                        <div
                            class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                            <i class="fas fa-circle-info text-blue-400"></i>
                            Este rol no tiene permisos asignados.
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3 w-full">
                    @if ($mostrarBotonModificar && !empty($permiso) && count($permiso) > 0)
                        <x-danger-button wire:click="actualizar" wire:loading.remove wire:target="actualizar"
                            class="inline-flex items-center gap-2 !bg-blue-500 !hover:bg-blue-600 !text-white !border-blue-500 !focus:ring-2 !focus:ring-blue-200">
                            <i class="fas fa-pen-to-square"></i>
                            Modificar Relación
                        </x-danger-button>

                        <span
                            class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200"
                            wire:loading wire:target="actualizar">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            Cargando...
                        </span>
                    @endif

                    <x-secondary-button
                        class="inline-flex items-center gap-2 border-blue-200 text-blue-700 hover:bg-blue-50 focus:ring-2 focus:ring-blue-200"
                        wire:click="close">
                        <i class="fas fa-circle-xmark"></i>
                        Cancelar
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

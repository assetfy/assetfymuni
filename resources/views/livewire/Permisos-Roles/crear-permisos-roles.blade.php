<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center gap-2 text-center">
                <i class="fas fa-diagram-project text-blue-500 text-lg"></i>
                <span class="font-semibold text-blue-800">Relación Licencia y Roles</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Licencias disponibles -->
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-blue-800 font-medium">
                        <i class="fas fa-id-badge text-blue-500"></i>
                        <span>Licencias disponibles</span>
                    </label>

                    <div class="relative mt-2">
                        <i class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                        <select
                            class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2"
                            wire:model="id_rol" wire:change="seleccionarRol($event.target.value)">
                            <option value="" hidden>Seleccione una Licencia</option>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-input-error for="id_rol" class="mt-2" />
                </div>

                @if ($id_rol)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Permisos Asignados -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 text-blue-800 font-medium">
                                    <i class="fas fa-shield-halved text-emerald-500"></i>
                                    <span>Permisos Asignados</span>
                                </label>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <i class="fas fa-check"></i> {{ count($permisosAsignados ?? []) }}
                                </span>
                            </div>

                            <div class="relative mb-3">
                                <i
                                    class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                                <input type="text" wire:model.live="buscarAsignados"
                                    class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2"
                                    placeholder="Buscar permisos asignados...">
                            </div>

                            <div class="overflow-y-auto max-h-72 rounded-lg border border-blue-100 bg-blue-50/30 p-2">
                                @forelse ($permisosAsignados as $permiso)
                                    <label
                                        class="flex items-start gap-2 rounded-md bg-white/80 border border-blue-100 px-3 py-2 mb-2 shadow-sm">
                                        <input type="checkbox" wire:model="id_permisos"
                                            value="{{ $permiso->id_permiso }}"
                                            class="mt-0.5 rounded text-blue-600 focus:ring-blue-300">
                                        <span class="text-gray-800 whitespace-normal break-words leading-snug">
                                            {{ $permiso->nombre }}
                                        </span>
                                    </label>
                                @empty
                                    <div
                                        class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                                        <i class="fas fa-circle-info text-blue-400"></i>
                                        No hay permisos asignados.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Permisos Disponibles -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 text-blue-800 font-medium">
                                    <i class="fas fa-list-check text-blue-500"></i>
                                    <span>Permisos Disponibles</span>
                                </label>
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200">
                                    <i class="fas fa-list"></i> {{ count($permisosDisponibles ?? []) }}
                                </span>
                            </div>

                            <div class="relative mb-3">
                                <i
                                    class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                                <input type="text" wire:model.live="buscarDisponibles"
                                    class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2"
                                    placeholder="Buscar permisos disponibles...">
                            </div>

                            <div class="overflow-y-auto max-h-72 rounded-lg border border-blue-100 bg-blue-50/30 p-2">
                                @forelse ($permisosDisponibles as $permiso)
                                    <label
                                        class="flex items-start gap-2 rounded-md bg-white/80 border border-blue-100 px-3 py-2 mb-2 shadow-sm">
                                        <input type="checkbox" wire:model="id_permisos"
                                            value="{{ $permiso->id_permiso }}"
                                            class="mt-0.5 rounded text-blue-600 focus:ring-blue-300">
                                        <span class="text-gray-800 whitespace-normal break-words leading-snug">
                                            {{ $permiso->nombre }}
                                        </span>
                                    </label>
                                @empty
                                    <div
                                        class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                                        <i class="fas fa-circle-info text-blue-400"></i>
                                        No hay permisos disponibles.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <x-input-error for="id_permisos" class="mt-3" />
                @endif
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3 w-full">
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                        class="inline-flex items-center gap-2 !bg-blue-500 !hover:bg-blue-600 !text-white !border-blue-500 !focus:ring-2 !focus:ring-blue-200">
                        <i class="fas fa-floppy-disk"></i>
                        Registrar Relación
                    </x-danger-button>

                    <span
                        class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-200"
                        wire:loading wire:target="save">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        Cargando...
                    </span>

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

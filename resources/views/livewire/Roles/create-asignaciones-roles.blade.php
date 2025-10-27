<div>
    <x-dialog-modal wire:model="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center gap-2 text-center">
                <i class="fas fa-people-arrows text-blue-500 text-lg"></i>
                <span class="font-semibold text-blue-800">Asignar Licencias</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Empresa -->
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-blue-800 font-medium">
                        <i class="fas fa-building text-blue-500"></i>
                        <span>Empresa</span>
                    </label>
                    @if ($empresa)
                        <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50/40 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-receipt text-blue-400 mt-0.5"></i>
                                <div>
                                    <div class="text-gray-900 font-semibold">{{ $empresa->razon_social }}
                                    </div>
                                    <div class="text-sm text-blue-700/80">CUIT: <span
                                            class="font-medium">{{ $empresa->cuit }}</span></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="mt-2 inline-flex items-center gap-2 rounded-md bg-blue-50 px-3 py-2 text-sm text-blue-700 ring-1 ring-inset ring-blue-200">
                            <i class="fas fa-circle-info text-blue-400"></i>
                            Empresa no encontrada para el CUIT seleccionado.
                        </div>
                    @endif
                    <x-input-error for="cuit" class="mt-2" />
                </div>
                <!-- Usuario -->
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-blue-800 font-medium">
                        <i class="fas fa-user-circle text-blue-500"></i>
                        <span>Usuario</span>
                    </label>

                    @if ($usuario_empresa)
                        <div class="relative mt-2">
                            <i class="fas fa-users absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                            <select
                                class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2"
                                wire:model.live="usuario">
                                <option value="" hidden>Seleccione Usuario</option>
                                @foreach ($usuario_empresa as $user)
                                    <option value="{{ $user->id_usuario }}">{{ $user->usuarios->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error for="usuario" class="mt-2" />
                    @endif
                </div>
                <!-- Licencia -->
                @if ($roles)
                    <div class="mb-2">
                        <label class="flex items-center gap-2 text-blue-800 font-medium">
                            <i class="fas fa-id-badge text-emerald-500"></i>
                            <span>Licencia</span>
                        </label>
                        <div class="relative mt-2">
                            <i class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                            <select
                                class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 py-2"
                                wire:model="id_rol">
                                <option value="" hidden>Seleccione Licencia</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id_rol }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-input-error for="id_rol" class="mt-2" />
                    </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-end gap-3 w-full">
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                        class="inline-flex items-center gap-2 !bg-blue-500 !hover:bg-blue-600 !text-white !border-blue-500 !focus:ring-2 !focus:ring-blue-200">
                        <i class="fas fa-paper-plane"></i>
                        Asignar Licencia
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

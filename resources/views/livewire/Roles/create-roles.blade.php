<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <div class="flex items-center justify-center gap-2 text-center">
                <i class="fas fa-id-badge text-blue-500 text-lg"></i>
                <span class="font-semibold text-blue-900">Registrar Licencias</span>
            </div>
        </x-slot>

        <form wire:submit.prevent="save">
            <x-slot name="content">
                <!-- Empresa / Razón Social -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-blue-900 font-medium">
                        <i class="fas fa-building text-blue-500"></i>
                        <span>Empresa / Organización</span>
                    </label>

                    @php $empresaSeleccionada = null; @endphp
                    @foreach ($empresas as $empresa)
                        @if ($cuit == $empresa->cuit)
                            @php $empresaSeleccionada = $empresa; @endphp
                        @endif
                    @endforeach

                    @if ($empresaSeleccionada)
                        <div class="mt-2 rounded-xl border border-blue-200 bg-blue-50/40 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-receipt text-blue-900 mt-0.5"></i>
                                <div>
                                    <div class="text-gray-900 font-semibold">{{ $empresaSeleccionada->razon_social }}
                                    </div>
                                    <div class="text-sm text-blue-700/80">
                                        CUIT: <span class="font-medium">{{ $cuit }}</span>
                                    </div>
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

                <!-- Nombre de la licencia -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-blue-900 font-medium mb-1">
                        <i class="fas fa-pencil-alt text-emerald-500"></i>
                        <span>Nombre de la licencia</span>
                    </label>

                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                        <x-input type="text" wire:model.lazy="nombre" placeholder="Ej.: Licencia edición de bienes"
                            class="w-full pl-10 rounded-lg border border-blue-200 bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300" />
                    </div>
                    <p class="mt-1 text-xs text-blue-800/70">Usá un nombre claro para identificar el alcance.</p>

                    <x-input-error for="nombre" class="mt-2" />
                </div>

                <!-- Tipo de Empresa -->
                @if ($tipoUsuario)
                    <div class="mb-2">
                        <label class="flex items-center gap-2 text-blue-900 font-medium mb-1">
                            <i class="fas fa-sitemap text-emerald-500"></i>
                            <span>Tipo de empresa</span>
                        </label>

                        <div class="relative">
                            <i class="fas fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                            <select wire:model="id_tipo"
                                class="mt-1 block w-full appearance-none pl-10 pr-9 bg-white border border-blue-200 text-gray-800 py-2 rounded-lg leading-tight focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition">
                                <option value="" hidden>Seleccione tipo de empresa</option>
                                @foreach ($tipoEmpresa as $tipo)
                                    <option value="{{ $tipo->id_tipo_empresa }}">{{ $tipo->tipo_empresa }}</option>
                                @endforeach
                            </select>
                            <i
                                class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-blue-300"></i>
                        </div>
                        <x-input-error for="id_tipo" class="mt-2" />
                    </div>
                @endif
            </x-slot>
        </form>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-3 w-full">
                @if (!is_null($cuit))
                    <x-danger-button wire:click="save" wire:loading.remove wire:target="save"
                        class="inline-flex items-center gap-2 !bg-blue-500 !hover:bg-blue-600 !text-white !border-blue-500 !focus:ring-2 !focus:ring-blue-200">
                        <i class="fas fa-save"></i>
                        Registrar Licencia
                    </x-danger-button>
                @endif

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
    </x-dialog-modal>
</div>

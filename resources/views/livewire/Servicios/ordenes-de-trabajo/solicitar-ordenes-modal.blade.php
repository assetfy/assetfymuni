<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <h2 class="text-center text-2xl font-semibold text-gray-800">
                <i class="fa-solid fa-tools text-blue-600"></i>
                Solicitar Orden de Trabajo
            </h2>
        </x-slot>

        <form wire:submit.prevent="save">
            <x-slot name="content">
                @if ($filteredTecnicos && !$Gestora)
                {{-- Asignar Técnico --}}
                <div class="mb-6" x-data="{ open: false }" @click.away="open = false">
                    <label class="block font-medium mb-1">Asignar a</label>
                    <div class="relative">
                        <button type="button" @click="open = !open"
                            class="w-full bg-white px-3 py-2 rounded text-left border">
                            <span>{{ $tecnicoBusqueda->usuarios->name ?? 'Seleccione técnico' }}</span>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute mt-1 w-full bg-white rounded max-h-60 overflow-auto border">
                            <input type="text" wire:model.live="searchTecnico"
                                class="w-full px-3 py-2 text-sm border-b" placeholder="Buscar..." />
                            <ul>
                                @forelse($filteredTecnicos as $t)
                                <li @click="open = false" wire:click="setIdTecnico({{ $t->id_usuario }})"
                                    class="px-3 py-2 hover:bg-blue-100 cursor-pointer text-sm">
                                    {{ $t->usuarios->name }}
                                </li>
                                @empty
                                <li class="px-3 py-2 text-gray-500 text-sm">Sin resultados</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    @error('id_tecnico')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                @if ($Gestora)
                <div class="mb-6">
                    <x-label class="text-sm text-gray-700 font-semibold mb-1" value="Empresa" />
                    <div x-data="{ openDropdownEmpresa: false }" @click.away="openDropdownEmpresa = false" class="relative">
                        <!-- Botón del dropdown -->
                        <button
                            type="button"
                            @click="openDropdownEmpresa = !openDropdownEmpresa"
                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <span class="block truncate">
                                @if ($cuit && $empresaBusqueda)
                                {{ $empresaBusqueda->razon_social }}
                                @else
                                Seleccione una Empresa
                                @endif
                            </span>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-gray-400"></i>
                            </span>
                        </button>

                        <!-- Dropdown -->
                        <div
                            x-show="openDropdownEmpresa"
                            x-transition
                            class="absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg">

                            <div class="p-2 border-b">
                                <input
                                    type="text"
                                    wire:model.live="searchEmpresa"
                                    @keydown.enter="openDropdownEmpresa = false"
                                    @keydown.escape="openDropdownEmpresa = false"
                                    class="w-full border border-blue-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                                    placeholder="Buscar Empresa..." />
                            </div>

                            <ul class="max-h-60 overflow-y-auto py-1 text-sm">
                                @forelse ($filteredEmpresas as $empresa)
                                <li
                                    wire:click="setCuitEmpresa({{ $empresa->cuit }})"
                                    @click="openDropdownEmpresa = false; @this.set('searchEmpresa', '');"
                                    class="cursor-pointer px-3 py-2 hover:bg-blue-100">
                                    {{ $empresa->razon_social }}
                                </li>
                                @empty
                                <li class="px-3 py-2 text-gray-500">Sin resultado</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    @error('cuit')
                    <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                {{-- Tipo de Servicio --}}
                <div class="mb-6">
                    <label class="block font-medium mb-1">Tipo de Servicio</label>
                    <div class="flex flex-wrap gap-4 mt-2">
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model.live="selectedTipoServicio" value="Correctivo/Reparación"
                                class="form-radio text-blue-600" />
                            <span>Correctivo/Reparación</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model.live="selectedTipoServicio" value="Preventivo"
                                class="form-radio text-blue-600" />
                            <span>Preventivo</span>
                        </label>
                    </div>
                    @error('selectedTipoServicio')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SLA Preventivo --}}
                @if ($selectedTipoServicio === 'Correctivo/Reparación')
                <div class="mb-6">
                    <label class="block font-medium mb-1">Acuerdo Nivel de Servicio (SLA)</label>
                    <div class="grid grid-cols-2 gap-3 mt-2">
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_4hs')" wire:model="sla_4hs" />
                            <span>Menos de 4 hs.</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_8hs')" wire:model="sla_8hs" />
                            <span>Dentro de 8 hs.</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_12hs')" wire:model="sla_12hs" />
                            <span>Dentro de 12 hs.</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="checkbox" wire:click="selectSLA('sla_24hs')" wire:model="sla_24hs" />
                            <span>Dentro de 24 hs.</span>
                        </label>
                    </div>
                </div>
                @endif

                {{-- SLA Correctivo --}}
                @if ($selectedTipoServicio === 'Preventivo')
                <div class="mb-6">
                    <label class="block font-medium mb-1">Programación / Periódico</label>
                    <div class="flex gap-4 mt-2">
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model.live="slaTipo" value="programado" />
                            <span>Programado</span>
                        </label>
                        <label class="inline-flex items-center space-x-2">
                            <input type="radio" wire:model.live="slaTipo" value="periodico" />
                            <span>Periódico</span>
                        </label>
                    </div>

                    @if ($slaTipo === 'programado')
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Fecha de inicio programada</label>
                        <input type="date" wire:model="fechaProgramada" min="{{ now()->toDateString() }}"
                            class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2 focus:ring-indigo-500" />

                        @error('fechaProgramada')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    @if ($slaTipo === 'periodico')
                    <div class="mt-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{
                                    inicio: @entangle('fechaInicio'),
                                    fin: @entangle('fechaFin')
                                }">
                            <div>
                                <label class="block text-sm font-medium mb-1">Fecha de inicio</label>
                                <input type="date" wire:model="fechaInicio" x-model="inicio"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Fecha de fin</label>
                                <input type="date" wire:model="fechaFin" x-model="fin"
                                    :min="inicio || new Date().toISOString().split('T')[0]"
                                    class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Periodicidad</label>
                            <select wire:model.live="periodicidad"
                                class="w-full px-3 py-2 rounded border focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Seleccione --</option>
                                <option value="diario">Diario</option>
                                <option value="semana">Cada semana</option>
                                <option value="2semanas">Cada 2 semanas</option>
                                <option value="mes">Cada mes</option>
                            </select>
                        </div>

                        @if (in_array($periodicidad, ['semana', '2semanas', 'mes']))
                        <div>
                            <label class="block text-sm font-medium mb-1">Días de la semana</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $d)
                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox" value="{{ $d }}"
                                        wire:model="diasSeleccionados"
                                        class="form-checkbox h-4 w-4 text-indigo-600" />
                                    <span class="text-sm text-gray-700">{{ $d }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endif

                {{-- Descripción --}}
                <div class="mb-6">
                    <label class="block font-medium mb-1">Descripción</label>
                    <textarea wire:model.live="descripcion" rows="3"
                        class="w-full px-3 py-2 rounded border focus:ring-2 focus:ring-indigo-500" placeholder="Describa el motivo..."></textarea>
                    @error('descripcion')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Subida de imágenes debajo -->
                <div class="border border-dashed border-blue-400 bg-blue-50 rounded p-4 space-y-3 text-sm mt-4">
                    <h3 class="font-semibold text-blue-700 flex items-center">
                        📸 Subir Imagen(es)
                    </h3>
                    <input type="file" wire:model="newImages" multiple class="w-full text-sm text-blue-700" />
                    @error('newImages.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($imagenesTrabajo && count($imagenesTrabajo) > 0)
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach ($imagenesTrabajo as $index => $file)
                        <li class="flex justify-between items-center">
                            <span>{{ $file->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeImage({{ $index }})"
                                class="text-red-500 hover:text-red-700 font-semibold">×</button>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end space-x-3 px-2">
                    <x-secondary-button wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                        Cancelar
                    </x-secondary-button>
                    <x-danger-button wire:click="save" class="px-4 py-2 text-sm">
                        Generar
                    </x-danger-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>
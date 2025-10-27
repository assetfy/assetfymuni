<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        <x-slot name="title">
            <h2 class="text-xl font-semibold text-gray-700 flex items-center justify-center space-x-2">
                <svg class="w-6 h-6 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5.121 17.804A4.002 4.002 0 016 16h12a4 4 0 11-1.879 1.804M9 14v2a2 2 0 104 0v-2m5-7H6m6 0v6m-6-6v6m12-6v6" />
                </svg>
                <span>Asignar Técnico Encargado</span>
            </h2>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <x-label value="Nombre de la Solicitud" class="font-medium text-gray-600 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h11M9 21V3m12 0l-6 6m0 0l-6-6m6 6V3">
                            </path>
                        </svg>
                    </x-label>
                    <div class="border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-700 shadow-sm">
                        {{ $solicitud->Nombre_solicitud ?? 'N/A' }}
                    </div>
                </div>

                <div>
                    <x-label value="Fecha de Finalización" class="font-medium text-gray-600 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </x-label>
                    <div class="border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-700 shadow-sm">
                        {{ $solicitud->fecha_finalizacion ?? 'N/A' }}
                    </div>
                </div>
                <!-- Select de Representante Técnico -->
                <div x-data="{ openDropdownTecnico: false }" @click.away="openDropdownTecnico = false">
                    <x-label value="Representante Técnico" class="font-medium text-gray-600 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5.121 17.804A4.002 4.002 0 016 16h12a4 4 0 11-1.879 1.804M9 14v2a2 2 0 104 0v-2m5-7H6m6 0v6m-6-6v6m12-6v6">
                            </path>
                        </svg>
                    </x-label>
                    <div class="relative">
                        <button type="button" @click="openDropdownTecnico = !openDropdownTecnico"
                            class="w-full bg-white border border-gray-300 rounded-lg shadow-sm px-4 py-2 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <span class="text-gray-700">
                                {{ $selectedTecnicoName ?? 'Seleccione a un técnico' }}
                            </span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform transform"
                                :class="{ 'rotate-180': openDropdownTecnico }" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 3a1 1 0 01.832.445l3 5A1 1 0 0113 10H7a1 1 0 01-.832-1.555l3-5A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-3-5A1 1 0 016 14h8a1 1 0 01.832-1.555l-3 5A1 1 0 0110 17z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="openDropdownTecnico" x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchTecnico"
                                    @keydown.enter="openDropdownTecnico = false"
                                    @keydown.escape="openDropdownTecnico = false"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Buscar técnico...">
                            </div>

                            <ul class="max-h-60 overflow-auto">
                                @forelse ($tecnicos ?? [] as $tecnico)
                                    <li wire:click="setTecnico({{ $tecnico->id_usuario }})"
                                        @click="openDropdownTecnico = false; $wire.set('searchTecnico', '')"
                                        class="cursor-pointer px-4 py-2 hover:bg-blue-100 transition">
                                        {{ $tecnico->usuarios->name }}
                                    </li>
                                @empty
                                    <li class="px-4 py-2 text-gray-600">
                                        {{ empty($searchTecnico) ? 'Sin opciones disponibles' : 'Sin resultados' }}
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Precio (Justo antes del Footer) -->
                <div>
                    <x-label value="Precio" class="font-medium text-gray-600 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 10c-4.41 0-8 3.59-8 8h16c0-4.41-3.59-8-8-8z">
                            </path>
                        </svg>
                    </x-label>
                    <div class="border border-gray-300 rounded-lg px-4 py-2 bg-gray-100 text-gray-700 shadow-sm">
                        {{ $solicitud->precio ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-2">
                <x-secondary-button wire:click="$set('open', false)"
                    class="px-4 py-2 text-gray-600 border-gray-300 transition hover:bg-gray-200">
                    Cancelar
                </x-secondary-button>
                <x-danger-button wire:click="asignarTecnico"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white transition">
                    Asignar Técnico
                </x-danger-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

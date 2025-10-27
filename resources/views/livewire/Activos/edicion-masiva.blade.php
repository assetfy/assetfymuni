<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <!-- Título del Modal -->
        <x-slot name="title">
            <div class="flex items-center justify-center space-x-3 w-full">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 21a7.5 7.5 0 0115 0v.75H4.5V21z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800">
                    Edición Masiva
                </h2>
            </div>
        </x-slot>
        <!-- Contenido del Modal -->
        <x-slot name="content">
            <div class="space-y-6 text-gray-700">
                <!-- Número de bienes -->
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 10h2l1 2h13l1-2h2M4 6h16M5 6l1.5-2.5a1 1 0 01.86-.5h9.28a1 1 0 01.86.5L19 6">
                        </path>
                    </svg>
                    <h3 class="text-base font-medium">
                        Número de bienes a modificar:
                        <span class="text-indigo-600 font-semibold">{{ $NroSelecionados }}</span>
                    </h3>
                </div>
                @if ($general)
                    <div>
                        <x-label value="Estado General" class="text-sm font-semibold text-gray-700" />
                        <div class="relative mt-2">
                            {{-- Icono dentro del select --}}
                            <i
                                class="fa-solid fa-tachometer-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select wire:model.live="id_estado_sit_general"
                                class="pl-10 w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="">Seleccione un estado</option>
                                @foreach ($general as $estado)
                                    <option value="{{ $estado->id_estado_sit_general }}">
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error for="id_estado_sit_general" />
                    </div>
                @endif
                <!-- Estado de Alta -->
                @if ($altas)
                    <div>
                        <x-label value="Estado de Alta" class="text-sm font-semibold text-gray-700" />
                        <div class="relative mt-2">
                            {{-- Icono dentro del select --}}
                            <i
                                class="fa-solid fa-check-circle absolute left-3 top-1/2 transform -translate-y-1/2 text-green-400"></i>
                            <select wire:model="id_estado_sit_alta"
                                class="pl-10 w-full bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="" hidden>Seleccione el Estado de Alta</option>
                                @foreach ($altas as $estado)
                                    <option value="{{ $estado->id_estado_sit_alta }}">
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error for="id_estado_sit_alta" />
                    </div>
                @endif
                <!-- Asignado a -->
                @if ($empresaPrestadora != 2)
                    <div class="space-y-2" x-data="{ openDropdownAsignado: false }" @click.away="openDropdownAsignado = false">
                        <x-label value="Persona Asignada" class="text-sm font-semibold text-gray-700" />
                        <div class="relative">
                            <button type="button" @click="openDropdownAsignado = !openDropdownAsignado"
                                class="w-full bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded-md shadow-sm flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M5.121 17.804A4 4 0 017 17h10a4 4 0 011.879.804M15 11a3 3 0 100-6 3 3 0 000 6zM9 11a3 3 0 100-6 3 3 0 000 6z">
                                        </path>
                                    </svg>
                                    <span>
                                        {{ $asignado_a === null ? 'Sin Asignado Seleccionado' : ($asignado_a ?: 'Seleccione persona asignada') }}
                                    </span>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="openDropdownAsignado" x-transition
                                class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchAsignado"
                                        @keydown.enter="openDropdownAsignado = false"
                                        @keydown.escape="openDropdownAsignado = false"
                                        class="w-full border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="Buscar asignado..." />
                                </div>
                                <ul class="max-h-60 overflow-auto">
                                    @if (!empty($empleadosLista))
                                        <li wire:click="setAsignadoA(null)" @click="openDropdownAsignado = false"
                                            class="px-4 py-2 cursor-pointer hover:bg-red-100 text-red-600 font-semibold">
                                            Sin Asignado
                                        </li>
                                        @foreach ($empleadosLista as $empleado)
                                            <li wire:click="setAsignadoA({{ $empleado->id }})"
                                                @click="openDropdownAsignado = false"
                                                class="px-4 py-2 cursor-pointer hover:bg-indigo-100">
                                                {{ $empleado->name }}
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="px-4 py-2 text-gray-500">No hay empleados disponibles.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" wire:model="asignado_a_id" />
                        <x-input-error for="asignado_a_id" />
                    </div>
                @endif

                <!-- Responsable -->
                <div class="space-y-2" x-data="{ openDropdownResponsable: false }" @click.away="openDropdownResponsable = false">
                    <x-label value="Responsable" class="text-sm font-semibold text-gray-700" />
                    <div class="relative">
                        <button type="button" @click="openDropdownResponsable = !openDropdownResponsable"
                            class="w-full bg-white border border-gray-300 text-gray-800 py-2 px-4 rounded-md shadow-sm flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 00-8 0v4a4 4 0 108 0V7zM12 15v4m0 0h-3m3 0h3" />
                                </svg>
                                <span>{{ $responsable === null ? 'Sin Responsable Seleccionado' : ($responsable ?: 'Seleccione responsable') }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openDropdownResponsable" x-transition
                            class="absolute mt-2 w-full bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchResponsable"
                                    @keydown.enter="openDropdownResponsable = false"
                                    @keydown.escape="openDropdownResponsable = false"
                                    class="w-full border border-gray-300 rounded-md px-3 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Buscar responsable..." />
                            </div>
                            <ul class="max-h-60 overflow-auto">
                                @if (!empty($empleadosLista))
                                    <li wire:click="setResponsable(null)" @click="openDropdownResponsable = false"
                                        class="px-4 py-2 cursor-pointer hover:bg-red-100 text-red-600 font-semibold">
                                        Sin Responsable
                                    </li>
                                    @foreach ($empleadosLista as $empleado)
                                        <li wire:click="setResponsable({{ $empleado->id }})"
                                            @click="openDropdownResponsable = false"
                                            class="px-4 py-2 cursor-pointer hover:bg-indigo-100">
                                            {{ $empleado->name }}
                                        </li>
                                    @endforeach
                                @else
                                    <li class="px-4 py-2 text-gray-500">No hay empleados disponibles.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" wire:model="responsable_id" />
                    <x-input-error for="responsable_id" />
                </div>
            </div>
        </x-slot>

        <!-- Footer del Modal -->
        <x-slot name="footer">
            <div class="flex justify-end space-x-3 mt-4">
                <button wire:click="close"
                    class="flex items-center bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancelar
                </button>
                <button wire:click="asignaciones"
                    class="flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Guardar
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

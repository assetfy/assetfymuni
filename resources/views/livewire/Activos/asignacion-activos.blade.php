<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <!-- Título del Modal -->
        <x-slot name="title">
            <h2 class="text-xl font-semibold text-black">
                Registrar Asignado / Responsable
            </h2>
        </x-slot>

        <!-- Contenido del Modal -->
        <x-slot name="content">
            <!-- Sección superior: foto + nombre del activo a la izquierda, encargado/asignado a la derecha -->
            <div class="flex justify-between items-start mb-6">

                <!-- IZQUIERDA: Foto + Nombre del Activo -->
                <div class="flex items-center space-x-4">
                    <!-- Foto -->
                    <div>
                        @if ($fotoActivoUrl)
                            <div class="relative w-20 h-20">
                                <img src="{{ $fotoActivoUrl }}" alt="{{ $nombreActivo }}"
                                    class="w-full h-full object-cover rounded-full shadow-md">

                                <button type="button" wire:click="eliminarImagenBD('{{ $fotoActivo }}')"
                                    class="absolute top-1 right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    ✖
                                </button>
                            </div>
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-gray-600">Sin Imagen</span>
                            </div>
                        @endif
                    </div>

                    <!-- Nombre del Activo al lado de la foto -->
                    <div>
                        <h3 class="text-lg font-medium text-black">
                            {{ $nombreActivo }}
                        </h3>
                    </div>
                </div>

                <!-- DERECHA: Encargado (arriba) y Asignado (abajo) -->
                <div class="flex flex-col space-y-1 text-right">
                    <!-- <div>
                        <span class="text-black font-semibold">Encargado:</span>
                        <span class="text-blue-500">{{ $nombreEncargado }}</span>
                    </div> -->
                    @if ($empresaPrestadora != 2)
                        <div>
                            <span class="text-black font-semibold">Asignado:</span>
                            <span class="text-blue-500">{{ $nombreAsignado }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-black font-semibold">Responsable:</span>
                        <span class="text-blue-500">{{ $nombreResponsable }}</span>
                    </div>
                </div>
            </div>

            <!-- Selector de Gestor o Asignado -->
            <!-- Asignado a -->
            @if ($empresaPrestadora != 2)
                <div class="form-group mb-4" x-data="{ openDropdownAsignado: false }" @click.away="openDropdownAsignado = false">
                    <x-label value="Seleccione una persona asignada" />

                    <div class="relative flex items-center space-x-2">
                        <!-- Contenedor para botón y dropdown -->
                        <div class="relative w-full">
                            <!-- Botón de selección -->
                            <button type="button" @click="openDropdownAsignado = !openDropdownAsignado"
                                class="mt-1 block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none flex justify-between items-center">
                                <span>
                                    {{ $asignado_a === null ? 'Sin Asignado' : ($asignado_a ?: 'Seleccione persona asignada') }}
                                </span> <svg class="fill-current h-4 w-4 text-gray-700"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M5.516 7.548l4.484 4.484 4.484-4.484L16 8.032l-6 6-6-6z" />
                                </svg>
                            </button>

                            <!-- Dropdown con búsqueda -->
                            <div x-show="openDropdownAsignado" x-transition
                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                <div class="p-2">
                                    <input type="text" wire:model.live="searchAsignado"
                                        @keydown.enter="openDropdownAsignado = false"
                                        @keydown.escape="openDropdownAsignado = false"
                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                        placeholder="Buscar asignado..." />
                                </div>
                                <ul class="max-h-60 overflow-auto">
                                    @if (!empty($empleadosLista))
                                        <li wire:click="setAsignadoA(null)" @click="openDropdownAsignado = false"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-red-100 text-red-600 font-semibold">
                                            Sin Asignado
                                        </li>
                                        @foreach ($empleadosLista as $empleado)
                                            <li wire:click="setAsignadoA({{ $empleado->id }})"
                                                @click="openDropdownAsignado = false"
                                                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                                <span class="font-normal block truncate">{{ $empleado->name }}</span>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                            No hay empleados disponibles.
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" wire:model="asignado_a_id" />
                    <x-input-error for="asignado_a_id" />
                </div>
            @endif

            <!-- Responsable -->
            <div class="form-group mb-4" x-data="{ openDropdownResponsable: false }" @click.away="openDropdownResponsable = false">
                <x-label value="Seleccione un responsable" />

                <div class="relative flex items-center space-x-2">
                    <!-- Contenedor para botón y dropdown -->
                    <div class="relative w-full">
                        <!-- Botón de selección -->
                        <button type="button" @click="openDropdownResponsable = !openDropdownResponsable"
                            class="mt-1 block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none flex justify-between items-center">
                            <span>{{ $responsable === null ? 'Sin Responsable' : ($responsable ?: 'Seleccione responsable') }}</span>
                            <svg class="fill-current h-4 w-4 text-gray-700" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20">
                                <path d="M5.516 7.548l4.484 4.484 4.484-4.484L16 8.032l-6 6-6-6z" />
                            </svg>
                        </button>

                        <!-- Dropdown con búsqueda -->
                        <div x-show="openDropdownResponsable" x-transition
                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                            <div class="p-2">
                                <input type="text" wire:model.live="searchResponsable"
                                    @keydown.enter="openDropdownResponsable = false"
                                    @keydown.escape="openDropdownResponsable = false"
                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                    placeholder="Buscar responsable..." />
                            </div>
                            <ul class="max-h-60 overflow-auto">
                                @if (!empty($empleadosLista))
                                    <li wire:click="setResponsable(null)" @click="openDropdownResponsable = false"
                                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-red-100 text-red-600 font-semibold">
                                        Sin Responsable
                                    </li>
                                    @foreach ($empleadosLista as $empleado)
                                        <li wire:click="setResponsable({{ $empleado->id }})"
                                            @click="openDropdownResponsable = false"
                                            class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100">
                                            <span class="font-normal block truncate">{{ $empleado->name }}</span>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="cursor-default select-none relative py-2 px-3 text-gray-700">
                                        No hay empleados disponibles.
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <input type="hidden" wire:model="responsable_id" />
                <x-input-error for="responsable_id" />
            </div>

            <!-- Resumen de Datos del Activo -->
            <div class="mt-4 p-4 border border-black rounded space-y-2">
                <h4 class="text-lg font-semibold text-black">Resumen de Datos del Activo</h4>
                @if ($activo)
                    <div>
                        <strong class="text-black">Propietario:</strong>
                        {{ $activo->propietario }}
                    </div>
                    <div>
                        <strong class="text-black">Estado de Alta:</strong>
                        @foreach ($altas as $alta)
                            @if ($alta->id_estado_sit_alta == $activo->id_estado_sit_alta)
                                {{ $alta->nombre }}
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <strong class="text-black">Comentarios situación alta:</strong>
                        {{ $activo->comentarios_sit_alta }}
                    </div>
                    <div>
                        <strong class="text-black">Estado de Inventario:</strong>
                        {{ $activo->estado_inventario }}
                    </div>
                    @if ($activo->motivo_baja)
                        <div>
                            <strong class="text-black">Motivo de Baja:</strong>
                            {{ $activo->motivo_baja }}
                        </div>
                    @endif
                    <div>
                        <strong class="text-black">Estado Situación General:</strong>
                        @foreach ($generales as $general)
                            @if ($general->id_estado_sit_general == $activo->id_estado_sit_general)
                                {{ $general->nombre }}
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <strong class="text-black">Empresa Titular:</strong>
                        @foreach ($empresas as $empresa)
                            @if ($empresa->cuit == $activo->empresa_titular)
                                {{ $empresa->razon_social }}
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <strong class="text-black">Ubicación:</strong>
                        @foreach ($ubicaciones as $ubicacion)
                            @if ($ubicacion->id_ubicacion == $activo->id_ubicacion)
                                {{ $ubicacion->nombre }}
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </x-slot>

        <!-- Footer del Modal -->
        <x-slot name="footer">
            <button wire:click="close" class="mr-2 bg-black hover:bg-black text-white font-bold py-2 px-4 rounded">
                Cancelar
            </button>
            <button wire:click="asignaciones"
                class="bg-blue-500 hover:bg-blue text-white font-bold py-2 px-4 rounded">
                Guardar
            </button>
        </x-slot>
    </x-dialog-modal>
</div>

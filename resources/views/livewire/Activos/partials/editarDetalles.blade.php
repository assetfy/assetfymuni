<!-- Información General y Ubicación -->
<div class="grid grid-cols-1 gap-4 mb-6 mt-4">

    <!-- Ubicación -->
    <div>
        <x-label>
            <span class="flex items-center">
                <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i> Ubicación
            </span>
        </x-label>

        @if ($editMode)
            <select wire:model.live="id_ubicacion"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 mb-6">
                <option value="0">{{ $selectedUbicacionNombre }}</option>
                <option value="-1" class="{{ $id_ubicacion != -1 ? '' : 'hidden' }}">Sin Ubicación</option>
                @foreach ($ubicacionesDisponibles as $ubicacion)
                    <option value="{{ $ubicacion->id_ubicacion }}" @if ($ubicacion->id_ubicacion == $id_ubicacion) selected @endif>
                        {{ $ubicacion->nombre }}
                    </option>
                @endforeach
            </select>
            <x-input-error for="id_ubicacion" />

            @if ($pisosDisponible)
                <div class="mb-4 mt-2">
                    <div class="flex items-center space-x-2 mb-1">
                        <i class="fas fa-building text-gray-500"></i>
                        <x-label for="selectedPiso" value="Planta / Piso donde se encuentra el bien" />
                    </div>

                    <select id="selectedPiso" wire:model="selectedPiso"
                        class="block w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out appearance-none">
                        <option value="" hidden>Seleccione la planta o piso</option>
                        @foreach ($pisosDisponible as $piso)
                            <option value="{{ $piso }}">{{ $piso }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        @else
            @if ($id_ubicacion == -1)
                <p class="text-gray-700">Sin Ubicación</p>
            @else
                <p class="text-gray-700">
                    {{ $selectedUbicacionNombre }}
                    @if ($pisoActualNombre)
                        &nbsp;&mdash;&nbsp; {{ $pisoActualNombre }}
                    @endif
                </p>
            @endif
        @endif
    </div>

    <!-- Mapa -->
    <div class="{{ $id_ubicacion != -1 ? '' : 'hidden' }}">
        <p class="font-semibold text-sm text-gray-500">
            <i class="fas fa-map text-indigo-500 mr-2"></i>Mapa de Ubicación:
        </p>
        <div id="mapActivo" class="mt-2 border border-gray-300 rounded w-full h-52" wire:ignore></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Responsable Técnico (Gestor) -->
        <div class="flex flex-col">
            <div class="flex items-center text-sm text-gray-600 font-semibold mb-1">
                <i class="fas fa-user-cog text-purple-500 mr-2"></i> Responsable Técnico (Gestor)
            </div>
            <div class="text-gray-800">
                {{ $gestor }}
            </div>
        </div>

        <!-- Fecha de Asignación -->
        <div class="flex flex-col">
            <div class="flex items-center text-sm text-gray-600 font-semibold mb-1">
                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i> Fecha de Asignación
            </div>
            <div class="text-gray-800">
                {{ $fecha_asignacion ?? '-' }}
            </div>
        </div>

        <!-- Usuario del Bien -->
        <div class="flex flex-col md:col-span-2">
            <div class="flex items-center text-sm text-gray-600 font-semibold mb-1">
                <i class="fas fa-user text-teal-500 mr-2"></i> Usuario del Bien
            </div>
            <div class="text-gray-800">
                {{ $asignado }}
            </div>
        </div>
    </div>


    <!-- Estado General debajo y Condición debajo también -->
    <div class="mt-6 space-y-6">
        <!-- Estado General -->
        <div>
            <x-label>
                <span class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i> Estado General
                </span>
            </x-label>

            @if ($editMode)
                <select wire:model.live="id_estado_sit_general"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Seleccione un estado</option>
                    @foreach ($general as $estado)
                        <option value="{{ $estado->id_estado_sit_general }}">{{ $estado->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error for="id_estado_sit_general" />
            @else
                <p class="text-gray-800">
                    @foreach ($general as $estado)
                        @if ($estado->id_estado_sit_general == $id_estado_sit_general)
                            {{ $estado->nombre }}
                        @endif
                    @endforeach
                </p>
            @endif
        </div>

        <!-- Condición -->
        @if (!$inmueble)
        <div>
            <x-label>
                <span class="flex items-center">
                    <i class="fas fa-clipboard-check text-blue-500 mr-2"></i> Condición
                </span>
            </x-label>

            @if ($editMode)
                <select wire:model.live="id_condicion"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Seleccione la condición del bien</option>
                    @foreach ($condiciones as $condicion)
                        <option value="{{ $condicion->id_condicion }}">{{ $condicion->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error for="id_condicion" />
            @else
                <p class="text-gray-800">
                    {{ $condicionBien }}
                </p>
            @endif
        </div>
        @endif
    </div>

    <!-- Motivo de Baja (condicional) -->
    @if ($id_estado_sit_general == 2)
        <div class="mt-4">
            <x-label value="Motivo de Baja" />
            <x-input type="text" wire:model.defer="upmotivo_baja"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" />
            <x-input-error for="upmotivo_baja" />
        </div>
    @endif

    <div class="flex flex-col">
        <div class="flex items-center text-sm text-gray-600 font-semibold mb-1">
            <i class="fas fa-calendar-alt text-blue-500 mr-2"></i> Fecha de Ultimo Mantenimiento
        </div>
        <div class="text-gray-800">
            {{ $ultimaOrdenFecha ?? '-' }}
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="mt-4 border-t pt-4">
        <div class="flex items-center mb-2 text-sm font-semibold text-gray-700">
            <i class="fas fa-bolt text-red-500 mr-2"></i> Acciones Rápidas <span class="ml-1 text-gray-500"></span>
        </div>

        <ul class="list-none pl-0 text-sm space-y-1">
            @if (!empty($gestorEmail))
                <li>
                    <button type="button" class="text-blue-700 hover:underline" wire:click="correo('gestor')">
                        Enviar mensaje al Gestor Técnico
                    </button>
                </li>
            @endif

            @if (!empty($asignadoEmail))
                <li>
                    <button type="button" class="text-blue-700 hover:underline" wire:click="correo('usuario')">
                        Enviar mensaje al Usuario
                    </button>
                </li>
            @endif

            @unless (!empty($gestorEmail) || !empty($asignadoEmail))
                <li class="text-gray-500 italic">Sin destinatarios con email disponible</li>
            @endunless
        </ul>
    </div>


</div>

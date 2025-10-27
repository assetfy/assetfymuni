<div class="min-h-screen flex flex-col bg-white">
    <div class="w-full p-4 md:p-8">
        <!-- Encabezado con botón "Volver" y contenido centrado -->
        <div class="mb-4 md:mb-8 flex items-center">
            @if ($step == 1)
                <!-- Botón de Volver -->
                <a href="{{ route('register') }}"
                    class="bg-blue-100 text-blue-600 px-4 py-2 rounded hover:bg-blue-200 transition-all duration-300 text-sm font-medium">
                    Volver
                    @php
                        session()->forget('user_data');
                    @endphp
                </a>
            @endif
            <!-- Contenedor para título y subtítulo, centrado -->
            <div class="flex-1 text-center">
                <h2 class="text-2xl md:text-4xl font-bold text-gray-800">
                    @if ($step == 1)
                        Defina el área de cobertura
                    @elseif ($step == 2)
                        @if ($opcion == 'Si')
                            Detalles del Inmueble
                        @else
                            Seleccione la actividad económica
                        @endif
                    @elseif ($step == 3)
                        @if ($opcion == 'Si' && $tipoSeleccionado == 2)
                            Seleccione la actividad económica
                        @else
                            Buscar Empresa
                        @endif
                    @elseif ($step == 4)
                        @if ($opcion == 'Si' && $tipoSeleccionado == 1)
                            Buscar Empresa
                        @else
                            Resumen de la empresa
                        @endif
                    @elseif ($step == 5 && $opcion == 'Si' && $tipoSeleccionado == 2)
                        Resumen de la empresa
                    @endif
                </h2>
                <p class="text-gray-600 mt-2">
                    @if ($step == 1)
                        Seleccione la ubicación en el mapa
                    @elseif ($step == 2)
                        @if ($opcion == 'Si')
                            Ingrese los detalles del inmueble
                        @else
                            Seleccione la actividad económica
                        @endif
                    @elseif ($step == 3)
                        @if ($opcion == 'Si' && $tipoSeleccionado == 2)
                            Seleccione la actividad económica
                        @else
                            Buscar Empresa
                        @endif
                    @elseif ($step == 4)
                        @if ($opcion == 'Si' && $tipoSeleccionado == 1)
                            Buscar Empresa
                        @else
                            Resumen de la empresa
                        @endif
                    @elseif ($step == 5 && $opcion == 'Si' && $tipoSeleccionado == 2)
                        Resumen de la empresa
                    @endif
                </p>
            </div>
        </div>

        <!-- Barra de Progreso -->
        <div class="relative pt-1 mb-4 md:mb-8">
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span
                        class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                        Paso <span id="step-indicator">{{ $step }}</span> de
                        {{ $tipoSeleccionado == 1 && $opcion == 'Si' ? 5 : ($opcion == 'Si' ? 5 : 4) }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold inline-block text-blue-600">
                        <span id="step-percentage">
                            {{ round(($step / ($tipoSeleccionado == 1 && $opcion == 'Si' ? 5 : ($opcion == 'Si' ? 5 : 4))) * 100) }}
                        </span>%
                    </span>
                </div>
            </div>
            <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-200">
                <!-- Barra de progreso con efecto de transición suave y animación de brillo continuo -->
                <div id="progress-bar"
                    style="width: {{ ($step / ($tipoSeleccionado == 1 && $opcion == 'Si' ? 5 : ($opcion == 'Si' ? 5 : 4))) * 100 }}%;"
                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 
                           transition-width ease-in-out duration-1000 animated-bar">
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <form wire:submit.prevent="save" class="space-y-6 md:space-y-10">
            <!-- Paso 1: Mapa y ubicación (SIEMPRE en el DOM, solo oculto/visible con clases) -->
            <div class="@if ($step == 1) block @else hidden @endif">
                <!-- Aquí sí mantenemos el contenido de Step 1 -->
                <div class="flex flex-col md:flex-row md:space-x-8 space-y-6 md:space-y-0">
                    <div class="w-full md:w-1/2 h-64 md:h-96" wire:ignore>
                        <!-- Mantenemos wire:ignore para que Livewire no lo re-renderice -->
                        <div id="map-container" class="w-full h-full bg-gray-100 border-2 border-dashed">
                        </div>
                        <input type="hidden" wire:model.defer="lat">
                        <input type="hidden" wire:model.defer="long">
                    </div>
                    <div class="w-full md:w-1/2 space-y-6">
                        <!-- Datos de la ubicación -->
                        <div>
                            <label for="provincia" class="block text-base font-medium text-gray-700">
                                Provincia
                            </label>
                            <input type="text" id="provincia" wire:model.lazy="provincia" disabled
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                        </div>
                        <div>
                            <label for="localidad" class="block text-base font-medium text-gray-700">
                                Localidad
                            </label>
                            <input type="text" id="localidad" wire:model.lazy="localidad"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                            @error('localidad')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="domicilio" class="block text-base font-medium text-gray-700">
                                Domicilio
                            </label>
                            <input type="text" id="domicilio" wire:model.lazy="domicilio"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                            @error('domicilio')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0">
                            <div class="w-full md:w-1/2">
                                <label for="piso" class="block text-base font-medium text-gray-700">
                                    Piso
                                </label>
                                <input type="text" id="piso" wire:model.lazy="piso"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                           focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                @error('piso')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="w-full md:w-1/2">
                                <label for="codigo_postal" class="block text-base font-medium text-gray-700">
                                    Código Postal
                                </label>
                                <input type="text" id="codigo_postal" wire:model.lazy="codigo_postal"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                           focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                @error('codigo_postal')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Botón de selección de ubicación -->
                        <div>
                            <label for="opcion" class="block text-base font-medium text-gray-700">
                                ¿Desea agregar esta ubicación como un inmueble?
                            </label>
                            <select id="opcion" wire:model="opcion"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                <option value="No">No</option>
                                <option value="Si">Sí</option>
                            </select>
                            @error('opcion')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <!-- Fin del Paso 1 -->

            <!-- Paso 2: Detalles del Inmueble o Selección de Actividad Económica -->
            @if ($step == 2)
                @if ($opcion == 'Si')
                    <!-- Paso 2: Detalles del Inmueble -->
                    <div class="space-y-6">
                        <div>
                            <label for="nombre" class="block text-base font-medium text-gray-700">
                                Nombre del Inmueble
                            </label>
                            <input type="text" id="nombre" wire:model.lazy="nombre"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                            @error('nombre')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="propiedad" class="block text-base font-medium text-gray-700">
                                Propiedad
                            </label>
                            <select id="propiedad" wire:model="propiedad"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                <option value="" hidden>Propiedad</option>
                                <option value="Propio">Propio</option>
                                <option value="Cliente">Cliente</option>
                            </select>
                            @error('propiedad')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="tipoUbicacion" class="block text-base font-medium text-gray-700">
                                Tipo de Ubicación
                            </label>
                            <select id="tipoUbicacion" wire:model.lazy="tipoUbicacion"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                <option value="" hidden>Seleccione el Tipo de Ubicación</option>
                                @foreach ($tipoUbicaciones as $tipoUbic)
                                    <option value="{{ $tipoUbic->id_tipo }}">
                                        {{ $tipoUbic->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipoUbicacion')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    <!-- Paso 2: Selección de Actividad Económica para 'proveedora' -->
                    <div class="animate__animated animate__fadeIn">
                        @if ($tipoSeleccionado == 2)
                            <!-- Actividades con servicios para tipo 2 -->
                            <div class="space-y-6 animate__animated animate__fadeIn">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                                    @foreach ($actividadesConServicios as $act)
                                        <div class="custom-card @if ($selectedActividad == $act->COD_ACTIVIDAD) selected @endif"
                                            wire:click="$set('selectedActividad', {{ $act->COD_ACTIVIDAD }})">
                                            <div class="custom-box">
                                                <img src="{{ asset('storage/' . $act->logo) }}" alt="Logo"
                                                    class="custom-logo">
                                                <div class="custom-content">
                                                    <h3 class="text-sm font-semibold">
                                                        {{ $act->nombre }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <!-- Descripción oculta (efecto persiana) -->
                                            <div class="custom-description">
                                                <p class="text-sm">{{ $act->descripcion }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Mostrar mensaje de error si no se selecciona una actividad -->
                                @error('selectedActividad')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <!-- Links de paginación -->
                                <div class="mt-6">
                                    {{ $actividadesConServicios->links() }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Paso 3: Selección de Actividad Económica o Buscar Empresa -->
            @if ($step == 3)
                @if ($opcion == 'Si' && $tipoSeleccionado == 2)
                    <!-- Paso 3: Selección de Actividad Económica para 'proveedora' -->
                    <div class="space-y-6 animate__animated animate__fadeIn">
                        <div class="space-y-6 animate__animated animate__fadeIn">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                                @foreach ($actividadesConServicios as $act)
                                    <div class="custom-card @if ($selectedActividad == $act->COD_ACTIVIDAD) selected @endif"
                                        wire:click="$set('selectedActividad', {{ $act->COD_ACTIVIDAD }})">
                                        <div class="custom-box">
                                            <img src="{{ asset('storage/' . $act->logo) }}" alt="Logo"
                                                class="custom-logo">
                                            <div class="custom-content">
                                                <h3 class="text-sm font-semibold">
                                                    {{ $act->nombre }}
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="custom-description">
                                            <p class="text-sm">{{ $act->descripcion }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('selectedActividad')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            <div class="mt-6">
                                {{ $actividadesConServicios->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Paso 3: Buscar Empresa para tipo 1 con opcion 'No' -->
                    <div class="space-y-6 animate__animated animate__fadeIn">
                        <!-- Buscar empresa -->
                        <div>
                            <label for="cuit" class="block text-base font-medium text-gray-700">
                                Buscar Empresa
                            </label>
                            <div class="flex mt-2">
                                <input type="text" id="cuit" wire:model.lazy="cuit"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                                    maxlength="11"
                                    class="block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                               focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                <button type="button"
                                    class="ml-4 bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition-colors duration-300 text-sm"
                                    wire:click="verificarCuitAfip">
                                    Buscar
                                </button>
                            </div>

                            @if ($mensaje)
                                <div class="mt-3 text-sm text-blue-600">{{ $mensaje }}</div>
                            @else
                                <div class="mt-3 text-sm text-gray-500">
                                    Si la búsqueda falla, puede continuar con el registro de la empresa.
                                </div>
                            @endif
                            @error('cuit')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Campo Razón Social (siempre visible) -->
                        <div>
                            <label for="razon_social" class="block text-base font-medium text-gray-700">
                                Razón Social
                            </label>
                            <input type="text" id="razon_social" wire:model.lazy="razon_social"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                            @error('razon_social')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label for="descripcion" class="block text-base font-medium text-gray-700">
                                Descripción de la Actividad
                            </label>
                            <textarea id="descripcion" wire:model.lazy="descripcion"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2"></textarea>
                            @error('descripcion')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="url" class="block text-base font-medium text-gray-700">
                                Sitio Web (Opcional)
                            </label>
                            <input type="url" id="url" wire:model.lazy="url"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2"
                                placeholder="https://www.ejemplo.com">
                            @error('url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Cards para Constancia AFIP y Logo -->
                        <div class="flex flex-col md:flex-row justify-between space-y-6 md:space-y-0 md:space-x-6">
                            <!-- Card para la Constancia AFIP -->
                            <div class="w-full md:w-1/2 p-4 rounded-lg shadow-md bg-white">
                                <label class="block text-base font-medium text-gray-700 mb-4">
                                    Constancia AFIP <span class="text-gray-500">(Opcional)</span>
                                </label>
                                @if ($constancia_afip_path)
                                    <div class="flex justify-between items-center">
                                        <div class="w-full">
                                            @if (in_array(pathinfo(Storage::url($constancia_afip_path), PATHINFO_EXTENSION), ['jpeg', 'png', 'svg']))
                                                <img src="{{ Storage::url($constancia_afip_path) }}"
                                                    alt="Vista previa de Constancia AFIP"
                                                    class="w-full h-48 object-contain">
                                            @elseif (in_array(pathinfo(Storage::url($constancia_afip_path), PATHINFO_EXTENSION), ['pdf']))
                                                <iframe src="{{ Storage::url($constancia_afip_path) }}"
                                                    class="w-full h-48 object-contain"></iframe>
                                            @else
                                                <a href="{{ Storage::url($constancia_afip_path) }}" target="_blank"
                                                    class="text-blue-600 underline text-sm">
                                                    Ver archivo
                                                </a>
                                            @endif
                                        </div>
                                        <div class="mt-4 md:mt-0">
                                            <button type="button" wire:click="removeConstanciaAfip"
                                                class="text-red-500 text-sm">
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-full h-40 border border-dashed border-gray-300 rounded-lg cursor-pointer">
                                        <input type="file" wire:model="constancia_afip" class="hidden"
                                            id="constancia_afip_input">
                                        <label for="constancia_afip_input"
                                            class="cursor-pointer flex items-center justify-center w-full h-full">
                                            <span class="text-4xl text-gray-400">+</span>
                                        </label>
                                    </div>
                                    @error('constancia_afip')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <!-- Card para el Logo -->
                            <div class="w-full md:w-1/2 p-4 rounded-lg shadow-md bg-white">
                                <label class="block text-base font-medium text-gray-700 mb-4">
                                    Logo <span class="text-gray-500">(Opcional)</span>
                                </label>
                                @if ($logo_path)
                                    <div class="flex justify-between items-center">
                                        <div class="w-full">
                                            <img src="{{ Storage::url($logo_path) }}" alt="Vista previa del logo"
                                                class="w-full h-48 object-contain">
                                        </div>
                                        <div class="mt-4 md:mt-0">
                                            <button type="button" wire:click="removeLogo"
                                                class="text-red-500 text-sm">
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-full h-40 border border-dashed border-gray-300 rounded-lg cursor-pointer">
                                        <input type="file" wire:model="logo" class="hidden" id="logo_input">
                                        <label for="logo_input"
                                            class="cursor-pointer flex items-center justify-center w-full h-full">
                                            <span class="text-4xl text-gray-400">+</span>
                                        </label>
                                    </div>
                                    @error('logo')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Paso 4: Selección de Actividad Económica o Buscar Empresa / Resumen -->
            @if ($step == 4)
                @if ($opcion == 'Si' && in_array($tipoSeleccionado, [1, 2]))
                    <!-- Paso 4: Buscar Empresa para tipo 1 con opcion 'Si' -->
                    <div class="space-y-6 animate__animated animate__fadeIn">
                        <!-- Buscar empresa -->
                        <div>
                            <label for="cuit" class="block text-base font-medium text-gray-700">
                                Buscar Empresa
                            </label>
                            <div class="flex mt-2">
                                <input type="text" id="cuit" wire:model.lazy="cuit"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    class="block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                           focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                                <button type="button"
                                    class="ml-4 bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition-colors duration-300 text-sm"
                                    wire:click="verificarCuitAfip">
                                    Buscar
                                </button>
                            </div>
                            @if ($mensaje)
                                <div class="mt-3 text-sm text-blue-600">{{ $mensaje }}</div>
                            @else
                                <div class="mt-3 text-sm text-gray-500">
                                    Si la búsqueda no encuentra resultados, puede continuar el registro manualmente.
                                </div>
                            @endif
                            @error('cuit')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Campo Razón Social (siempre visible) -->
                        <div>
                            <label for="razon_social" class="block text-base font-medium text-gray-700">
                                Razón Social
                            </label>
                            <input type="text" id="razon_social" wire:model.lazy="razon_social"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2">
                            @error('razon_social')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Campo Descripción -->
                        <div>
                            <label for="descripcion" class="block text-base font-medium text-gray-700">
                                Descripción de la Actividad
                            </label>
                            <textarea id="descripcion" wire:model.lazy="descripcion"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2"></textarea>
                            @error('descripcion')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="url" class="block text-base font-medium text-gray-700">
                                Sitio Web (Opcional)
                            </label>
                            <input type="url" id="url" wire:model.lazy="url"
                                class="mt-1 block w-full text-sm rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 
                                       focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-1 px-2"
                                placeholder="https://www.ejemplo.com">
                            @error('url')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Cards para Constancia AFIP y Logo -->
                        <div class="flex flex-col md:flex-row justify-between space-y-6 md:space-y-0 md:space-x-6">
                            <!-- Card para la Constancia AFIP -->
                            <div class="w-full md:w-1/2 p-4 rounded-lg shadow-md bg-white">
                                <label class="block text-base font-medium text-gray-700 mb-4">
                                    Constancia AFIP <span class="text-gray-500">(Opcional)</span>
                                </label>
                                @if ($constancia_afip_path)
                                    <div class="flex justify-between items-center">
                                        <div class="w-full">
                                            @if (in_array(pathinfo(Storage::url($constancia_afip_path), PATHINFO_EXTENSION), ['jpeg', 'png', 'svg']))
                                                <img src="{{ Storage::url($constancia_afip_path) }}"
                                                    alt="Vista previa de Constancia AFIP"
                                                    class="w-full h-48 object-contain">
                                            @elseif (in_array(pathinfo(Storage::url($constancia_afip_path), PATHINFO_EXTENSION), ['pdf']))
                                                <iframe src="{{ Storage::url($constancia_afip_path) }}"
                                                    class="w-full h-48 object-contain"></iframe>
                                            @else
                                                <a href="{{ Storage::url($constancia_afip_path) }}" target="_blank"
                                                    class="text-blue-600 underline text-sm">
                                                    Ver archivo
                                                </a>
                                            @endif
                                        </div>
                                        <div class="mt-4 md:mt-0">
                                            <button type="button" wire:click="removeConstanciaAfip"
                                                class="text-red-500 text-sm">
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-full h-40 border border-dashed border-gray-300 rounded-lg cursor-pointer">
                                        <input type="file" wire:model="constancia_afip" class="hidden"
                                            id="constancia_afip_input">
                                        <label for="constancia_afip_input"
                                            class="cursor-pointer flex items-center justify-center w-full h-full">
                                            <span class="text-4xl text-gray-400">+</span>
                                        </label>
                                    </div>
                                    @error('constancia_afip')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>

                            <!-- Card para el Logo -->
                            <div class="w-full md:w-1/2 p-4 rounded-lg shadow-md bg-white">
                                <label class="block text-base font-medium text-gray-700 mb-4">
                                    Logo <span class="text-gray-500">(Opcional)</span>
                                </label>
                                @if ($logo_path)
                                    <div class="flex justify-between items-center">
                                        <div class="w-full">
                                            <img src="{{ Storage::url($logo_path) }}" alt="Vista previa del logo"
                                                class="w-full h-48 object-contain">
                                        </div>
                                        <div class="mt-4 md:mt-0">
                                            <button type="button" wire:click="removeLogo"
                                                class="text-red-500 text-sm">
                                                Cambiar
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div
                                        class="flex justify-center items-center w-full h-40 border border-dashed border-gray-300 rounded-lg cursor-pointer">
                                        <input type="file" wire:model="logo" class="hidden" id="logo_input">
                                        <label for="logo_input"
                                            class="cursor-pointer flex items-center justify-center w-full h-full">
                                            <span class="text-4xl text-gray-400">+</span>
                                        </label>
                                    </div>
                                    @error('logo')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Paso 5: Resumen de la empresa para 'proveedora' -->
            @if ($step == 5 && $opcion == 'Si' && in_array($tipoSeleccionado, [1, 2]))
                <div class="text-center space-y-6 animate__animated animate__fadeIn">
                    <h2 class="text-2xl font-semibold">Datos de la Empresa</h2>
                    <div class="p-6 bg-gray-100 rounded-lg shadow-md">
                        <ul class="list-disc pl-6 text-left text-sm">
                            <li><strong>Latitud:</strong> {{ $lat }}</li>
                            <li><strong>Longitud:</strong> {{ $long }}</li>
                            <li><strong>Provincia:</strong> {{ $provincia }}</li>
                            <li><strong>Localidad:</strong> {{ $localidad }}</li>
                            <li><strong>Domicilio:</strong> {{ $domicilio }}</li>
                            <li><strong>Piso:</strong> {{ $piso }}</li>
                            <li><strong>Código Postal:</strong> {{ $codigo_postal }}</li>
                            <li><strong>Tipo de Empresa:</strong>
                                {{ $tipoSeleccionado == 1 ? 'Propietario de un activo' : 'Proveedor de servicios' }}
                            </li>
                            <li><strong>CUIT:</strong> {{ $cuit }}</li>
                            <li><strong>Razón Social:</strong> {{ $razon_social }}</li>
                            <li><strong>Descripción de la Actividad:</strong> {{ $descripcion }}</li>
                            <li><strong>Actividad Económica:</strong>
                                {{ $selectedActividad ? App\Models\ActividadesEconomicasModel::find($selectedActividad)->nombre : 'Ninguna' }}
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Paso 4: Resumen de la empresa para tipo 1 con opcion 'No' -->
            @if ($step == 4 && $opcion == 'No')
                <div class="text-center space-y-6 animate__animated animate__fadeIn">
                    <h2 class="text-2xl font-semibold">Datos de la Empresa</h2>
                    <div class="p-6 bg-gray-100 rounded-lg shadow-md">
                        <ul class="list-disc pl-6 text-left text-sm">
                            <li><strong>Latitud:</strong> {{ $lat }}</li>
                            <li><strong>Longitud:</strong> {{ $long }}</li>
                            <li><strong>Provincia:</strong> {{ $provincia }}</li>
                            <li><strong>Localidad:</strong> {{ $localidad }}</li>
                            <li><strong>Domicilio:</strong> {{ $domicilio }}</li>
                            <li><strong>Piso:</strong> {{ $piso }}</li>
                            <li><strong>Código Postal:</strong> {{ $codigo_postal }}</li>
                            <li><strong>Tipo de Empresa:</strong>
                                {{ $tipoSeleccionado == 1 ? 'Propietario de un activo' : 'Proveedor de servicios' }}
                            </li>
                            <li><strong>CUIT:</strong> {{ $cuit }}</li>
                            <li><strong>Razón Social:</strong> {{ $razon_social }}</li>
                            <li><strong>Descripción de la Actividad:</strong> {{ $descripcion }}</li>
                            <li><strong>Actividad Económica:</strong>
                                {{ $selectedActividad ? App\Models\ActividadesEconomicasModel::find($selectedActividad)->nombre : 'Ninguna' }}
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Botones de navegación -->
            <div class="flex flex-col md:flex-row justify-between mt-6 md:mt-12 space-y-6 md:space-y-0">
                @if ($step > 1)
                    <button type="button" wire:click="previousStep" wire:loading.attr="disabled"
                        class="w-full md:w-auto bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-all duration-300 text-sm">
                        Anterior
                    </button>
                @endif

                @if ($step < ($tipoSeleccionado == 1 && $opcion == 'Si' ? 5 : ($opcion == 'Si' ? 5 : 4)))
                    <button type="button" wire:click="nextStep" wire:loading.attr="disabled"
                        class="w-full md:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-all duration-300 text-sm"
                        @if (!$isStepValid) disabled @endif>
                        Siguiente
                    </button>
                @elseif ($step == ($tipoSeleccionado == 1 && $opcion == 'Si' ? 5 : ($opcion == 'Si' ? 5 : 4)))
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full md:w-auto bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-all duration-300 text-sm">
                        Registrar Empresa
                    </button>
                @endif
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 px-4 py-2 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    wire:loading wire:target="save">
                    Cargando...
                </span>
            </div>
        </form>
    </div>

    <style>
        /* Animación para el movimiento continuo en la barra */
        @keyframes progressMovement {
            0% {
                background-position: 0% 0%;
            }

            100% {
                background-position: 200% 0%;
            }
        }

        /* Efecto de movimiento para la barra cuando está completamente cargada */
        .animated-bar {
            background: linear-gradient(90deg, rgb(30, 112, 199) 0%, rgb(23, 50, 202) 50%, rgba(0, 122, 255, 1) 100%);
            background-size: 200% 100%;
            animation: progressMovement 2s linear infinite;
        }

        .custom-card {
            position: relative;
            min-width: 160px;
            height: 240px;
            background: #fff;
            border-radius: 12px;
            margin: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 16px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .custom-card.selected {
            background-color: #e0f2fe;
            border: 2px solid #007bff;
            transition: background-color 0.3s ease;
        }

        .custom-card:hover .custom-box {
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }

        .custom-box {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #fff;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            z-index: 2;
        }

        .custom-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .custom-content {
            text-align: center;
            padding: 8px;
            color: black;
        }

        .custom-description {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f4f8;
            color: #007bff;
            height: 100%;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            padding: 10px;
            box-sizing: border-box;
            transition: transform 0.3s ease-in-out;
            z-index: 1;
        }

        .custom-card:hover .custom-description {
            transform: translateY(-100%);
            transition: transform 0.3s ease-in-out;
        }
    </style>
</div>

<!-- Script personalizado para inicializar el mapa -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script de inicialización del mapa cargado.');

        let modalMap = null; // Mapa para el modal (si lo usas)
        let viewMap = null; // Mapa para la vista
        let modalMarker = null;
        let viewMarker = null;

        const corrientes = {
            lat: -27.4799,
            lng: -58.8361
        }; // Coordenadas iniciales de Corrientes

        // Evento para el mapa de la vista (Paso 1)
        Livewire.on('viewMapShown', function() {
            // Usamos un pequeño setTimeout para asegurarnos de que el DIV ya está visible
            setTimeout(() => {
                const viewMapElement = document.getElementById('map-container');
                if (!viewMapElement) {
                    console.error('Elemento con id "map-container" no encontrado.');
                    return;
                }
                if (viewMap) {
                    google.maps.event.trigger(viewMap, 'resize');
                } else {
                    viewMap = initializeMap(viewMapElement, 'view');
                }
            }, 0);
        });

        function initializeMap(mapElement, type) {
            const map = new google.maps.Map(mapElement, {
                center: corrientes,
                zoom: 13,
                mapTypeId: 'roadmap',
                disableDefaultUI: true,
                zoomControl: true,
            });

            addSearchBox(map, type);

            map.addListener('click', function(event) {
                updateMarker(map, event.latLng, type);
                updateLocation(event.latLng.toJSON(), type);
            });

            return map;
        }

        function addSearchBox(map, type) {
            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Buscar ubicación';
            input.className = 'search-box';

            // Estilos inline del campo de búsqueda
            input.style.width = '220px';
            input.style.padding = '5px';
            input.style.border = '1px solid #ccc';
            input.style.borderRadius = '4px';
            input.style.marginTop = '10px';

            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

            const searchBox = new google.maps.places.SearchBox(input);
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                if (places.length === 0) {
                    return;
                }
                const place = places[0];
                if (place.geometry && place.geometry.location) {
                    updateMarker(map, place.geometry.location, type);
                    updateLocation(place.geometry.location.toJSON(), type);
                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                } else {
                    Swal.fire({
                        title: 'Ubicación no encontrada',
                        text: 'Por favor, selecciona una ubicación válida.',
                        icon: 'warning',
                        confirmButtonText: 'Aceptar',
                    });
                }
            });
        }

        function updateMarker(map, location, type) {
            let marker;
            if (type === 'modal') {
                if (modalMarker) {
                    modalMarker.setMap(null);
                }
                marker = modalMarker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            } else if (type === 'view') {
                if (viewMarker) {
                    viewMarker.setMap(null);
                }
                marker = viewMarker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            }
        }

        function updateLocation(latlng, type) {
            const wireId = (type === 'modal') ?
                document.getElementById('mapModal').closest('[wire\\:id]').getAttribute('wire:id') :
                document.getElementById('map-container').closest('[wire\\:id]').getAttribute('wire:id');

            const component = Livewire.find(wireId);

            if (component) {
                component.set('lat', latlng.lat);
                component.set('long', latlng.lng);
                component.call('setAddress', latlng.lat, latlng.lng);
            } else {
                console.error('Componente Livewire no encontrado para actualizar la ubicación.');
            }
        }
    });
</script>

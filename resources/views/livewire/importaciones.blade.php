<div class="mx-10 my-6">
    <!-- Título -->
    <h3 class="text-xl font-semibold text-center text-gray-900 mb-6 flex items-center justify-center">
        <i class="fas fa-file-upload mr-3 text-blue-600"></i> Carga Masiva
    </h3>

    <!-- Contenedor de importación -->
    <div class="mb-6 p-6 bg-gray-100 w-full rounded-lg shadow-sm">
        <label class="block font-medium text-gray-700 mb-2 text-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 4.5L7.5 12l9 7.5M16.5 4.5l-9 7.5 9 7.5" />
            </svg>
            Importar Datos
        </label>

        <div class="flex flex-wrap items-center gap-4">
            <!-- Select: Tipo de Datos -->
            <div class="relative w-70">
                <select wire:model.live="tipoDatos"
                    class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                    <option value="1" selected disabled class="text-gray-500">Seleccione una opción</option>
                    <option value="UbicacionesPropias">🏠 Ubicaciones Propias</option>
                    <option value="UbicacionesUsuarios">👥 Ubicaciones de Clientes</option>
                    <option value="Activos">💼 Activos</option>
                    <option value="Bienes">👥 Bienes de Clientes</option>
                    <option value="Proveedores">🚚 Proveedores</option>
                    <option value="Clientes">🛒 Clientes</option>
                    <option value="Usuarios">Usuarios</option>
                </select>

                <!-- Ícono dentro del select -->
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15l3.75 3.75m0 0l3.75-3.75M12 18.75V4.5" />
                    </svg>
                </div>
            </div>

            <!-- Select: Tipo de Operación -->
            <div class="relative w-70">
                <select wire:model.live="tipoOperacion"
                    class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                    <option value="1" selected disabled class="text-gray-500">Seleccione una opción</option>
                    <option value="Actualizar">🔄 Actualizar</option>
                    <option value="Insertar">➕ Insertar</option>
                </select>

                <!-- Ícono dentro del select -->
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15l3.75 3.75m0 0l3.75-3.75M12 18.75V4.5" />
                    </svg>
                </div>
            </div>

            @if ($tipoDatos != '1' && $tipoOperacion != '1')
                <!-- Botón de descarga de ejemplo según el tipo de datos -->
                @if ($tipoDatos === 'Activos')
                    <button wire:click="descargarEjemploActivo"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Bienes
                    </button>
                @elseif ($tipoDatos === 'Bienes')
                    <button wire:click="descargarEjemploActivo"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Bienes
                    </button>
                @elseif ($tipoDatos === 'Proveedores')
                    <button wire:click="descargarEjemploPrestadores"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Proveedores
                    </button>
                @elseif ($tipoDatos === 'Clientes')
                    <button wire:click="descargarEjemploClientes"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Clientes
                    </button>
                @elseif ($tipoDatos === 'UbicacionesPropias')
                    <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
                    <button wire:click="descargarEjemploUbicacion"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Ubicaciones
                    </button>
                @elseif ($tipoDatos === 'UbicacionesUsuarios')
                    <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
                    <button wire:click="descargarEjemploUbicacionCliente"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Ubicaciones
                    </button>
                @elseif ($tipoDatos === 'Usuarios')
                    <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
                    <button wire:click="descargarEjemploUsuarios"
                        class="px-5 py-2 bg-gray-600 text-white rounded-lg flex items-center gap-2 hover:bg-gray-700 transition">
                        <i class="fas fa-download"></i> Descargar Ejemplo Usuarios
                    </button>
                @endif

                <!-- Botón de carga de archivo -->
                <label
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg cursor-pointer flex items-center gap-2 hover:bg-blue-700 transition">
                    <i class="fas fa-upload"></i> Subir Archivo
                    <input type="file" class="hidden" wire:model="archivo">
                </label>
            @endif

            <!-- Botón de vista previa (solo aparece si hay archivo cargado) -->
            @if ($archivo)
                @if ($tipoDatos === 'Activos')
                    <button wire:click="importarArchivoActivo"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Bienes
                    </button>
                @elseif ($tipoDatos === 'Bienes')
                    <button wire:click="importarArchivoActivo"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Bienes
                    </button>
                @elseif ($tipoDatos === 'Clientes')
                    <button wire:click="importarClientes"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Clientes
                    </button>
                @elseif ($tipoDatos === 'Proveedores')
                    <button wire:click="importarArchivoProveedores"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Proveedores
                    </button>
                @elseif ($tipoDatos === 'Usuarios')
                    <button wire:click="importarArchivoUsuario"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Usuarios
                    </button>
                @else
                    <!-- Para Ubicaciones (Propias o Usuarios) -->
                    <button wire:click="importarArchivoUbicaciones"
                        class="bg-green-600 text-white px-5 py-2 rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                        <i class="fas fa-eye"></i> Vista Previa Ubicaciones
                    </button>
                @endif
            @endif
        </div>

        <p class="text-gray-500 text-sm mt-2">
            Seleccione el tipo de datos, el tipo de operación y luego cargue un archivo JSON o XML (o XLSX, según
            corresponda) para su importación.
        </p>
    </div>


    <!-- Previsualización de Ubicaciones -->
    @if (!empty($previewDataUbicacion))
        <!-- Buscador para Ubicaciones -->
        <div>
            <input type="text" wire:model.lazy="searchUbicaciones" placeholder="Buscar por nombre en Ubicaciones..."
                class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
        </div>
        @php
            $columnsToDisplay = [
                'nombre' => 'Nombre',
                'pais' => 'País',
                'provincia' => 'Provincia',
                'ciudad' => 'Ciudad',
                'codigo_postal' => 'Código Postal',
                'calle' => 'Calle',
                'altura' => 'Altura',
                'tipo_de_ubicacion' => 'Tipo de Ubicación',
                'gestor' => 'Gestor',
            ];
        @endphp

        <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
            <h4 class="font-semibold text-lg flex items-center">
                <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
            </h4>

            <!-- Tabla con borde -->
            <table class="min-w-full border-collapse mt-2 border border-gray-300">
                <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                    <tr>
                        @foreach ($columnsToDisplay as $key => $label)
                            <th class="px-4 py-2 font-medium border border-gray-300">
                                {{ $label }}
                                @if ($key === 'tipo_de_ubicacion')
                                    <!-- Global: Select de Tipo de Ubicación con búsqueda -->
                                    <div class="relative"
                                        x-data='{
                                         openDropdownTipo: false,
                                         searchTipo: "",
                                         tipos: @json($tipos),
                                         get filteredTipos() {
                                             return this.tipos.filter(item => item.nombre.toLowerCase().includes(this.searchTipo.toLowerCase()))
                                         }
                                     }'
                                        @click.away="openDropdownTipo = false">
                                        <button type="button" @click="openDropdownTipo = !openDropdownTipo"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalTipoUbicacion') ? '-- Seleccione --' : '{{ $tipos->firstWhere('id_tipo', $globalTipoUbicacion ?? null)->nombre ?? '-- Seleccione --' }}'"></span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownTipo" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchTipo"
                                                    @keydown.enter="openDropdownTipo = false"
                                                    @keydown.escape="openDropdownTipo = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Tipo...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="tipo in filteredTipos" :key="tipo.id_tipo">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownTipo = false; $wire.set('globalTipoUbicacion', tipo.id_tipo); searchTipo = ''">
                                                        <span class="font-normal block" x-text="tipo.nombre"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredTipos.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    @elseif ($key === 'gestor')
                                        <!-- Global: Select de Gestor con búsqueda -->
                                        <div class="relative"
                                            x-data='{
                                                openDropdownGestor: false,
                                                searchGestor: "",
                                                gestores: @json($gestores),
                                                get filteredGestores() {
                                                    return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                }
                                            }'
                                            @click.away="openDropdownGestor = false">
                                            <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('globalGestor') ? '-- Seleccione --' : (function(){
       let g = gestores.find(g => g.cuil == $wire.get('globalGestor'));
       return g ? `${g.cuil} - ${g.name}` : '-- Seleccione --';
   })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownGestor" x-transition
                                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchGestor"
                                                        @keydown.enter="openDropdownGestor = false"
                                                        @keydown.escape="openDropdownGestor = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Gestor...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="gestor in filteredGestores"
                                                        :key="gestor.id">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownGestor = false; $wire.set('globalGestor', gestor.cuil); searchGestor = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredGestores.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @forelse ($paginatedUbicaciones as $index => $row)
                        <tr class="hover:bg-gray-50 transition">
                            @foreach ($columnsToDisplay as $key => $label)
                                <td class="px-4 py-2 border border-gray-300">
                                    @if ($key === 'tipo_de_ubicacion')
                                        <!-- Local: Select de Tipo de Ubicación con búsqueda -->
                                        <div class="relative"
                                            x-data='{
                                             openDropdownTipoRow: false,
                                             searchTipoRow: "",
                                             tipos: @json($tipos),
                                             dropdownStyles: "",
                                             get filteredTipos() {
                                                 return this.tipos.filter(item => item.nombre.toLowerCase().includes(this.searchTipoRow.toLowerCase()))
                                             }
                                         }'
                                            @click.away="openDropdownTipoRow = false">
                                            <button type="button"
                                                @click="openDropdownTipoRow = !openDropdownTipoRow; if(openDropdownTipoRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('localTipoUbicacion.{{ $index }}') ? '-- Seleccione --' : '{{ $tipos->firstWhere('id_tipo', $localTipoUbicacion[$index] ?? null)->nombre ?? '-- Seleccione --' }}'"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownTipoRow" x-transition :style="dropdownStyles"
                                                class="rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchTipoRow"
                                                        @keydown.enter="openDropdownTipoRow = false"
                                                        @keydown.escape="openDropdownTipoRow = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Tipo...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="tipo in filteredTipos" :key="tipo.id_tipo">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownTipoRow = false; $wire.set('localTipoUbicacion.{{ $index }}', tipo.id_tipo); searchTipoRow = ''">
                                                            <span class="font-normal block"
                                                                x-text="tipo.nombre"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredTipos.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    @elseif ($key === 'gestor')
                                        <!-- Local: Select de Gestor con búsqueda -->
                                        <!-- Dropdown Local para Gestor en cada fila -->
                                        <div class="relative"
                                            x-data='{
    openDropdownGestorRow: false,
    searchGestorRow: "",
    gestores: @json($gestores),
    dropdownStyles: "",
    get filteredGestores() {
        return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
    }
}'
                                            @click.away="openDropdownGestorRow = false">
                                            <button type="button"
                                                @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('localGestor.{{ $index }}') ? '-- Seleccione --' : (function(){
       let g = gestores.find(g => g.cuil == $wire.get('localGestor.{{ $index }}'));
       return g ? `${g.cuil} - ${g.name}` : '-- Seleccione --';
   })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownGestorRow" x-transition :style="dropdownStyles"
                                                class="rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchGestorRow"
                                                        @keydown.enter="openDropdownGestorRow = false"
                                                        @keydown.escape="openDropdownGestorRow = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Gestor...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="gestor in filteredGestores"
                                                        :key="gestor.id">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownGestorRow = false; $wire.set('localGestor.{{ $index }}', gestor.cuil); searchGestorRow = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredGestores.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    @else
                                        {{ $row[$key] ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($tipoDatos == 'UbicacionesPropias')
                <div class="flex justify-end mt-4">
                    <button wire:click="confirmarImportubicacionesPropias"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                        <i class="fas fa-save"></i> Importar Ubicaciones
                    </button>
                </div>
            @else
                <div class="flex justify-end mt-4">
                    <button wire:click="confirmarImportUbicacionesClientes"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                        <i class="fas fa-save"></i> Importar Ubicaciones
                    </button>
                </div>
            @endif
            <div class="flex justify-end mt-4">
                {{ $paginatedUbicaciones->links() }}
            </div>
        </div>
    @endif

    @if (!empty($previewDataActivo))
        <!-- Buscador para Activos -->
        <div>
            <input type="text" wire:model.lazy="searchActivos" placeholder="Buscar por nombre en Activos..."
                class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
        </div>
        @php
            // Definir las columnas en el orden deseado
            $columnsToDisplay = [
                'nombre' => 'Nombre',
                'id_subcategoria' => 'Subcategoría',
                'id_categoria' => 'Categoría',
                'id_tipo' => 'Tipo',
                'id_estado_sit_alta' => 'Estado Sit Alta',
                'id_estado_sit_general' => 'Estado Sit General',
                'id_ubicacion' => 'Ubicación',
            ];

            $extraColumnsBienes = [
                'gestor' => 'Gestor',
                'responsable' => 'Responsable',
                'asignado' => 'Asignado',
            ];
        @endphp

        <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
            <h4 class="font-semibold text-lg flex items-center">
                <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de Activos:
            </h4>

            <!-- Tabla con borde -->
            <table class="min-w-full border-collapse mt-2 border border-gray-300">
                <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                    <tr>
                        @foreach ($columnsToDisplay as $key => $label)
                            <th class="px-4 py-2 font-medium border border-gray-300">
                                {{ $label }}

                                {{-- Dropdown global según la columna --}}
                                @if ($key === 'id_subcategoria')
                                    <!-- GLOBAL: Select de Subcategoría filtrado por Categoría y deshabilitado hasta seleccionar Categoría -->
                                    <div class="relative" @click.away="open = false"
                                        x-data='{
  open: false,
  search: "",
  items: @json($subcategorias)
}'>
                                        <button type="button"
                                            @click="if ($wire.get('globalCategoria') > 0) open = !open"
                                            :disabled="$wire.get('globalCategoria') <= 0"
                                            :class="{
                                                'opacity-50 cursor-not-allowed': $wire.get('globalCategoria') <= 0,
                                                'opacity-100 cursor-pointer': $wire.get('globalCategoria') > 0
                                            }"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm
      pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1
      focus:ring-blue-500 transition">
                                            <span
                                                x-text="
   $wire.get('globalCategoria') > 0
     ? (
         $wire.get('globalSubcategoria') > 0
           ? (items.find(i => i.id_subcategoria == $wire.get('globalSubcategoria')) || {}).nombre
           : ' Seleccione Subcategoría '
       )
     : 'Seleccione una categoría'
 "></span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71
            a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06
            0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>

                                        <div x-show="open" x-transition
                                            class="absolute mt-1 w-full bg-white shadow-lg z-10 rounded-md">
                                            <div class="p-2">
                                                <input type="text" x-model="search" @keydown.enter="open = false"
                                                    @keydown.escape="open = false"
                                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Subcategoría...">
                                            </div>
                                            <ul
                                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                                <template
                                                    x-for="item in items
     .filter(i => i.id_categoria == $wire.get('globalCategoria'))
     .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                                    :key="item.id_subcategoria">
                                                    <li @click="open = false;
             $wire.set('globalSubcategoria', item.id_subcategoria);
             search = ''"
                                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                        <span x-text="item.nombre"></span>
                                                    </li>
                                                </template>

                                                <template
                                                    x-if="
     items
       .filter(i => i.id_categoria == $wire.get('globalCategoria'))
       .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
       .length === 0
   ">
                                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'id_categoria')
                                    <!-- GLOBAL: Select de Categoría filtrado por Tipo y deshabilitado hasta seleccionar Tipo -->
                                    <div class="relative" @click.away="open = false"
                                        x-data='{
  open: false,
  search: "",
  items: @json($categorias)
}'>
                                        <button type="button" @click="if ($wire.get('globalTipo') > 0) open = !open"
                                            :disabled="$wire.get('globalTipo') <= 0"
                                            :class="{
                                                'opacity-50 cursor-not-allowed': $wire.get('globalTipo') <= 0,
                                                'opacity-100 cursor-pointer': $wire.get('globalTipo') > 0
                                            }"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left
      focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                                            <span
                                                x-text="
 $wire.get('globalTipo') > 0
   ? (
       $wire.get('globalCategoria') > 0
         ? (items.find(i => i.id_categoria == $wire.get('globalCategoria')) || {}).nombre
         : ' Seleccione Categoría '
     )
   : 'Seleccione un tipo'
">
                                            </span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0
            01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>

                                        <div x-show="open" x-transition
                                            class="absolute mt-1 w-full bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="search" @keydown.enter="open = false"
                                                    @keydown.escape="open = false"
                                                    class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Categoría...">
                                            </div>
                                            <ul
                                                class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                                <template
                                                    x-for="item in items
     .filter(i => i.id_tipo == $wire.get('globalTipo'))
     .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                                    :key="item.id_categoria">
                                                    <li @click="open = false;
            $wire.set('globalCategoria', item.id_categoria);
            search = ''"
                                                        class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                        <span x-text="item.nombre"></span>
                                                    </li>
                                                </template>

                                                <template
                                                    x-if="
     items
       .filter(i => i.id_tipo == $wire.get('globalTipo'))
       .filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
       .length === 0
   ">
                                                    <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'id_tipo')
                                    <!-- GLOBAL: Select de Tipo -->
                                    <div class="relative"
                                        x-data='{
                                                openDropdownGlobalTipo: false,
                                                searchGlobalTipo: "",
                                                tipos: @json($tiposActivos),
                                                get filteredTipos() {
                                                    return this.tipos.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalTipo.toLowerCase())
                                                    )
                                                }
                                            }'
                                        @click.away="openDropdownGlobalTipo = false">
                                        <button type="button"
                                            @click="openDropdownGlobalTipo = !openDropdownGlobalTipo"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalTipo')
                                                    ? 'Seleccione'
                                                    : '{{ $tiposActivos->firstWhere('id_tipo', $globalTipo ?? null)->nombre ?? 'Seleccione' }}'">
                                            </span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownGlobalTipo" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchGlobalTipo"
                                                    @keydown.enter="openDropdownGlobalTipo = false"
                                                    @keydown.escape="openDropdownGlobalTipo = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Tipo...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="item in filteredTipos" :key="item.id_tipo">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownGlobalTipo = false; $wire.set('globalTipo', item.id_tipo); searchGlobalTipo = ''">
                                                        <span class="font-normal block" x-text="item.nombre"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredTipos.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'id_estado_sit_alta')
                                    <!-- GLOBAL: Select de Estado Sit Alta -->
                                    <div class="relative"
                                        x-data='{
                                                openDropdownGlobalSitAlta: false,
                                                searchGlobalSitAlta: "",
                                                estadosSitAlta: @json($estadosSitAlta),
                                                get filteredSitAlta() {
                                                    return this.estadosSitAlta.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalSitAlta.toLowerCase())
                                                    )
                                                }
                                            }'
                                        @click.away="openDropdownGlobalSitAlta = false">
                                        <button type="button"
                                            @click="openDropdownGlobalSitAlta = !openDropdownGlobalSitAlta"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalSitAlta')
                                                    ? 'Seleccione '
                                                    : '{{ $estadosSitAlta->firstWhere('id_estado_sit_alta', $globalSitAlta ?? null)->nombre ?? 'Seleccione' }}'">
                                            </span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownGlobalSitAlta" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchGlobalSitAlta"
                                                    @keydown.enter="openDropdownGlobalSitAlta = false"
                                                    @keydown.escape="openDropdownGlobalSitAlta = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Estado Sit Alta...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="item in filteredSitAlta"
                                                    :key="item.id_estado_sit_alta">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownGlobalSitAlta = false; $wire.set('globalSitAlta', item.id_estado_sit_alta); searchGlobalSitAlta = ''">
                                                        <span class="font-normal block" x-text="item.nombre"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredSitAlta.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'id_estado_sit_general')
                                    <!-- GLOBAL: Select de Estado Sit General -->
                                    <div class="relative"
                                        x-data='{
                                                openDropdownGlobalEstadoGeneral: false,
                                                searchGlobalEstadoGeneral: "",
                                                estadosSitGeneral: @json($estadosSitGeneral),
                                                get filteredEstadoGeneral() {
                                                    return this.estadosSitGeneral.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalEstadoGeneral.toLowerCase())
                                                    )
                                                }
                                            }'
                                        @click.away="openDropdownGlobalEstadoGeneral = false">
                                        <button type="button"
                                            @click="openDropdownGlobalEstadoGeneral = !openDropdownGlobalEstadoGeneral"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalEstadoGeneral')
                                                    ? 'Seleccione'
                                                    : '{{ $estadosSitGeneral->firstWhere('id_estado_sit_general', $globalEstadoGeneral ?? null)->nombre ?? 'Seleccione' }}'">
                                            </span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownGlobalEstadoGeneral" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchGlobalEstadoGeneral"
                                                    @keydown.enter="openDropdownGlobalEstadoGeneral = false"
                                                    @keydown.escape="openDropdownGlobalEstadoGeneral = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Estado Sit General...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="item in filteredEstadoGeneral"
                                                    :key="item.id_estado_sit_general">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownGlobalEstadoGeneral = false; $wire.set('globalEstadoGeneral', item.id_estado_sit_general); searchGlobalEstadoGeneral = ''">
                                                        <span class="font-normal block" x-text="item.nombre"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredEstadoGeneral.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'id_ubicacion')
                                    <!-- GLOBAL: Select de Ubicación -->
                                    <div class="relative"
                                        x-data='{
                                                openDropdownGlobalUbicacion: false,
                                                searchGlobalUbicacion: "",
                                                ubicaciones: @json($ubicaciones),
                                                get filteredUbicaciones() {
                                                    return this.ubicaciones.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchGlobalUbicacion.toLowerCase())
                                                    )
                                                }
                                            }'
                                        @click.away="openDropdownGlobalUbicacion = false">
                                        <button type="button"
                                            @click="openDropdownGlobalUbicacion = !openDropdownGlobalUbicacion"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get('globalUbicacion')
                                                    ? 'Seleccione '
                                                    : ubicaciones.find(u => u.id_ubicacion == $wire.get('globalUbicacion'))?.nombre ?? 'Seleccione'">
                                            </span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>

                                        <div x-show="openDropdownGlobalUbicacion" x-transition
                                            class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchGlobalUbicacion"
                                                    @keydown.enter="openDropdownGlobalUbicacion = false"
                                                    @keydown.escape="openDropdownGlobalUbicacion = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Ubicación...">
                                            </div>

                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="item in filteredUbicaciones"
                                                    :key="item.id_ubicacion">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownGlobalUbicacion = false; $wire.set('globalUbicacion', item.id_ubicacion); searchGlobalUbicacion = ''">
                                                        <span class="font-normal block" x-text="item.nombre"></span>
                                                    </li>
                                                </template>

                                                <template x-if="filteredUbicaciones.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                        @endforeach

                        @if ($tipoDatos === 'Bienes' || $tipoDatos === 'Activos'){{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                            @foreach ($extraColumnsBienes as $key => $label)
                                <th class="px-4 py-2 font-medium border border-gray-300">
                                    {{ $label }}
                                    @if ($key === 'gestor')
                                        <!-- Global: Select de Gestor con búsqueda -->
                                        <div class="relative"
                                            x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                                            @click.away="openDropdownGestor = false">
                                            <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('globalGestores') ? ' Seleccione ' : (function(){
                let g = gestores.find(g => g.id == $wire.get('globalGestores'));
                return g ? `${g.cuil} - ${g.name}` : ' Seleccione ';
            })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownGestor" x-transition
                                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchGestor"
                                                        @keydown.enter="openDropdownGestor = false"
                                                        @keydown.escape="openDropdownGestor = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Gestor...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="gestor in filteredGestores"
                                                        :key="gestor.id">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownGestor = false; $wire.set('globalGestores', gestor.id); searchGestor = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredGestores.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    @elseif ($key === 'responsable')
                                        <!-- Global: Select de Gestor con búsqueda -->
                                        <div class="relative"
                                            x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                                            @click.away="openDropdownGestor = false">
                                            <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('globalResponsable') ? ' Seleccione ' : (function(){
                let g = gestores.find(g => g.id == $wire.get('globalResponsable'));
                return g ? `${g.cuil} - ${g.name}` : ' Seleccione';
            })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownGestor" x-transition
                                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchGestor"
                                                        @keydown.enter="openDropdownGestor = false"
                                                        @keydown.escape="openDropdownGestor = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Gestor...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="gestor in filteredGestores"
                                                        :key="gestor.id">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownGestor = false; $wire.set('globalResponsable', gestor.id); searchGestor = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredGestores.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    @elseif ($key === 'asignado')
                                        <!-- Global: Select de Gestor con búsqueda -->
                                        <div class="relative"
                                            x-data='{
                                                            openDropdownGestor: false,
                                                            searchGestor: "",
                                                            gestores: @json($gestores),
                                                            get filteredGestores() {
                                                                return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestor.toLowerCase()))
                                                            }
                                                        }'
                                            @click.away="openDropdownGestor = false">
                                            <button type="button" @click="openDropdownGestor = !openDropdownGestor"
                                                class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                <span
                                                    x-text="!$wire.get('globalAsignado') ? ' Seleccione ' : (function(){
                let g = gestores.find(g => g.id == $wire.get('globalAsignado'));
                return g ? `${g.cuil} - ${g.name}` : ' Seleccione';
            })()"></span>
                                                <span
                                                    class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </button>
                                            <div x-show="openDropdownGestor" x-transition
                                                class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-10">
                                                <div class="p-2">
                                                    <input type="text" x-model="searchGestor"
                                                        @keydown.enter="openDropdownGestor = false"
                                                        @keydown.escape="openDropdownGestor = false"
                                                        class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Buscar Gestor...">
                                                </div>
                                                <ul
                                                    class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                    <template x-for="gestor in filteredGestores"
                                                        :key="gestor.id">
                                                        <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                            @click="openDropdownGestor = false; $wire.set('globalAsignado', gestor.id); searchGestor = ''">
                                                            <span class="font-normal block"
                                                                x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                        </li>
                                                    </template>
                                                    <template x-if="filteredGestores.length === 0">
                                                        <li
                                                            class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                            Sin resultados
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                </th>
                            @endif
                        @endforeach
    @endif
    </th>
    </tr>
    </thead>
    <tbody class="text-gray-600">
        @forelse ($paginatedActivos as $index => $row)
            <tr class="hover:bg-gray-50 transition">
                @foreach ($columnsToDisplay as $key => $label)
                    <td class="px-4 py-2 border border-gray-300">
                        @if ($key === 'id_subcategoria')
                            <!-- LOCAL: Select de Subcategoría con búsqueda -->
                            <div class="relative" @click.away="open = false"
                                x-data='{
open: false,
search: "",
items: @json($subcategorias)
}'>
                                <button type="button" @click="if ($wire.get('globalCategoria') > 0) open = !open"
                                    :disabled="$wire.get('globalCategoria') <= 0"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': $wire.get('globalCategoria') <= 0,
                                        'opacity-100 cursor-pointer': $wire.get('globalCategoria') > 0
                                    }"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm
pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1
focus:ring-blue-500 transition">
                                    <span
                                        x-text="
$wire.get('globalCategoria') > 0
? (
$wire.get('globalSubcategoria') > 0
? (items.find(i => i.id_subcategoria == $wire.get('globalSubcategoria')) || {}).nombre
: ' Seleccione Subcategoría '
)
: 'Seleccione una categoría primero'
"></span>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71
a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06
0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="open" x-transition
                                    class="absolute mt-1 w-full bg-white shadow-lg z-10 rounded-md">
                                    <div class="p-2">
                                        <input type="text" x-model="search" @keydown.enter="open = false"
                                            @keydown.escape="open = false"
                                            class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Subcategoría...">
                                    </div>
                                    <ul
                                        class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                        <template
                                            x-for="item in items
.filter(i => i.id_categoria == $wire.get('globalCategoria'))
.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                            :key="item.id_subcategoria">
                                            <li @click="open = false;
 $wire.set('globalSubcategoria', item.id_subcategoria);
 search = ''"
                                                class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                <span x-text="item.nombre"></span>
                                            </li>
                                        </template>

                                        <template
                                            x-if="
items
.filter(i => i.id_categoria == $wire.get('globalCategoria'))
.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
.length === 0
">
                                            <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        @elseif ($key === 'id_categoria')
                            <!-- GLOBAL: Select de Categoría filtrado por Tipo y deshabilitado hasta seleccionar Tipo -->
                            <div class="relative" @click.away="open = false"
                                x-data='{
open: false,
search: "",
items: @json($categorias)
}'>
                                <button type="button" @click="if ($wire.get('globalTipo') > 0) open = !open"
                                    :disabled="$wire.get('globalTipo') <= 0"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': $wire.get('globalTipo') <= 0,
                                        'opacity-100 cursor-pointer': $wire.get('globalTipo') > 0
                                    }"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left
focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                                    <span
                                        x-text="
$wire.get('globalTipo') > 0
? (
$wire.get('globalCategoria') > 0
? (items.find(i => i.id_categoria == $wire.get('globalCategoria')) || {}).nombre
: ' Seleccione Categoría '
)
: 'Seleccione un tipo'
">
                                    </span>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0
01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="open" x-transition class="absolute mt-1 w-full bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="search" @keydown.enter="open = false"
                                            @keydown.escape="open = false"
                                            class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Categoría...">
                                    </div>
                                    <ul
                                        class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                        <template
                                            x-for="item in items
.filter(i => i.id_tipo == $wire.get('globalTipo'))
.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))"
                                            :key="item.id_categoria">
                                            <li @click="open = false;
$wire.set('globalCategoria', item.id_categoria);
search = ''"
                                                class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                <span x-text="item.nombre"></span>
                                            </li>
                                        </template>

                                        <template
                                            x-if="
items
.filter(i => i.id_tipo == $wire.get('globalTipo'))
.filter(i => i.nombre.toLowerCase().includes(search.toLowerCase()))
.length === 0
">
                                            <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        @elseif ($key === 'id_categoria')
                            <!-- LOCAL: Select de Categoría con búsqueda -->
                            <div class="relative" @click.away="openLocalCat = false" x-data="{
                                openLocalCat: false,
                                searchLocalCat: '',
                                dropdownStylesCat: '',
                                itemsCat: @json($categorias)
                            }">
                                <button type="button"
                                    @click="
 if ($wire.get('localTipo.{{ $index }}') > 0) {
   openLocalCat = !openLocalCat;
   if (openLocalCat) {
     const rect = $el.getBoundingClientRect();
     dropdownStylesCat = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`;
   }
 }
"
                                    :disabled="$wire.get('localTipo.{{ $index }}') <= 0"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': $wire.get('localTipo.{{ $index }}') <=
                                            0,
                                        'opacity-100 cursor-pointer': $wire.get('localTipo.{{ $index }}') > 0
                                    }"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left
      focus:outline-none focus:ring-1 focus:ring-blue-500 transition">
                                    <span
                                        x-text="
 $wire.get('localTipo.{{ $index }}') > 0
   ? (
       $wire.get('localCategoria.{{ $index }}') > 0
         ? (itemsCat.find(i => i.id_categoria == $wire.get('localCategoria.{{ $index }}')) || {}).nombre
         : 'Seleccione Categoría'
     )
   : 'Seleccione tipo'
">
                                    </span>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0
            111.06 1.06l-4.24 4.24a.75.75 0
            01-1.06 0l-4.24-4.24a.75.75 0
            01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="openLocalCat" x-transition :style="dropdownStylesCat"
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalCat"
                                            @keydown.enter="openLocalCat = false"
                                            @keydown.escape="openLocalCat = false"
                                            class="w-full border rounded-md px-2 py-1 mb-2 focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Categoría..." />
                                    </div>
                                    <ul
                                        class="max-h-60 overflow-auto ring-1 ring-black ring-opacity-5 rounded-md py-1 text-base">
                                        <template
                                            x-for="item in itemsCat
     .filter(i => i.id_tipo == $wire.get('localTipo.{{ $index }}'))
     .filter(i => i.nombre.toLowerCase().includes(searchLocalCat.toLowerCase()))"
                                            :key="item.id_categoria">
                                            <li @click="openLocalCat = false;
             $wire.set('localCategoria.{{ $index }}', item.id_categoria);
             searchLocalCat = ''"
                                                class="cursor-pointer py-2 pl-3 pr-9 hover:bg-blue-100">
                                                <span x-text="item.nombre"></span>
                                            </li>
                                        </template>

                                        <template
                                            x-if="
     itemsCat
       .filter(i => i.id_tipo == $wire.get('localTipo.{{ $index }}'))
       .filter(i => i.nombre.toLowerCase().includes(searchLocalCat.toLowerCase()))
       .length === 0
   ">
                                            <li class="py-2 pl-3 pr-9 text-gray-500">Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        @elseif ($key === 'id_tipo')
                            <!-- LOCAL: Select de Tipo con búsqueda -->
                            <div class="relative"
                                x-data='{
                                                openDropdownLocalTipo: false,
                                                searchLocalTipo: "",
                                                dropdownStyles: "",
                                                tipos: @json($tiposActivos),
                                                get filteredTipos() {
                                                    return this.tipos.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalTipo.toLowerCase())
                                                    )
                                                }
                                            }'
                                @click.away="openDropdownLocalTipo = false">
                                <button type="button"
                                    @click="openDropdownLocalTipo = !openDropdownLocalTipo; if(openDropdownLocalTipo){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                    class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span
                                        x-text="!$wire.get('localTipo.{{ $index }}')
                                                    ? ' Seleccione'
                                                    : '{{ $tiposActivos->firstWhere('id_tipo', $localTipo[$index] ?? null)->nombre ?? 'Seleccione' }}'">
                                    </span>
                                    <span
                                        class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>
                                <div x-show="openDropdownLocalTipo" x-transition :style="dropdownStyles"
                                    class="rounded-md bg-white shadow-lg z-10">
                                    <div class="p-2">
                                        <input type="text" x-model="searchLocalTipo"
                                            @keydown.enter="openDropdownLocalTipo = false"
                                            @keydown.escape="openDropdownLocalTipo = false"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            placeholder="Buscar Tipo...">
                                    </div>
                                    <ul
                                        class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                        <template x-for="item in filteredTipos" :key="item.id_tipo">
                                            <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                @click="openDropdownLocalTipo = false; $wire.set('localTipo.{{ $index }}', item.id_tipo); searchLocalTipo = ''">
                                                <span class="font-normal block" x-text="item.nombre"></span>
                                            </li>
                                        </template>
                                        <template x-if="filteredTipos.length === 0">
                                            <li
                                                class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                Sin resultados</li>
                                        </template>
                                    </ul>
                                </div>
                            @elseif ($key === 'id_estado_sit_alta')
                                <!-- LOCAL: Select de Estado Sit Alta con búsqueda -->
                                <div class="relative"
                                    x-data='{
                                                openDropdownLocalSitAlta: false,
                                                searchLocalSitAlta: "",
                                                dropdownStyles: "",
                                                estadosSitAlta: @json($estadosSitAlta),
                                                get filteredSitAlta() {
                                                    return this.estadosSitAlta.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalSitAlta.toLowerCase())
                                                    )
                                                }
                                            }'
                                    @click.away="openDropdownLocalSitAlta = false">
                                    <button type="button"
                                        @click="openDropdownLocalSitAlta = !openDropdownLocalSitAlta; if(openDropdownLocalSitAlta){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('localSitAlta.{{ $index }}')
                                                    ? 'Seleccione'
                                                    : '{{ $estadosSitAlta->firstWhere('id_estado_sit_alta', $localSitAlta[$index] ?? null)->nombre ?? 'Seleccione' }}'">
                                        </span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownLocalSitAlta" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchLocalSitAlta"
                                                @keydown.enter="openDropdownLocalSitAlta = false"
                                                @keydown.escape="openDropdownLocalSitAlta = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Estado Sit Alta...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="item in filteredSitAlta" :key="item.id_estado_sit_alta">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownLocalSitAlta = false; $wire.set('localSitAlta.{{ $index }}', item.id_estado_sit_alta); searchLocalSitAlta = ''">
                                                    <span class="font-normal block" x-text="item.nombre"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredSitAlta.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @elseif ($key === 'id_estado_sit_general')
                                <!-- LOCAL: Select de Estado Sit General con búsqueda -->
                                <div class="relative"
                                    x-data='{
                                        openDropdownLocalEG: false,
                                        searchLocalEG: "",
                                        dropdownStylesEG: "",
                                        estados: @json($estadosSitGeneral),
                                        get filteredEstadoGeneral() {
                                            return this.estados.filter(item =>
                                            item.nombre.toLowerCase().includes(this.searchLocalEG.toLowerCase())
                                            )
                                        }
}'
                                    @click.away="openDropdownLocalEG = false">
                                    <button type="button"
                                        @click="openDropdownLocalEG = !openDropdownLocalEG;
             if (openDropdownLocalEG) {
               const rect = $el.getBoundingClientRect();
               dropdownStylesEG = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`
             }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="! $wire.get('localEstadoGeneral.{{ $index }}')
              ? 'Seleccione'
              : (estados.find(e => e.id_estado_sit_general == $wire.get('localEstadoGeneral.{{ $index }}')) || {}).nombre">
                                        </span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0
            111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0
            01.02-1.06z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="openDropdownLocalEG" x-transition :style="dropdownStylesEG"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchLocalEG"
                                                @keydown.enter="openDropdownLocalEG = false"
                                                @keydown.escape="openDropdownLocalEG = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Estado Sit General...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="item in filteredEstadoGeneral"
                                                :key="item.id_estado_sit_general">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownLocalEG = false;
               $wire.set('localEstadoGeneral.{{ $index }}', item.id_estado_sit_general);
               searchLocalEG = ''">
                                                    <span class="font-normal block" x-text="item.nombre"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredEstadoGeneral.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @elseif ($key === 'id_ubicacion')
                                <!-- LOCAL: Select de Ubicación con búsqueda -->
                                <div class="relative"
                                    x-data='{
                                                openDropdownLocalUbicacion: false,
                                                searchLocalUbicacion: "",
                                                dropdownStyles: "",
                                                ubicaciones: @json($ubicaciones),
                                                get filteredUbicaciones() {
                                                    return this.ubicaciones.filter(item =>
                                                        item.nombre.toLowerCase().includes(this.searchLocalUbicacion.toLowerCase())
                                                    )
                                                }
                                            }'
                                    @click.away="openDropdownLocalUbicacion = false">
                                    <button type="button"
                                        @click="openDropdownLocalUbicacion = !openDropdownLocalUbicacion; if(openDropdownLocalUbicacion){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('localUbicacion.{{ $index }}')
                                                    ? 'Seleccione'
                                                    : '{{ $ubicaciones->firstWhere('id_ubicacion', $localUbicacion[$index] ?? null)->nombre ?? 'Seleccione' }}'">
                                        </span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownLocalUbicacion" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchLocalUbicacion"
                                                @keydown.enter="openDropdownLocalUbicacion = false"
                                                @keydown.escape="openDropdownLocalUbicacion = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Ubicación...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="item in filteredUbicaciones" :key="item.id_ubicacion">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownLocalUbicacion = false; $wire.set('localUbicacion.{{ $index }}', item.id_ubicacion); searchLocalUbicacion = ''">
                                                    <span class="font-normal block" x-text="item.nombre"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredUbicaciones.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @else
                                {{ $row[$key] ?? '' }}
                        @endif
                    </td>
                @endforeach

                {{-- Si es 'Bienes', agregar las columnas adicionales --}}
                @if ($tipoDatos === 'Bienes' || $tipoDatos === 'Activos')
                    {{-- Si el tipo de datos es 'bienes', mostrar las columnas adicionales --}}
                    @foreach ($extraColumnsBienes as $key => $label)
                        <td class="px-4 py-2 border border-gray-300">
                            @if ($key === 'gestor')
                                <!-- Local: Select de Gestor con búsqueda -->
                                <!-- Dropdown Local para Gestor en cada fila -->
                                <div class="relative"
                                    x-data='{
    openDropdownGestorRow: false,
    searchGestorRow: "",
    gestores: @json($gestores),
    dropdownStyles: "",
    get filteredGestores() {
        return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
    }
}'
                                    @click.away="openDropdownGestorRow = false">
                                    <button type="button"
                                        @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('localGestores.{{ $index }}') ? 'Seleccione' : (function(){
                                                    let g = gestores.find(g => g.id == $wire.get('localGestores.{{ $index }}'));
                                                    return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
                                                })()"></span>

                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownGestorRow" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchGestorRow"
                                                @keydown.enter="openDropdownGestorRow = false"
                                                @keydown.escape="openDropdownGestorRow = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Gestor...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="gestor in filteredGestores" :key="gestor.id">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownGestorRow = false; $wire.set('localGestores.{{ $index }}', gestor.id); searchGestorRow = ''">
                                                    <span class="font-normal block"
                                                        x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredGestores.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @elseif ($key === 'responsable')
                                <!-- Local: Select de Gestor con búsqueda -->
                                <div class="relative"
                                    x-data='{
    openDropdownGestorRow: false,
    searchGestorRow: "",
    gestores: @json($gestores),
    dropdownStyles: "",
    get filteredGestores() {
        return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
    }
}'
                                    @click.away="openDropdownGestorRow = false">
                                    <button type="button"
                                        @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('localResponsable.{{ $index }}') ? 'Seleccione' : (function(){
       let g = gestores.find(g => g.id == $wire.get('localResponsable.{{ $index }}'));
       return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
   })()"></span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownGestorRow" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchGestorRow"
                                                @keydown.enter="openDropdownGestorRow = false"
                                                @keydown.escape="openDropdownGestorRow = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Gestor...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="gestor in filteredGestores" :key="gestor.id">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownGestorRow = false; $wire.set('localResponsable.{{ $index }}', gestor.id); searchGestorRow = ''">
                                                    <span class="font-normal block"
                                                        x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredGestores.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            @elseif ($key === 'asignado')
                                <!-- Local: Select de Gestor con búsqueda -->
                                <div class="relative"
                                    x-data='{
    openDropdownGestorRow: false,
    searchGestorRow: "",
    gestores: @json($gestores),
    dropdownStyles: "",
    get filteredGestores() {
        return this.gestores.filter(item => item.name.toLowerCase().includes(this.searchGestorRow.toLowerCase()))
    }
}'
                                    @click.away="openDropdownGestorRow = false">
                                    <button type="button"
                                        @click="openDropdownGestorRow = !openDropdownGestorRow; if(openDropdownGestorRow){ const rect = $el.getBoundingClientRect(); dropdownStyles = `position: fixed; top: ${rect.bottom}px; left: ${rect.left}px; width: ${rect.width}px;`; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('localAsignado.{{ $index }}') ? 'Seleccione' : (function(){
       let g = gestores.find(g => g.id == $wire.get('localAsignado.{{ $index }}'));
       return g ? `${g.cuil} - ${g.name}` : 'Seleccione';
   })()"></span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownGestorRow" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchGestorRow"
                                                @keydown.enter="openDropdownGestorRow = false"
                                                @keydown.escape="openDropdownGestorRow = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Gestor...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="gestor in filteredGestores" :key="gestor.id">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownGestorRow = false; $wire.set('localAsignado.{{ $index }}', gestor.id); searchGestorRow = ''">
                                                    <span class="font-normal block"
                                                        x-text="`${gestor.cuil} - ${gestor.name}`"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredGestores.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                        </td>
                    @endif
                @endforeach
        @endif
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
        </tr>
        @endforelse
    </tbody>
    </table>
    @if ($tipoDatos == 'Activos')
        <div class="flex justify-end mt-4">
            <button wire:click="confirmarImporActivo"
                class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-save"></i> Importar Activos
            </button>
        </div>
    @else
        <div class="flex justify-end mt-4">
            <button wire:click="confirmarImporBien"
                class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-save"></i> Importar Activos
            </button>
        </div>
    @endif
    <div class="flex justify-end mt-4">
        {{ $paginatedActivos->links() }}
    </div>

</div>
@endif

<!-- Previsualización de Proveedores -->
@if (!empty($previewDataProveedores))
    <!-- Buscador para Proveedores -->
    <div>
        <input type="text" wire:model.lazy="searchProveedores" placeholder="Buscar por nombre en Proveedores..."
            class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
    </div>

    @php
        // Ajusta las claves según las que se usen en tu importación.
        $columnsToDisplay = [
            'razon_social' => 'Razón Social',
            'cuit' => 'CUIT',
            'localidad' => 'Localidad',
            'provincia' => 'Provincia',
            'ciudad' => 'Ciudad',
            'codigo_postal' => 'Código Postal',
            'calle' => 'Calle',
            'altura' => 'Altura',
        ];
    @endphp

    <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
        <h4 class="font-semibold text-lg flex items-center">
            <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
        </h4>
        <!-- Tabla con borde -->
        <table class="min-w-full border-collapse mt-2 border border-gray-300">
            <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                <tr>
                    @foreach ($columnsToDisplay as $key => $label)
                        <th class="px-4 py-2 font-medium border border-gray-300">
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @forelse ($paginatedProveedores as $index => $row)
                    <tr class="hover:bg-gray-50 transition">
                        @foreach ($columnsToDisplay as $key => $label)
                            <td class="px-4 py-2 border border-gray-300">
                                {{ $row[$key] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="flex justify-end mt-4">
            <button wire:click="confirmarProveedores"
                class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-save"></i> Importar Proveedores
            </button>
        </div>
        <div class="flex justify-end mt-4">
            {{ $paginatedProveedores->links() }}
        </div>
    </div>
@endif

@if (!empty($previewDataClientes))
    <!-- Buscador para Clientes -->
    <div>
        <!-- Cambiamos la variable de estado a "searchClientes" -->
        <input type="text" wire:model.lazy="searchClientes" placeholder="Buscar por nombre de cliente..."
            class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
    </div>

    @php
        // Ajustamos las columnas según las que se usen en la importación de clientes.
        $columnsToDisplay = [
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'cuil' => 'CUIL',
            'password' => 'Contraseña',
        ];
    @endphp

    <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
        <h4 class="font-semibold text-lg flex items-center">
            <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos (Clientes):
        </h4>
        <!-- Tabla con borde -->
        <table class="min-w-full border-collapse mt-2 border border-gray-300">
            <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                <tr>
                    @foreach ($columnsToDisplay as $key => $label)
                        <th class="px-4 py-2 font-medium border border-gray-300">
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @forelse ($paginatedClientes as $index => $row)
                    <tr class="hover:bg-gray-50 transition">
                        @foreach ($columnsToDisplay as $key => $label)
                            <td class="px-4 py-2 border border-gray-300">
                                {{ $row[$key] ?? '' }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">
                            Sin resultados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="flex justify-end mt-4">
            <!-- Cambiamos la llamada al método de Livewire para confirmar clientes -->
            <button wire:click="confirmarClientes"
                class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-save"></i> Importar Clientes
            </button>
        </div>
        <div class="flex justify-end mt-4">
            <!-- Ajustamos la paginación a $paginatedClientes -->
            {{ $paginatedClientes->links() }}
        </div>
    </div>
@endif
@if (!empty($vistaPreviaUsuario))
    <!-- Buscador para Usuarios -->
    <div>
        <input type="text" wire:model.lazy="searchUsuarios" placeholder="Buscar por nombre de Usuario..."
            class="border rounded-lg px-4 py-2 w-96 text-lg focus:ring-2 focus:ring-blue-500 transition">
    </div>

    @php
        $columnsToDisplay = [
            'name' => 'Name',
            'cuit' => 'CUIT', // Aquí se usará para la selección de Empresa
            'email' => 'Email',
            'password' => 'Contraseña',
            'tipo_usuario' => 'Tipo Usuario',
        ];
    @endphp

    <div class="mt-6 overflow-x-auto w-full min-h-[70vh]">
        <h4 class="font-semibold text-lg flex items-center">
            <i class="fas fa-table mr-2 text-gray-700"></i> Previsualización de datos:
        </h4>
        <table class="min-w-full border-collapse mt-2 border border-gray-300">
            <thead class="bg-blue-100 text-gray-700 border-b border-gray-200">
                <tr>
                    @foreach ($columnsToDisplay as $key => $label)
                        <th class="px-4 py-2 font-medium border border-gray-300">
                            {{ $label }}
                            @if ($key === 'cuit')
                                <!-- Global: Dropdown para selección de Empresa (CUIT) -->
                                <div class="relative"
                                    x-data='{
                                             openDropdownEmpresaGlobal: false,
                                             searchEmpresaGlobal: "",
                                             dropdownStyles: "",
                                             empresas: @json($empresas),
                                             get filteredEmpresas() {
                                                 return this.empresas.filter(item =>
                                                     (item.cuit + " " + item.razon_social)
.toLowerCase().includes(this.searchEmpresaGlobal.toLowerCase())
                                                 )
                                             }
                                         }'
                                    @click.away="openDropdownEmpresaGlobal = false">
                                    <button type="button"
                                        @click="openDropdownEmpresaGlobal = !openDropdownEmpresaGlobal; if(openDropdownEmpresaGlobal){ const rect = $el.getBoundingClientRect(); dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; }"
                                        class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <span
                                            x-text="!$wire.get('globalEmpresa') ? '-- Seleccione --' : (function(){
                                               let e = empresas.find(e => e.cuit == $wire.get('globalEmpresa'));
                                               return e ? `${e.cuit} - ${e.razon_social}` : '-- Seleccione --';
                                           })()"></span>
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div x-show="openDropdownEmpresaGlobal" x-transition :style="dropdownStyles"
                                        class="rounded-md bg-white shadow-lg z-10">
                                        <div class="p-2">
                                            <input type="text" x-model="searchEmpresaGlobal"
                                                @keydown.enter="openDropdownEmpresaGlobal = false"
                                                @keydown.escape="openDropdownEmpresaGlobal = false"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Buscar Empresa...">
                                        </div>
                                        <ul
                                            class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                            <template x-for="empresa in filteredEmpresas" :key="empresa.cuit">
                                                <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                    @click="openDropdownEmpresaGlobal = false; $wire.set('globalEmpresa', empresa.cuit); searchEmpresaGlobal = ''">
                                                    <span class="font-normal block"
                                                        x-text="`${empresa.cuit} - ${empresa.razon_social}`"></span>
                                                </li>
                                            </template>
                                            <template x-if="filteredEmpresas.length === 0">
                                                <li
                                                    class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                    Sin resultados
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                @elseif ($key === 'tipo_usuario')
                                    <!-- Global: Select para Tipo Usuario -->
                                    <select wire:model="Global_tipo_usario"
                                        x-on:change="Livewire.dispatch('updateAllLocalTipoUsuario', [$event.target.value])"
                                        class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                        <option value="" selected disabled class="text-gray-500">Seleccione
                                            una opción</option>
                                        <option value="2">Usuario Administrador</option>
                                        <option value="1">Usuario Común</option>
                                    </select>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-gray-600">
                @forelse ($paginatedUsuarios as $index => $row)
                    <tr class="hover:bg-gray-50 transition">
                        @foreach ($columnsToDisplay as $key => $label)
                            <td class="px-4 py-2 border border-gray-300">
                                @if ($key === 'cuit')
                                    <!-- Local: Dropdown para selección de Empresa en cada fila -->
                                    <div class="relative"
                                        x-data='{
                                                openDropdownEmpresaLocal: false,
                                                searchEmpresaLocal: "",
                                                dropdownStyles: "",
                                                empresas: @json($empresas->toArray()),
                                                get filteredEmpresas() {
                                                    return this.empresas.filter(item =>
                                                        (item.cuit + " " + item.razon_social)
.toLowerCase().includes(this.searchEmpresaLocal.toLowerCase())
                                                    )
                                                }
                                            }'
                                        @click.away="openDropdownEmpresaLocal = false">
                                        <button type="button"
                                            @click="openDropdownEmpresaLocal = !openDropdownEmpresaLocal; 
                                                    if(openDropdownEmpresaLocal){ 
                                                        const rect = $el.getBoundingClientRect(); 
                                                        dropdownStyles = 'position: fixed; top: ' + rect.bottom + 'px; left: ' + rect.left + 'px; width: ' + rect.width + 'px;'; 
                                                    }"
                                            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-blue-500">
                                            <span
                                                x-text="!$wire.get(`localEmpresa.{{ $index }}`) ? '-- Seleccione --' : (function(){
                                                    let e = empresas.find(e => e.cuit == $wire.get(`localEmpresa.{{ $index }}`));
                                                    return e ? `${e.cuit} - ${e.razon_social}` : '-- Seleccione --';
                                                })()"></span>
                                            <span
                                                class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 10.939l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0l-4.24-4.24a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                        <div x-show="openDropdownEmpresaLocal" x-transition :style="dropdownStyles"
                                            class="rounded-md bg-white shadow-lg z-10">
                                            <div class="p-2">
                                                <input type="text" x-model="searchEmpresaLocal"
                                                    @keydown.enter="openDropdownEmpresaLocal = false"
                                                    @keydown.escape="openDropdownEmpresaLocal = false"
                                                    class="w-full border border-gray-300 rounded-md px-2 py-1 mb-2 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Buscar Empresa...">
                                            </div>
                                            <ul
                                                class="max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                                <template x-for="empresa in filteredEmpresas" :key="empresa.cuit">
                                                    <li class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-100"
                                                        @click="openDropdownEmpresaLocal = false; $wire.set(`localEmpresa.{{ $index }}`, empresa.cuit); searchEmpresaLocal = ''">
                                                        <span class="font-normal block"
                                                            x-text="`${empresa.cuit} - ${empresa.razon_social}`"></span>
                                                    </li>
                                                </template>
                                                <template x-if="filteredEmpresas.length === 0">
                                                    <li
                                                        class="cursor-default select-none relative py-2 pl-3 pr-9 text-gray-500">
                                                        Sin resultados
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                @elseif ($key === 'tipo_usuario')
                                    <!-- Local: Select para Tipo Usuario (por fila) -->
                                    <select wire:model="local_tipo_usario.{{ $index }}"
                                        class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                                        <option value="" selected disabled class="text-gray-500">Seleccione
                                            una opción</option>
                                        <option value="2">Usuario Administrador</option>
                                        <option value="1">Usuario Común</option>
                                    </select>
                                @else
                                    {{ $row[$key] ?? '' }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columnsToDisplay) }}" class="text-center py-4">Sin resultados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="flex justify-end mt-4">
            <button wire:click="confirmarUsuarios"
                class="px-6 py-3 bg-red-600 text-white rounded-lg text-lg font-semibold shadow-lg transition hover:bg-red-700 flex items-center gap-2">
                <i class="fas fa-save"></i> Importar Usuarios
            </button>
        </div>
        <div class="flex justify-end mt-4">
            {{ $paginatedUsuarios->links() }}
        </div>
    </div>
@endif
</div>

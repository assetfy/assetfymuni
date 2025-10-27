<div class="max-w-1xl mx-auto px-2 py-2">
    <!-- Título -->
    <h3 class="text-lg font-semibold text-center text-gray-900 mb-4 flex items-center justify-center">
        <i class="fas fa-file-upload mr-2 text-blue-600"></i> Carga Masiva
    </h3>

    <!-- Contenedor de importación -->
    <div class="mb-5 p-4 bg-gray-100 w-full rounded-lg shadow">
        <label class="block font-medium text-gray-700 mb-2 text-md flex items-center gap-2">
            <strong class="font-semibold">Importar Datos</strong>
        </label>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Select: Tipo de Datos -->
            <div class="relative w-54">
                <select wire:model.live="tipoDatos"
                    class="border rounded-md pl-9 pr-8 h-10 text-md focus:ring-2 focus:ring-blue-500 bg-white w-full">
                    <option value="1" selected disabled class="text-gray-500">Seleccione el tipo de dato</option>
                    <option value="UbicacionesPropias">🏠 Ubicaciones Propias</option>
                    <option value="UbicacionesUsuarios">📍 Ubicaciones de Clientes</option>
                    <option value="Activos">💼 Bienes Propios</option>
                    <option value="Bienes">👥 Bienes de Clientes</option>
                    <!-- <option value="Proveedores">🚚 Proveedores</option>
                    <option value="Clientes">🛒 Clientes</option> -->
                    <option value="Usuarios">👥 Usuarios</option>
                </select>

                <!-- Ícono dentro del select -->
                <div class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15l3.75 3.75m0 0l3.75-3.75M12 18.75V4.5" />
                    </svg>
                </div>
            </div>

            <!-- Select: Tipo de Operación -->
            <div class="relative w-54">
                <select wire:model.live="tipoOperacion"
                    class="border rounded-md pl-9 pr-4 h-10 text-md focus:ring-2 focus:ring-blue-500 bg-white w-full">
                    <option value="1" selected disabled class="text-gray-500">Seleccione una opción</option>
                    <option value="Insertar">➕ Crear</option>
                    <option value="Actualizar">🔄 Actualizar</option>
                </select>

                <!-- Ícono dentro del select -->
                <div class="absolute inset-y-0 left-1 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 15l3.75 3.75m0 0l3.75-3.75M12 18.75V4.5" />
                    </svg>
                </div>
            </div>

            @if ($tipoDatos != '1' && $tipoOperacion != '1')
            <!-- Botón de descarga de ejemplo según el tipo de datos -->
            @if ($tipoDatos === 'Activos')
            <button wire:click="descargarEjemploActivo"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Bienes
            </button>
            @elseif ($tipoDatos === 'Bienes')
            <button wire:click="descargarEjemploBienCliente"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Bienes
            </button>
            @elseif ($tipoDatos === 'Proveedores')
            <button wire:click="descargarEjemploPrestadores"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Proveedores
            </button>
            @elseif ($tipoDatos === 'Clientes')
            <button wire:click="descargarEjemploClientes"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Clientes
            </button>
            @elseif ($tipoDatos === 'UbicacionesPropias')
            <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
            <button wire:click="descargarEjemploUbicacion"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Ubicaciones
            </button>
            @elseif ($tipoDatos === 'UbicacionesUsuarios')
            <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
            <button wire:click="descargarEjemploUbicacionCliente"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Ubicaciones
            </button>
            @elseif ($tipoDatos === 'Usuarios')
            <!-- Para Ubicaciones (Propias o Usuarios), asumiendo que ambas usan el mismo ejemplo -->
            <button wire:click="descargarEjemploUsuarios"
                class="px-4 py-2 text-sm bg-gray-600 text-white rounded-md flex items-center gap-2 hover:bg-gray-700">
                <i class="fas fa-download"></i> Descargar Ejemplo Usuarios
            </button>
            @endif

            <!-- Botón de carga de archivo -->
            <label
                class="bg-blue-600 text-white px-4 py-2 text-sm rounded-md cursor-pointer flex items-center gap-2 hover:bg-blue-700">
                <i class="fas fa-upload"></i> Subir Archivo
                <input type="file" class="hidden" wire:model="archivo">
            </label>
            @endif

            <!-- Botón de vista previa (solo aparece si hay archivo cargado) -->
            @if ($archivo)
            @if ($tipoDatos === 'Activos')
            <button wire:click="importarArchivoActivo"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Bienes
            </button>
            @elseif ($tipoDatos === 'Bienes')
            <button wire:click="importarArchivoActivo('{{ $tipoOperacion }}')"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Bienes
            </button>
            @elseif ($tipoDatos === 'Clientes')
            <button wire:click="importarClientes"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Clientes
            </button>
            @elseif ($tipoDatos === 'Proveedores')
            <button wire:click="importarArchivoProveedores"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Proveedores
            </button>
            @elseif ($tipoDatos === 'Usuarios')
            <button wire:click="importarArchivoUsuario"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Usuarios
            </button>
            @else
            <!-- Para Ubicaciones (Propias o Usuarios) -->
            <button wire:click="importarArchivoUbicaciones"
                class="bg-green-600 text-white px-4 py-2 text-sm rounded-md flex items-center gap-2 hover:bg-green-700">
                <i class="fas fa-eye"></i> Vista Previa Ubicaciones
            </button>
            @endif
            @endif
        </div>

        <p class="text-gray-500 text-xs mt-2">
            Seleccione el tipo de datos, el tipo de operación y luego cargue un archivo JSON o XML (o XLSX, según
            corresponda) para su importación.
        </p>
    </div>

    @include('livewire.importaciones.partials.ubicaciones')

    @include('livewire.importaciones.partials.bienes')

    @include('livewire.importaciones.partials.proveedores')

    @include('livewire.importaciones.partials.clientes')

    @include('livewire.importaciones.partials.usuarios')
</div>
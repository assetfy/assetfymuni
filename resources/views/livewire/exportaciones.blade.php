<div class="mx-10 my-6">
    <!-- Título -->
    <h3 class="text-xl font-semibold text-center text-gray-900 mb-6 flex items-center justify-center">
        <i class="fas fa-file-upload mr-3 text-blue-600"></i> Descarga Masiva
    </h3>

    <!-- Contenedor de importación -->
    <div class="mb-6 p-6 bg-gray-100 w-full rounded-lg shadow-sm">
        <label class="block font-medium text-gray-700 mb-2 text-lg flex items-center gap-2">
            <strong>Exportar Datos</strong>
        </label>

        <div class="flex flex-wrap items-center gap-4">
            <!-- Select: Tipo de Datos -->
            <div class="relative w-70">
                <select wire:model.live="tipoDatos"
                    class="border rounded-lg px-10 py-3 text-lg focus:ring-2 focus:ring-blue-500 transition bg-white appearance-none w-full text-left">
                    <option value="1" selected disabled class="text-gray-500">Seleccione el tipo de dato</option>
                    <option value="UbicacionesPropias">🏠 Ubicaciones Propias</option>
                    <option value="UbicacionesUsuarios">📍 Ubicaciones de Clientes</option>
                    <option value="Activos">💼 Bienes Propios</option>
                    <option value="Bienes">📦 Bienes de Clientes</option>
                    <!-- <option value="Clientes">🛒 Clientes</option>  -->
                    <option value="Usuarios">👤 Usuarios</option>
                    <option value="Ordenes">🛠️ Ordenes de Trabajo</option>
                    <option value="Cotizaciones">💲 Cotizaciones</option>
                    <option value="Solicitudes">📝 Solicitudes</option>
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

            <!-- Botón de descarga de ejemplo según el tipo de datos -->
            @if ($tipoDatos === 'Activos')
            <button wire:click="descargarEjemploActivo"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Bienes Propios
            </button>
            @elseif ($tipoDatos === 'Bienes')
            <button wire:click="descargarEjemploClientes"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Bienes Clientes
            </button>
            @elseif ($tipoDatos === 'UbicacionesPropias')
            <button wire:click="descargarEjemploUbicacionesPropias"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Ubicaciones Propias
            </button>
            @elseif ($tipoDatos === 'UbicacionesUsuarios')
            <button wire:click="descargarEjemploUbicacionesClientes"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Ubicaciones Clientes
            </button>
            @elseif ($tipoDatos === 'Ordenes')
            <button wire:click="descargarEjemploOrdenes"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Ordenes de Trabajo
            </button>
            @elseif ($tipoDatos === 'Cotizaciones')
            <button wire:click="descargarEjemploCotizaciones"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Cotizaciones
            </button>
            @elseif ($tipoDatos === 'Solicitudes')
            <button wire:click="descargarEjemploSolicitudes"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Solicitudes
            </button>
            @elseif ($tipoDatos === 'Usuarios')
            <button wire:click="descargarEjemploUsuario"
                class="px-5 py-2 bg-green-600 text-white rounded-lg flex items-center gap-2 hover:bg-green-700 transition">
                <i class="fas fa-download"></i> Descargar Usuarios
            </button>
            @endif
        </div>
    </div>
</div>
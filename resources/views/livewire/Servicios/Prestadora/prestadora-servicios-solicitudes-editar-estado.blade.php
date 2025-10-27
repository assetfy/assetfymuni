<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl"
        class="fixed inset-0 flex items-center justify-center bg-black/50">

        {{-- Encabezado con Pestañas --}}
        <x-slot name="title">
            <div class="flex justify-center border-b bg-gray-100 sticky top-0 z-10">
                <button wire:click="$set('activeTab', 'solicitud')"
                    class="{{ $activeTab === 'solicitud' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Solicitud
                </button>
                <button wire:click="$set('activeTab', 'atributos')"
                    class="{{ $activeTab === 'atributos' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Atributos
                </button>
                <button wire:click="$set('activeTab', 'servicios')"
                    class="{{ $activeTab === 'servicios' ? 'px-4 py-2 font-semibold text-blue-500 border-b-4 border-blue-500' : 'px-4 py-2 font-semibold text-gray-700 border-b-4 border-transparent hover:border-gray-300' }}">
                    Servicios
                </button>

            </div>
        </x-slot>

        {{-- Contenido del Modal --}}
        <x-slot name="content">
            <div class="bg-white p-6 space-y-6 overflow-auto max-h-[calc(100vh-200px)]">
                {{-- Contenido de la Pestaña Solicitud --}}
                <div class="{{ $activeTab === 'solicitud' ? '' : 'hidden' }}">
                    <h2 class="text-center font-semibold text-xl mb-4">📌 Nombre del Bien</h2>
                    <p class="text-center text-gray-700 mb-4 text-lg">{{ $nombreActivo }}</p>

                    <h3 class="text-center font-semibold text-lg mb-4">🛠️ Servicio Solicitado</h3>
                    <p class="text-center text-gray-700 mb-6 text-lg">{{ $serviciosNombre }}</p>

                    {{-- Selector de Fecha con Mejor Diseño --}}
                    <!-- Mensaje si la fecha fue modificada -->
                    @if ($fechaModificadaMensaje)
                        <div class="mb-6 p-3 bg-yellow-50 text-yellow-700 font-medium rounded-lg shadow-sm">
                            ⚠️ La fecha y hora del servicio se ha modificado. Se volverá a enviar la cotización.
                        </div>
                    @endif

                    <!-- Campo de Fecha y Hora -->
                    <div class="mb-6 p-4 border rounded-lg shadow-sm bg-gray-100">
                        <h3 class="font-semibold text-lg mb-3">📅 Fecha y Hora del Servicio</h3>
                        <x-input type="datetime-local" wire:model.live="fechaHora" class="w-full p-2 rounded border" />
                        <x-input-error for="fechaHora" />
                    </div>

                    {{-- Mostrar solo si hay una fecha seleccionada --}}
                    @if (!empty($fechaHora))
                        <div class="mt-6 p-4 border rounded-lg shadow-sm bg-gray-100">
                            <h3 class="font-semibold text-lg mb-3">⏳ ¿El servicio se realizará el mismo día?</h3>
                            <select class="form-control w-full p-2 rounded border" wire:model.live="realizacionDia">
                                <option value="" hidden>Seleccione una opción</option>
                                <option value="Si">✅ Sí</option>
                                <option value="No">❌ No</option>
                            </select>
                            <x-input-error for="realizacionDia" />

                            {{-- Si el usuario selecciona "No", mostrar el campo de fecha de finalización --}}
                            @if ($realizacionDia == 'No')
                                <div class="mt-4">
                                    <h3 class="font-semibold text-lg mb-3">📅 Fecha y Hora de Finalización</h3>
                                    <x-input type="datetime-local" wire:model.live="fecha_finalizacion"
                                        class="w-full p-2 rounded border" min="{{ $fechaHora }}" />
                                    <x-input-error for="fechaFinalizacion" />
                                </div>
                            @endif
                        </div>
                    @endif

                    <h3 class="font-semibold text-lg mt-6 mb-4">📍 Datos de su Ubicación</h3>
                    <table class="w-full border-collapse shadow-md rounded-lg overflow-hidden">
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2 text-left">🏠 Nombre</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $ubicacionesNombre }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">🌎 País</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $pais }}</td>
                        </tr>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2 text-left">📍 Provincia</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $provincia }}</td>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 text-left">🏙️ Ciudad</th>
                            <td class="border border-gray-300 px-4 py-2">{{ $localidad }}</td>
                        </tr>
                    </table>

                    <div class="mt-6">
                        <strong>🗺️ Mapa de Ubicación:</strong>
                        <div id="mapActivosUbicacion" class="mt-2 border border-gray-300 rounded shadow-sm bg-gray-100"
                            style="width: 100%; height: 200px;" wire:ignore></div>
                    </div>

                    {{-- Formulario de Aceptación de Servicio --}}
                    <div class="mt-6 p-4 border rounded-lg shadow-sm bg-gray-100">
                        <h3 class="font-semibold text-lg mb-3">✅ Aceptación del Servicio</h3>
                        <select class="form-control w-full p-2 rounded border" wire:model.live="aceptacion">
                            <option value="" hidden>Seleccione una opción</option>
                            <option value="Si">✔️ Aceptar</option>
                            <option value="No">❌ Rechazar</option>
                        </select>
                        <x-input-error for="aceptacion" />

                        @if ($aceptacion == 'Si')
                            <div class="mt-4">
                                <label class="font-semibold">📄 Adjuntar Documentos (PDF):</label>
                                <input type="file" wire:model="presupuesto" class="block mt-2">
                                <x-input-error for="presupuesto" />
                            </div>
                            <div class="mt-4">
                                <h3 class="font-semibold text-lg mb-2">💰 Precio</h3>
                                <x-input type="text" class="w-full p-2 rounded border" wire:model="precio"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                <x-input-error for="precio" />
                            </div>
                        @elseif ($aceptacion == 'No')
                            <div class="mt-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                                <h3 class="font-bold">❌ El servicio será rechazado y se informará al usuario.</h3>
                            </div>
                        @endif
                    </div>

                    <section class="p-6 border rounded-lg shadow-sm bg-white">
                        <h3 class="text-center font-semibold text-xl text-gray-800 mb-4">🛡️ Configuración de Garantía
                        </h3>

                        <!-- Selector de Garantía -->
                        <div class="mb-4">
                            <x-label value="¿El servicio tiene garantía?" class="font-semibold text-gray-700" />
                            <select
                                class="form-control w-full p-3 rounded border focus:ring-2 focus:ring-blue-500 transition duration-150"
                                wire:model.live="garantia">
                                <option value="" hidden>Seleccione una opción</option>
                                <option value="Si">✅ Sí</option>
                                <option value="No">❌ No</option>
                            </select>
                        </div>

                        <!-- Campo para ingresar días de garantía -->
                        @if ($garantia == 'Si')
                            <div class="mt-4">
                                <x-label value="📆 Ingrese los días de garantía" class="font-semibold text-gray-700" />
                                <x-input type="number"
                                    class="w-full p-3 border rounded focus:ring-2 focus:ring-green-500 transition"
                                    wire:model.lazy="fecha_garantia" placeholder="Ejemplo: 30, 60, 90" />
                                <x-input-error for="fecha_garantia" />
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-gray-100 text-gray-600 text-center border rounded">
                                🚫 No se aplicará garantía para este servicio.
                            </div>
                        @endif
                    </section>
                </div>
                {{-- Contenido de la Pestaña Atributos --}}
                <div class="{{ $activeTab === 'atributos' ? '' : 'hidden' }}">
                    <!-- Garantía -->
                    <section class="p-6 mb-6 border rounded-lg shadow-sm bg-gray-100">
                        @if ($garantiaActivo === 'Si')
                            <div class="text-center">
                                <h3 class="font-semibold text-xl text-green-600 mb-3">✅ Garantía Vigente</h3>
                                <p class="text-gray-700 text-lg">📅 Vencimiento:
                                    <span class="font-semibold">
                                        {{ \Carbon\Carbon::parse($fecha_garantia)->format('d/m/Y') }}
                                    </span>
                                </p>
                            </div>
                        @else
                            <h3 class="font-semibold text-xl text-center text-red-500 mb-3">❌ Sin garantía vigente</h3>
                        @endif
                    </section>

                    <!-- Atributos -->
                    <section class="p-6 border rounded-lg shadow-sm bg-white">
                        <h3 class="font-semibold text-xl text-center text-gray-800 mb-6">📋 Atributos del Bien</h3>

                        @if ($atributos && $atributos->count())
                            <div class="overflow-x-auto border rounded-lg shadow-sm">
                                <table class="w-full table-auto border-collapse">
                                    <thead class="bg-gray-200 text-gray-800">
                                        <tr>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📛 Nombre
                                            </th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📝
                                                Descripción</th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📏 Unidad de
                                                Medida</th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📌 Tipo de
                                                Campo</th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">✏️ Campo
                                            </th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">🔢 Campo
                                                Numérico</th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📅 Fecha
                                            </th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">📜 Valores
                                                Enum (Lista)</th>
                                            <th class="px-4 py-3 border-b text-left text-sm font-semibold">🎯 Valor
                                                Enum (Único)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($atributos as $atributo)
                                            <tr class="odd:bg-gray-50 hover:bg-gray-100 transition duration-200">
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->atributo->nombre ?? 'n/a' }}</td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->atributo->descripcion ?? 'n/a' }}</td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->atributo->unidadMedida->nombre ?? 'n/a' }}</td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->atributo->tiposCampos->nombre ?? 'n/a' }}</td>
                                                <td class="px-4 py-3 border-b text-sm">{{ $atributo->campo ?? 'n/a' }}
                                                </td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->campo_numerico ?? 'n/a' }}</td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->fecha ? \Carbon\Carbon::parse($atributo->fecha)->format('d/m/Y') : 'n/a' }}
                                                </td>
                                                <!-- Nuevas Celdas -->
                                                <td class="px-4 py-3 border-b text-sm">
                                                    @if ($atributo->campo_enum_list)
                                                        <ul class="list-disc pl-4">
                                                            @foreach (explode(',', $atributo->campo_enum_list) as $valor)
                                                                <li>{{ trim($valor) }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        n/a
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 border-b text-sm">
                                                    {{ $atributo->campo_enum ?? 'n/a' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center">🚫 Sin atributos registrados.</p>
                        @endif
                    </section>
                </div>

                {{-- Contenido de la Pestaña Servicios --}}
                <div class="{{ $activeTab === 'servicios' ? '' : 'hidden' }}">
                    @if ($serviciosRealizados && $serviciosRealizados->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha y Hora</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Servicio</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Descripción</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($serviciosRealizados as $servicio)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $servicio->fechaHora }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $servicio->servicios->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $servicio->descripcion }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $servicio->precio }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-gray-700">No hay servicios registrados para este activo.</p>
                    @endif
                </div>
                {{-- Contenido de la Pestaña Garantia --}}
                <div class="{{ $activeTab === 'Garantia' ? '' : 'hidden' }}">
                    <section class="p-6 border rounded-lg shadow-sm bg-white">
                        <h3 class="text-center font-semibold text-xl text-gray-800 mb-4">🛡️ Configuración de Garantía
                        </h3>

                        <!-- Selector de Garantía -->
                        <div class="mb-4">
                            <x-label value="¿El servicio tiene garantía?" class="font-semibold text-gray-700" />
                            <select
                                class="form-control w-full p-3 rounded border focus:ring-2 focus:ring-blue-500 transition duration-150"
                                wire:model.live="garantia">
                                <option value="" hidden>Seleccione una opción</option>
                                <option value="Si">✅ Sí</option>
                                <option value="No">❌ No</option>
                            </select>
                        </div>

                        <!-- Campo para ingresar días de garantía -->
                        @if ($garantia == 'Si')
                            <div class="mt-4">
                                <x-label value="📆 Ingrese los días de garantía"
                                    class="font-semibold text-gray-700" />
                                <x-input type="number"
                                    class="w-full p-3 border rounded focus:ring-2 focus:ring-green-500 transition"
                                    wire:model.lazy="fecha_garantia" placeholder="Ejemplo: 30, 60, 90" />
                                <x-input-error for="fecha_garantia" />
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-gray-100 text-gray-600 text-center border rounded">
                                🚫 No se aplicará garantía para este servicio.
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </x-slot>

        {{-- Footer con Botones --}}
        <x-slot name="footer">
            <div class="flex justify-end mt-2 space-x-2 sticky bottom-0 bg-white p-1 border-t">
                {{-- Botón Cancelar --}}
                <button wire:click="$set('open', false)"
                    class="px-3 py-1 rounded border border-gray-400 text-gray-700 font-medium bg-white transition-all hover:bg-gray-100 hover:border-gray-500 hover:text-gray-800 shadow-sm text-sm">
                    Cancelar
                </button>

                {{-- Botón Actualizar --}}
                <button wire:click="actualizar"
                    class="px-3 py-1 rounded bg-gradient-to-r from-red-500 to-red-600 text-white font-medium transition-all hover:from-red-600 hover:to-red-700 hover:shadow-lg shadow-md text-sm">
                    Actualizar
                </button>
            </div>
        </x-slot>

    </x-dialog-modal>
</div>

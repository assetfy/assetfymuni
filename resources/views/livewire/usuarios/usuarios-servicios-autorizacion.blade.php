<div>
    <x-dialog-modal wire:model="open" maxWidth="2xl">
        <x-slot name="title">
            <h2 class="text-3xl font-extrabold text-gray-800 text-center mb-4 border-b pb-2">
                {{ $estadoSolicitud }}
            </h2>
        </x-slot>

        <x-slot name="content">
            <h3 class="text-lg font-bold text-gray-800 mb-6">
                Prestadora: <span class="text-blue-600">{{ $empresa->razon_social ?? 'Empresa no encontrada' }}</span>
            </h3>

            @if (isset($activo) && isset($servicio))
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Nombre del Activo -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm border">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">🏷️ Nombre del Activo</label>
                        <p class="text-lg font-bold text-blue-600">{{ $activo->nombre }}</p>
                    </div>

                    <!-- Servicio Solicitado -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm border">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">🛠️ Servicio Solicitado</label>
                        <p class="text-lg font-bold text-blue-600">{{ $servicio->nombre }}</p>
                    </div>
                </div>

                <!-- Mensaje de Modificación de Fecha -->
                @if ($fechaModificada)
                    <div class="mb-6 p-3 bg-yellow-50 text-yellow-700 font-medium rounded-lg shadow-sm">
                        ⚠️ La fecha y hora se modificó y se volverá a enviar la cotización.
                    </div>
                @endif

                <!-- Mensaje de duración o servicio el mismo día -->
                @if ($editar)
                    @if ($fechaModificada == null)
                        <div class="mb-6 text-lg font-semibold">
                            @if ($servicios->fecha_finalizacion)
                                @php
                                    $diasDuracion = \Carbon\Carbon::parse($servicios->fechaHora)->diffInDays(
                                        \Carbon\Carbon::parse($servicios->fecha_finalizacion),
                                    );
                                @endphp
                                <p class="text-blue-600">
                                    📅 El servicio se realizará en un rango de {{ $diasDuracion }}
                                    día{{ $diasDuracion > 1 ? 's' : '' }}.
                                </p>
                            @else
                                <p class="text-green-600">
                                    ✅ El servicio se realizará el mismo día.
                                </p>
                            @endif
                        </div>
                    @endif
                @endif

                <!-- Sección interactiva de edición: solo se muestra si $editar es true -->
                @if ($editar)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Fecha y Hora de Inicio -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">📅 Fecha y Hora de
                                Inicio</label>
                            <input type="datetime-local" id="fechaHora"
                                class="w-full px-4 py-3 rounded-lg border shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                wire:model.live="fechaHora" />
                            <x-input-error for="fechaHora" />
                        </div>

                        <!-- Fecha de Finalización -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">⏳ Fecha de
                                Finalización</label>
                            <div
                                class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg shadow-sm text-lg font-semibold text-gray-800">
                                {{ $servicios->fecha_finalizacion
                                    ? \Carbon\Carbon::parse($servicios->fecha_finalizacion)->format('d/m/Y - H:i')
                                    : 'No definida' }}
                            </div>
                        </div>
                    </div>
                    <!-- Precio y Garantía -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Precio -->
                        <div class="bg-green-50 p-4 rounded-lg shadow-sm border">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">💰 Precio</label>
                            <p class="text-lg font-bold text-green-700 flex items-center">
                                <span class="mr-2">💲</span>{{ $servicios->precio }}
                            </p>
                        </div>

                        <!-- Garantía -->
                        <div
                            class="{{ $servicios->garantia == 'Si' ? 'bg-green-50' : 'bg-red-50' }} p-4 rounded-lg shadow-sm border">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">🛡️ Garantía</label>
                            <p class="text-lg font-bold flex items-center">
                                <span class="mr-2">{{ $servicios->garantia == 'Si' ? '✅' : '❌' }}</span>
                                {{ $servicios->garantia == 'Si' ? $servicios->dias_garantia . ' días' : 'Sin Garantía' }}
                            </p>
                        </div>
                    </div>

                    <!-- Aceptación del Presupuesto -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">📜 Acepta Presupuesto</label>
                        <select
                            class="w-full px-4 py-3 rounded-lg border shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            wire:model.live="aceptacion">
                            <option value="" hidden>Elegir una opción</option>
                            <option value="Si">✅ Sí</option>
                            <option value="No">❌ No</option>
                        </select>
                        <x-input-error for="aceptacion" />
                    </div>

                    <!-- Mensaje de Confirmación o Rechazo -->
                    @if ($aceptacion == 'Si')
                        <div class="mb-6 p-3 bg-green-50 text-green-700 font-medium rounded-lg shadow-sm">
                            ✅ El servicio será realizado en la fecha estipulada.
                        </div>
                    @elseif ($aceptacion == 'No')
                        <div class="mb-6 p-3 bg-red-50 text-red-700 font-medium rounded-lg shadow-sm">
                            ❌ El servicio será rechazado y se informará a la prestadora.
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Motivo del Rechazo</label>
                            <x-input type="text"
                                class="w-full px-4 py-3 rounded-lg border shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                                wire:model.lazy="descripcion" />
                            <x-input-error for="descripcion" />
                        </div>
                    @endif
                @endif
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-4">
                <x-secondary-button
                    class="bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg shadow-md border"
                    wire:click="$set('open', false)">
                    Cancelar
                </x-secondary-button>

                @if ($editar)
                    <x-danger-button class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-lg shadow-md"
                        wire:click="actualizar">
                        Actualizar
                    </x-danger-button>
                @endif
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

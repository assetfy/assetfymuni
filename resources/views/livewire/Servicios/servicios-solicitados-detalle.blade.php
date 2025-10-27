<div>
    <x-dialog-modal wire:model.live="open" maxWidth="2xl"
        class="fixed inset-0 flex items-center justify-center bg-black/50">

        {{-- Header con el Título y Botón de Cerrar --}}
        <x-slot name="title">
            <div class="flex justify-between items-center bg-white p-6 rounded-t-lg shadow">
                <h2 class="text-xl font-bold text-gray-800">Detalles de Servicios</h2>
                <button type="button" wire:click="cerrar" class="text-red-500 hover:text-red-700">
                    <span class="sr-only">Cerrar</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </x-slot>

        {{-- Contenido del Modal --}}
        <x-slot name="content">
            <div class="bg-white p-6 space-y-6 rounded-b-lg shadow">

                {{-- Barra de Navegación para las Pestañas --}}
                <div class="flex justify-between items-center border-b pb-3">
                    <ul class="flex justify-start space-x-4">
                        {{-- Botón Detalle --}}
                        <li>
                            <button wire:click="$set('activeTab', 'Detalle')"
                                class="{{ $activeTab === 'Detalle' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black' }} px-3 py-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="16" x2="12" y2="12" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                </svg>
                                Detalle
                            </button>
                        </li>

                        {{-- Botón Garantía --}}
                        <li>
                            <button wire:click="$set('activeTab', 'Garantía')"
                                class="px-3 py-1 {{ $botonGarantiaDeshabilitado ? 'text-gray-400 cursor-not-allowed' : ($activeTab === 'Garantía' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black') }}"
                                {{ $botonGarantiaDeshabilitado ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6l-8-4-8 4v6a8 8 0 0 0 16 0V6z" />
                                    <polyline points="4.14 8 12 12 19.86 8" />
                                </svg>
                                Garantía
                            </button>
                        </li>

                        {{-- Botón Presupuesto --}}
                        <li>
                            <button wire:click="$set('activeTab', 'Presupuesto')"
                                class="px-3 py-1 {{ $botonPresupuestoDeshabilitado ? 'text-gray-400 cursor-not-allowed' : ($activeTab === 'Presupuesto' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black') }}"
                                {{ $botonPresupuestoDeshabilitado ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                </svg>
                                Presupuesto
                            </button>
                        </li>
                        {{-- Botón Detalle trabajo --}}
                        <li>
                            <button wire:click="$set('activeTab', 'Trabajo')"
                                class="{{ $activeTab === 'Trabajo' ? 'font-bold text-black border-b-2 border-black' : 'text-gray-600 hover:text-black' }} px-3 py-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.7 6.3a5 5 0 01-6.4 6.4l-3.6 3.6a1 1 0 001.4 1.4l3.6-3.6a5 5 0 016.4-6.4l2.3-2.3a1 1 0 10-1.4-1.4l-2.3 2.3z" />
                                </svg>
                                Trabajo
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Contenido de las Pestañas --}}
                @if ($activeTab === 'Detalle')
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="4" />
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            </svg>
                            <strong class="text-gray-700 mr-2">Nombre de la prestadora:</strong>
                            <span class="text-gray-900 font-medium">{{ $prestadora ?? 'No hay datos' }}</span>
                        </div>

                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                            <strong class="text-gray-700 mr-2">Nombre del Solicitante:</strong>
                            <span class="text-gray-900 font-medium">{{ $solicitante ?? 'No hay datos' }}</span>
                        </div>

                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                <line x1="2" y1="10" x2="22" y2="10" />
                            </svg>
                            <strong class="text-gray-700 mr-2">Fecha Solicitada:</strong>
                            <span class="text-gray-900 font-medium">{{ $fechaSolicitada ?? 'No hay datos' }}</span>
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                <line x1="2" y1="10" x2="22" y2="10" />
                            </svg>
                            <strong class="text-gray-700 mr-2">Fecha De realizacion del servicio:</strong>
                            <span
                                class="text-gray-900 font-medium">{{ $fechaRealizacion ?? 'Sin fecha programada' }}</span>
                        </div>
                    </div>
                @elseif ($activeTab === 'Garantía')
                    <div>
                        @if (!$botonGarantiaDeshabilitado)
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6l-8-4-8 4v6a8 8 0 0 0 16 0V6z" />
                                    <polyline points="4.14 8 12 12 19.86 8" />
                                </svg>
                                <strong class="text-gray-700 mr-2">Garantía:</strong>
                                <span class="text-gray-900 font-medium">
                                    {{ $tieneGarantia === 'Si' ? "Tiene garantía por $diasGarantia días" : 'No tiene garantía' }}
                                </span>
                            </div>
                        @else
                            <p class="text-gray-500">Garantía no disponible para este servicio.</p>
                        @endif
                    </div>
                @elseif ($activeTab === 'Presupuesto')
                    <div>
                        @if (!$botonPresupuestoDeshabilitado)
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2" />
                                    <line x1="2" y1="10" x2="22" y2="10" />
                                </svg>
                                <strong class="text-gray-700 mr-2">Precio:</strong>
                                <span
                                    class="text-green-600 font-medium">${{ number_format($precio, 2) ?? 'No disponible' }}</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="14 2 14 8 20 8" />
                                    <rect x="3" y="8" width="18" height="13" rx="2" ry="2" />
                                    <path d="M5 12h14M12 15l3 3-3 3M9 18l3-3" />
                                </svg>
                                @if ($servicio->presupuesto)
                                    <strong class="text-gray-700 mr-2">Presupuesto:</strong>
                                    <a href="{{ Storage::disk('s3')->temporaryUrl($servicio->presupuesto, now()->addMinutes(10)) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        Descargar Presupuesto
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500">Presupuesto no disponible para este servicio.</p>
                        @endif
                    </div>
                @elseif ($activeTab === 'Trabajo')
                    <div class="space-y-6">
                        <!-- Comentario del trabajo -->
                        <div>
                            <h2 class="text-xl font-semibold mb-2 text-gray-800">Comentario del trabajo realizado</h2>
                            <div class="border border-gray-300 rounded-md p-4 min-h-[100px] bg-gray-50 text-gray-700">
                                {{ $trabajo->comentarios ?? 'N/A' }}
                            </div>
                        </div>

                        <!-- Galería de fotos -->
                        @if (count($fotosTrabajo))
                            <div x-data="{ currentIndex: 0 }"
                                class="flex flex-col items-center justify-center gap-4 md:flex-row">

                                <!-- Botón Anterior -->
                                <button type="button"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition"
                                    @click="currentIndex = currentIndex > 0 ? currentIndex - 1 : {{ count($fotosTrabajo) - 1 }}">
                                    ←
                                </button>

                                <!-- Imagen actual -->
                                <div
                                    class="w-full max-w-xs aspect-square overflow-hidden border rounded-md bg-white shadow">
                                    @foreach ($fotosTrabajo as $i => $url)
                                        <img x-show="currentIndex === {{ $i }}" x-transition
                                            src="{{ $url }}" alt="Foto {{ $i + 1 }}"
                                            class="w-full h-full object-cover rounded-md" style="display: none;">
                                    @endforeach
                                </div>

                                <!-- Botón Siguiente -->
                                <button type="button"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition"
                                    @click="currentIndex = currentIndex < {{ count($fotosTrabajo) - 1 }} ? currentIndex + 1 : 0">
                                    →
                                </button>
                            </div>
                        @else
                            <p class="text-center text-gray-500 italic">Sin fotos del servicio</p>
                        @endif
                    </div>
                @endif
            </div>
        </x-slot>

        {{-- Footer --}}
        <x-slot name="footer">
            <div class="mt-6 flex justify-center items-center gap-4">
                @if ($estado == 'Esperando confirmación del cliente.')
                    <button class="bg-blue-500 text-white rounded py-2 px-4 flex items-center gap-2"
                        wire:click="update({{ $servicio }})">
                        <i class="fa-solid fa-calendar-days fa-lg"></i>
                        Agendar
                    </button>
                @endif
                @if (
                    $estado == 'Confirmado por el cliente. En espera de visita.' &&
                        \Carbon\Carbon::parse($fechaRealizacion)->subDays(3)->greaterThanOrEqualTo(now()))
                    <button class="bg-red-500 text-white rounded py-2 px-4 flex items-center gap-2 hover:bg-red-700"
                        wire:click="cancelarEvento({{ $servicio }})">
                        <i class="fa-solid fa-xmark fa-lg"></i>
                        Cancelar Servicio
                    </button>
                @endif
                <button wire:click="cerrar"
                    class="px-4 py-2 bg-gray-700 text-white font-bold rounded hover:bg-gray-800">
                    Cerrar
                </button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

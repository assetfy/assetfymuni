<div>
    <x-dialog-modal wire:model.live="open" maxWidth="custom"
        class="fixed inset-0 flex items-center justify-center bg-black/50">

        {{-- ===== HEADER SIEMPRE VISIBLE ===== --}}
        <x-slot name="title">
            @php
                $ordenId = $orden->id_orden ?? 'n/a';
                $estadoOrden = $orden->estado_orden ?? null;
                $tipoOrden = $orden->tipo_orden ?? 'N/A';
                $clienteNom = $nombreCliente ?? 'Sin Datos';
                $prestNom = $prestadora->razon_social ?? 'Sin Prestador';
                $tecnicoNom = data_get($tecnico, 'usuarios.name', 'Sin asignado');
                $fechaVisita = !empty($ordenProgramacion?->fecha_inicio)
                    ? date('Y-m-d', strtotime($ordenProgramacion->fecha_inicio))
                    : 'N/A';
                $slaHoras = $horario->sla_horas ?? 'N/A';
            @endphp

            {{-- sticky header --}}
            <div class="sticky top-0 z-30 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/75">
                <div class="bg-white shadow-sm rounded-t-xl border-x border-t ring-1 ring-gray-200 overflow-hidden">
                    <!-- Fila 1 -->
                    <div
                        class="p-4 flex flex-col md:flex-row md:justify-between md:items-start gap-3 bg-[#EFF6FC] text-[#2F2F2F]">
                        <!-- Columna Izquierda -->
                        <div class="space-y-1">
                            <div class="flex items-center text-lg font-bold">
                                <i class="fa-solid fa-building-user w-5 text-blue-800 mr-2"></i>
                                <span class="font-semibold text-blue-900 whitespace-nowrap">Cliente:</span>
                                <span class="ml-1 text-blue-900">{{ $clienteNom }}</span>
                            </div>

                            <div class="flex items-center">
                                <i class="fa-solid fa-handshake w-5 text-blue-800 mr-2"></i>
                                <span class="font-semibold text-blue-900 whitespace-nowrap">Prestadora:</span>
                                <span class="ml-1 text-blue-900">{{ $prestNom }}</span>
                            </div>

                            <div class="flex items-center min-w-0">
                                <i class="fa-solid fa-user-gear w-5 text-blue-800 mr-2"></i>
                                <span class="font-semibold text-blue-900 whitespace-nowrap">Téc.&nbsp;Asig.:</span>
                                <span class="ml-1 truncate text-blue-900">{{ $tecnicoNom }}</span>
                            </div>

                            <div class="flex items-center flex-nowrap">
                                <i class="fa-solid fa-calendar-day w-5 text-blue-800 mr-2"></i>
                                <span class="font-semibold text-blue-900 whitespace-nowrap">Fecha&nbsp;Visita:</span>
                                <span class="ml-1 whitespace-nowrap text-blue-900">{{ $fechaVisita }}</span>
                            </div>
                        </div>

                        <!-- Columna Derecha -->
                        <div class="text-left md:text-right space-y-2">
                            <div class="flex items-center justify-start md:justify-end text-xl font-bold text-blue-900">
                                <i class="fa-solid fa-ticket mr-2 text-blue-800"></i>
                                OT#{{ $ordenId }}
                                @if ($estadoOrden)
                                    <span
                                        class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs
                                        {{ $estadoOrden === 'Realizado'
                                            ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-200'
                                            : 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200' }}">
                                        <i class="fa-solid fa-circle text-[7px]"></i> {{ $estadoOrden }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center md:justify-end flex-wrap gap-2">
                                <span class="inline-flex items-center font-semibold text-blue-900 whitespace-nowrap">
                                    <i class="fa-solid fa-wrench mr-2 text-blue-800"></i>
                                    Tipo: {{ $tipoOrden }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Fila 2 -->
                    <div class="p-4 border-t bg-white text-sm text-[#2F2F2F]">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 font-medium">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-route text-gray-600 shrink-0"></i>
                                    <span>Ubicación:</span>
                                    <span class="text-gray-800">
                                        {{ ($ubicacion?->calle ?? 'N/A') . ($ubicacion?->altura ? ' ' . $ubicacion?->altura : '') }}{!! $ubicacion?->localidad ? ' – ' . e($ubicacion?->localidad) : '' !!}
                                    </span>
                                </span>

                                <span class="hidden md:inline-block mx-2 text-gray-300">|</span>

                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-gauge-high text-gray-600 shrink-0"></i>
                                    <span>SLA (hs):</span>
                                    <span class="text-gray-800">{{ $slaHoras }}</span>
                                </span>
                            </div>

                            <div class="inline-flex items-center gap-2 font-semibold text-indigo-700">
                                <i class="fa-solid fa-database"></i>
                                <span class="whitespace-nowrap">Datos de la Orden</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs (Solicitud siempre / Datos solo si está cerrada) --}}
                    <div class="px-3 py-2 border-t bg-[#EFF6FC]">
                        <div class="flex flex-wrap items-center gap-4">
                            <button wire:click="$set('activeTab', 'solicitud')"
                                class="relative pb-2 transition text-sm md:text-base
                                    {{ $activeTab === 'solicitud' ? 'text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                                <i class="fa-regular fa-message mr-1.5"></i> Solicitud Cliente
                                @if ($activeTab === 'solicitud')
                                    <span class="absolute left-0 -bottom-0.5 h-0.5 w-full bg-blue-600 rounded"></span>
                                @endif
                            </button>

                            @if (($orden->estado_orden ?? null) === 'Realizado' && !empty($cierreOrden))
                                <button wire:click="$set('activeTab', 'datos')"
                                    class="relative pb-2 transition text-sm md:text-base
                                        {{ $activeTab === 'datos' ? 'text-blue-700 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                                    <i class="fa-solid fa-clipboard-check mr-1.5"></i> Datos de la Orden
                                    @if ($activeTab === 'datos')
                                        <span
                                            class="absolute left-0 -bottom-0.5 h-0.5 w-full bg-blue-600 rounded"></span>
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- fin sticky header --}}
                <div class="border-x border-b rounded-b-xl ring-1 ring-gray-200"></div>
            </div>
        </x-slot>

        {{-- ===== CONTENT (SCROLL) ===== --}}
        <x-slot name="content">
            {{-- solo el cuerpo scrollea, el header queda fijo --}}
            <div class="max-h-[72vh] overflow-y-auto pr-1">
                @php
                    $bienes = $activosOrden ?? [];
                    $focoId = (int) ($activoSeleccionadoId ?? 0);
                    $bienFoco = null;
                    foreach ($bienes as $b) {
                        $idCmp = (int) ($b['id_ot_bien'] ?? ($b['id_activo'] ?? 0));
                        if ($idCmp === $focoId) {
                            $bienFoco = $b;
                            break;
                        }
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Lista de bienes --}}
                    <aside class="md:col-span-4">
                        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
                            <div class="p-3 border-b flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                    Bienes ({{ count($bienes) }})
                                </div>
                            </div>

                            <div class="p-2 max-h-[56vh] overflow-y-auto">
                                @if (empty($bienes))
                                    <div class="text-sm text-gray-500 p-3">No hay bienes asociados a la orden.</div>
                                @else
                                    <ul class="space-y-1">
                                        @foreach ($bienes as $b)
                                            @php
                                                $bid = (int) ($b['id_ot_bien'] ?? ($b['id_activo'] ?? 0));
                                                $isActive = $bid === $focoId;
                                                $label =
                                                    data_get($b, 'activos.identificador') ??
                                                    (data_get($b, 'activos.nombre') ?? 'Bien #' . $bid);
                                                $sub = trim(
                                                    (string) (data_get($b, 'tipo.nombre', '') ?: '') .
                                                        (data_get($b, 'categoria.nombre')
                                                            ? ' · ' . data_get($b, 'categoria.nombre')
                                                            : '') .
                                                        (data_get($b, 'subcategoria.nombre')
                                                            ? ' · ' . data_get($b, 'subcategoria.nombre')
                                                            : ''),
                                                );
                                            @endphp
                                            <li>
                                                <button type="button"
                                                    wire:click="setActivoSeleccionado({{ $bid }})"
                                                    class="w-full text-left px-3 py-2 rounded-lg border transition
                                                        {{ $isActive
                                                            ? 'bg-blue-600 text-white border-blue-600'
                                                            : 'bg-white text-gray-800 border-gray-200 hover:bg-gray-50' }}">
                                                    <div class="text-sm font-medium truncate">{{ $label }}</div>
                                                    @if ($sub !== '')
                                                        <div class="text-xs opacity-75 truncate">
                                                            {{ $sub }}
                                                        </div>
                                                    @endif
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </aside>

                    {{-- Detalle del bien + pestañas --}}
                    <section class="md:col-span-8 space-y-4">
                        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200">
                            <div class="p-3 border-b flex items-center justify-between">
                                <div class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                    <i class="fa-solid fa-cube"></i> Activo en foco
                                </div>
                                @if ($bienFoco)
                                    <span class="text-xs text-gray-500">
                                        ID interno:
                                        {{ $bienFoco['activos']['id_activo'] ?? ($bienFoco['id_activo'] ?? 'N/A') }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-4">
                                @if ($bienFoco)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                        <div><span class="text-gray-500">Nombre:</span> <span
                                                class="font-medium text-gray-900">{{ data_get($bienFoco, 'activos.nombre', 'N/A') }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Tipo:</span> <span
                                                class="font-medium text-gray-900">{{ data_get($bienFoco, 'tipo.nombre', 'N/A') }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Categoría:</span> <span
                                                class="font-medium text-gray-900">{{ data_get($bienFoco, 'categoria.nombre', 'N/A') }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Subcategoría:</span> <span
                                                class="font-medium text-gray-900">{{ data_get($bienFoco, 'subcategoria.nombre', 'N/A') }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-sm">
                                        <div class="text-gray-500 font-medium">Ubicación</div>
                                        <div class="text-gray-900">
                                            {{ ($ubicacion?->calle ?? 'N/A') . ($ubicacion?->altura ? ' ' . $ubicacion?->altura : '') }}{!! $ubicacion?->localidad ? ' – ' . e($ubicacion?->localidad) : '' !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="text-gray-500 text-sm">No hay activo seleccionado.</div>
                                @endif
                            </div>
                        </div>

                        {{-- Pestañas de contenido --}}
                        <div class="{{ $activeTab === 'solicitud' ? '' : 'hidden' }}">
                            <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4">
                                @include('livewire.servicios.ordenes-de-trabajo.partials.solicitud')
                            </div>
                        </div>

                        @if (($orden->estado_orden ?? null) === 'Realizado' && !empty($cierreOrden))
                            <div class="{{ $activeTab === 'datos' ? '' : 'hidden' }}">
                                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-4 space-y-4">
                                    {{-- resumen de cierre + adjuntos + firma (solo lectura) --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
                                        <div><span class="text-gray-500">Hora de llegada:</span> <span
                                                class="font-medium text-gray-900">{{ $cierreOrden->hora_llegada ?? 'N/A' }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Hora de retiro:</span> <span
                                                class="font-medium text-gray-900">{{ $cierreOrden->hora_retiro ?? 'N/A' }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Tiempo total:</span> <span
                                                class="font-medium text-gray-900">{{ $this->tiempoTotal }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Mano de obra:</span> <span
                                                class="font-medium text-gray-900">{{ $cierreOrden->mano_de_obra ?? 0 ? 'Sí' : 'No' }}</span>
                                        </div>
                                        <div><span class="text-gray-500">Incluye materiales:</span> <span
                                                class="font-medium text-gray-900">{{ $cierreOrden->incluye_materiales ?? 0 ? 'Sí' : 'No' }}</span>
                                        </div>
                                    </div>

                                    @if (!empty($cierreOrden->comentarios))
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800 mb-1">Comentarios</div>
                                            <div
                                                class="rounded-lg p-3 bg-gray-50 text-gray-800 leading-relaxed text-sm">
                                                {{ $cierreOrden->comentarios }}
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($trabajo))
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800 mb-2">Adjuntos del trabajo
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($trabajo as $ruta)
                                                    @php
                                                        $url = \Illuminate\Support\Facades\Storage::disk(
                                                            's3',
                                                        )->temporaryUrl($ruta, now()->addMinutes(10));
                                                    @endphp
                                                    <a href="{{ $url }}" target="_blank" class="block">
                                                        <img src="{{ $url }}" alt="Trabajo"
                                                            class="h-24 w-24 object-cover rounded-lg ring-1 ring-gray-200" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if (!empty($firma?->ruta_archivo))
                                        @php
                                            $firmaUrl = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl(
                                                $firma->ruta_archivo,
                                                now()->addMinutes(10),
                                            );
                                        @endphp
                                        <div>
                                            <div class="text-sm font-semibold text-gray-800 mb-2">Firma</div>
                                            <img src="{{ $firmaUrl }}" alt="Firma"
                                                class="h-24 object-contain bg-white p-2 rounded-lg ring-1 ring-gray-200" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </x-slot>

        {{-- ===== FOOTER ===== --}}
        <x-slot name="footer">
            <div class="flex flex-wrap justify-end gap-2">
                <button wire:click="$set('open', false)"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded border border-gray-300 text-gray-700 font-medium bg-white transition hover:bg-gray-50 hover:border-gray-400 shadow-sm text-sm">
                    <i class="fa-regular fa-circle-xmark"></i>
                    Cerrar
                </button>
            </div>
        </x-slot>

    </x-dialog-modal>
</div>

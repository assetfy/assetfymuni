<div class="w-full">
    {{-- Contenedor principal (más ancho en desktop) --}}
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6">
        {{-- Header --}}
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-indigo-600"></i>
                        Cerrar Orden #{{ $id_orden }}
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Estado:
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $orden->estado_orden === 'Pendiente' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                            <i class="fa-solid fa-circle"></i> {{ $orden->estado_orden }}
                        </span>
                        <span class="mx-2 opacity-50">•</span>
                        Creada:
                        <span class="font-medium">{{ \Carbon\Carbon::parse($orden->fecha)->format('d/m/Y') }}</span>
                    </p>
                </div>

                {{-- Representante técnico (auto) --}}
                <div class="text-left sm:text-right">
                    @if ($usuarioEsRepresentante && $nombre_representante)
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border bg-emerald-50 text-emerald-700">
                            <i class="fa-solid fa-user-shield"></i>
                            <span class="text-sm font-medium">Representante técnico:</span>
                            <span class="text-sm font-semibold">{{ $nombre_representante }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Se asignará automáticamente al cerrar.</p>
                    @else
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border bg-slate-50 text-slate-600">
                            <i class="fa-regular fa-user"></i>
                            <span class="text-sm">El usuario actual no es representante técnico.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Barra de opciones globales (modo / acción / fecha cierre) --}}
        <div class="bg-white border rounded-2xl shadow-sm p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="text-[13px] font-semibold text-slate-700">Modo de carga</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <label class="inline-flex items-center gap-2 border rounded-xl px-3 py-2.5 cursor-pointer">
                            <input type="radio" wire:model.live="modoAplicacion" value="global" class="h-4 w-4">
                            <span>Global (aplicar a todos los bienes)</span>
                        </label>
                        <label class="inline-flex items-center gap-2 border rounded-xl px-3 py-2.5 cursor-pointer"
                            @class(['opacity-50 pointer-events-none' => $accionPasiva])>
                            <input type="radio" wire:model.live="modoAplicacion" value="por_item" class="h-4 w-4"
                                @disabled($accionPasiva)>
                            <span>Por ítem (bien por bien)</span>
                        </label>
                    </div>
                    @if ($accionPasiva)
                        <p class="text-[11px] text-amber-700 mt-1">
                            <i class="fa-solid fa-info-circle"></i>
                            Acci&oacute;n pasiva activada: el formulario por &iacute;tem se oculta y se usar&aacute;
                            modo global.
                        </p>
                    @endif
                </div>

                <div>
                    <label class="text-[13px] font-semibold text-slate-700">Acción pasiva</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" wire:model.live="accionPasiva" class="h-4 w-4">
                            <span class="text-sm">Ocultar ítems y cargar solo global</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="text-[13px] font-semibold text-slate-700">Fecha de cierre</label>
                    <div class="mt-1 flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-slate-400"></i>
                        <input type="date" wire:model="fecha_cierre"
                            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                    </div>
                    @error('fecha_cierre')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- BLOQUE GLOBAL (servicios + materiales + adjuntos) --}}
        <div class="bg-white border rounded-2xl shadow-sm p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Servicios globales --}}
                <div>
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-slate-500"></i> Servicios (global)
                    </h3>
                    @if ($tieneCatalogo && !empty($serviciosDisponibles))
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($serviciosDisponibles as $s)
                                @php $checked = in_array($s['id_servicio'], $serviciosSeleccionGlobal, true); @endphp
                                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 cursor-pointer">
                                    <input type="checkbox" @checked($checked)
                                        wire:click="toggleServicioGlobal({{ $s['id_servicio'] }})" class="h-4 w-4">
                                    <span class="text-sm">{{ $s['nombre'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500 mt-2">No hay catálogo de servicios disponible
                            (contrato/actividades).</p>
                    @endif
                </div>

                {{-- Materiales globales --}}
                <div>
                    <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-screwdriver-wrench text-slate-500"></i> Materiales (global)
                    </h3>

                    {{-- Buscador rápido --}}
                    <div class="mt-3 flex items-center gap-2">
                        <div class="flex items-center gap-2 border rounded-lg px-2 py-1 bg-white w-full">
                            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                            <input type="text" placeholder="Buscar por nombre o código…"
                                class="border-0 focus:ring-0 text-sm w-full"
                                wire:keydown.debounce.500ms="buscarMaterial($event.target.value)">
                        </div>
                        <button type="button" class="text-xs px-3 py-2 rounded-lg border hover:bg-slate-50"
                            wire:click="$set('modoCrearMaterial', true)">
                            <i class="fa-solid fa-plus mr-1"></i> Nuevo
                        </button>
                    </div>

                    @if (!empty($materialesBusqueda['results']))
                        <div class="mt-2 border rounded-lg p-2 bg-slate-50">
                            <ul class="divide-y text-sm">
                                @foreach ($materialesBusqueda['results'] as $m)
                                    <li class="py-1.5 flex items-center justify-between">
                                        <div class="truncate">
                                            <span class="font-medium">{{ $m['nombre'] }}</span>
                                            <span class="text-slate-500"> ·
                                                {{ $m['codigo_interno'] ?? 's/código' }}</span>
                                        </div>
                                        <button type="button" class="text-xs px-2 py-1 rounded border hover:bg-white"
                                            wire:click="agregarMaterialGlobal({{ $m['id_material'] }})">
                                            Agregar
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Selección global actual --}}
                    @if (!empty($materialesSeleccionGlobal))
                        <div class="mt-3 border rounded-lg p-2 bg-white">
                            <ul class="divide-y text-sm">
                                @foreach ($materialesSeleccionGlobal as $idx => $mm)
                                    <li class="py-1.5 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs">
                                                ID {{ $mm['id_material'] }}
                                            </span>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-slate-500">Cant.</span>
                                                <input type="number" min="0" step="0.01"
                                                    class="w-24 border rounded px-2 py-1 text-sm"
                                                    wire:model.lazy="materialesSeleccionGlobal.{{ $idx }}.cantidad">
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="text-xs px-2 py-1 rounded border hover:bg-slate-50"
                                            wire:click="quitarMaterialGlobal({{ $idx }})">
                                            Quitar
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Crear material rápido --}}
                    @if ($modoCrearMaterial)
                        <div class="mt-3 rounded-lg border p-3 bg-white">
                            <h5 class="font-semibold text-slate-700 mb-2">Nuevo material</h5>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="text-xs text-slate-600">Código interno</label>
                                    <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                        wire:model="materialNuevo.codigo_interno">
                                    <x-input-error for="materialNuevo.codigo_interno" class="text-xs" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs text-slate-600">Nombre</label>
                                    <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                        wire:model="materialNuevo.nombre">
                                    <x-input-error for="materialNuevo.nombre" class="text-xs" />
                                </div>
                                <div>
                                    <label class="text-xs text-slate-600">Unidad</label>
                                    <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                        wire:model="materialNuevo.unidad">
                                    <x-input-error for="materialNuevo.unidad" class="text-xs" />
                                </div>
                                <div class="md:col-span-4">
                                    <label class="text-xs text-slate-600">Descripción</label>
                                    <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                        wire:model="materialNuevo.descripcion">
                                    <x-input-error for="materialNuevo.descripcion" class="text-xs" />
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <button type="button"
                                    class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700"
                                    wire:click="crearMaterial">
                                    <i class="fa-solid fa-floppy-disk mr-1"></i> Guardar
                                </button>
                                <button type="button" class="px-3 py-2 rounded-lg border text-sm hover:bg-slate-50"
                                    wire:click="$set('modoCrearMaterial', false)">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Adjuntos globales --}}
            <div class="mt-6 rounded-xl border p-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-paperclip text-slate-500"></i> Adjuntos globales (cierre)
                    </h4>
                    <label for="filesGlobal"
                        class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer">
                        <i class="fa-solid fa-upload"></i> Subir
                    </label>
                    <input id="filesGlobal" type="file" multiple wire:model="adjuntosGlobalesNuevos"
                        class="hidden" />
                </div>

                @if (!empty($adjuntosGlobalesNuevos))
                    <ul class="mt-3 divide-y text-sm">
                        @foreach ($adjuntosGlobalesNuevos as $i => $f)
                            <li class="py-2 flex items-center justify-between">
                                <span class="truncate">{{ $f->getClientOriginalName() }}</span>
                                <button type="button" wire:click="quitarAdjuntoGlobalNuevo({{ $i }})"
                                    class="text-red-600 hover:text-red-700 text-xs font-semibold">
                                    <i class="fa-solid fa-xmark mr-1"></i>Quitar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if (!empty($adjuntosGlobales))
                    <p class="text-xs text-slate-500 mt-3">Existentes</p>
                    <ul class="mt-1 divide-y text-sm">
                        @foreach ($adjuntosGlobales as $a)
                            <li class="py-2 flex items-center justify-between">
                                <span class="truncate">{{ $a['nombre'] }}</span>
                                <a href="{{ Storage::disk('s3')->url($a['ruta']) }}" target="_blank"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:underline text-xs">
                                    <i class="fa-regular fa-eye"></i> Ver
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- ÍTEMS (solo si NO hay acción pasiva y modo por ítem) --}}
        @if (!$accionPasiva && $modoAplicacion === 'por_item')
            <div class="space-y-6">
                @forelse ($bienes as $b)
                    @php $idb = $b['id_orden_bien']; @endphp
                    <div class="bg-white border rounded-2xl shadow-sm p-4 sm:p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-semibold text-slate-800 flex items-center gap-2">
                                    <i class="fa-solid fa-cube text-slate-500"></i>
                                    Bien #{{ $b['id_activo'] }} — {{ $b['nombre_activo'] }}
                                </h3>
                                <p class="text-xs text-slate-500">ID Orden-Bien: {{ $idb }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="text-[13px] font-semibold text-slate-700">Estado</label>
                                <input type="text" wire:model="resumenBien.{{ $idb }}.estado"
                                    class="mt-1 w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                    placeholder="Ej: Reparado / Reemplazado / Sin reparación">
                                @error("resumenBien.$idb.estado")
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="text-[13px] font-semibold text-slate-700">Resolución</label>
                                <textarea rows="2" wire:model="resumenBien.{{ $idb }}.resolucion"
                                    class="mt-1 w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                    placeholder="Detalle técnico de la resolución"></textarea>
                                @error("resumenBien.$idb.resolucion")
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Servicios por ítem --}}
                        <div class="mt-4">
                            <h4 class="font-semibold text-slate-700 flex items-center gap-2">
                                <i class="fa-solid fa-list-check text-slate-500"></i> Servicios del bien
                            </h4>
                            @if ($tieneCatalogo && !empty($serviciosDisponibles))
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach ($serviciosDisponibles as $s)
                                        @php $checked = in_array($s['id_servicio'], $serviciosSeleccion[$idb] ?? [], true); @endphp
                                        <label
                                            class="flex items-center gap-2 border rounded-lg px-3 py-2 cursor-pointer">
                                            <input type="checkbox" @checked($checked)
                                                wire:click="toggleServicioItem({{ $idb }}, {{ $s['id_servicio'] }})"
                                                class="h-4 w-4">
                                            <span class="text-sm">{{ $s['nombre'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-500 mt-2">Sin catálogo disponible.</p>
                            @endif
                        </div>

                        {{-- Materiales por ítem --}}
                        <div class="rounded-xl border p-4 mt-4 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <h4 class="font-semibold text-slate-700 flex items-center gap-2">
                                    <i class="fa-solid fa-screwdriver-wrench text-slate-500"></i> Materiales usados
                                </h4>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-2 border rounded-lg px-2 py-1 bg-white">
                                        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                        <input type="text" placeholder="Buscar por nombre o código…"
                                            class="border-0 focus:ring-0 text-sm"
                                            wire:keydown.debounce.500ms="buscarMaterial($event.target.value)">
                                    </div>
                                    <button type="button"
                                        class="text-xs px-3 py-2 rounded-lg border hover:bg-slate-50"
                                        wire:click="$set('modoCrearMaterial', true)">
                                        <i class="fa-solid fa-plus mr-1"></i> Nuevo material
                                    </button>
                                </div>
                            </div>

                            @if (!empty($materialesBusqueda['results']))
                                <div class="border rounded-lg p-2 bg-slate-50">
                                    <ul class="divide-y text-sm">
                                        @foreach ($materialesBusqueda['results'] as $m)
                                            <li class="py-1.5 flex items-center justify-between">
                                                <div class="truncate">
                                                    <span class="font-medium">{{ $m['nombre'] }}</span>
                                                    <span class="text-slate-500"> ·
                                                        {{ $m['codigo_interno'] ?? 's/código' }}</span>
                                                </div>
                                                <button type="button"
                                                    class="text-xs px-2 py-1 rounded border hover:bg-white"
                                                    wire:click="agregarMaterialA({{ $idb }}, {{ $m['id_material'] }})">
                                                    Agregar
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty($materialesSeleccion[$idb] ?? []))
                                <div class="border rounded-lg p-2 bg-white">
                                    <ul class="divide-y text-sm">
                                        @foreach ($materialesSeleccion[$idb] as $idx => $mm)
                                            <li class="py-1.5 flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-xs">
                                                        ID {{ $mm['id_material'] }}
                                                    </span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-slate-500">Cant.</span>
                                                        <input type="number" min="0" step="0.01"
                                                            class="w-24 border rounded px-2 py-1 text-sm"
                                                            wire:model.lazy="materialesSeleccion.{{ $idb }}.{{ $idx }}.cantidad">
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="text-xs px-2 py-1 rounded border hover:bg-slate-50"
                                                    wire:click="quitarMaterialDe({{ $idb }}, {{ $idx }})">
                                                    Quitar
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Crear material inline (compartido) --}}
                            @if ($modoCrearMaterial)
                                <div class="rounded-lg border p-3 bg-white">
                                    <h5 class="font-semibold text-slate-700 mb-2">Nuevo material</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div>
                                            <label class="text-xs text-slate-600">Código interno</label>
                                            <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                                wire:model="materialNuevo.codigo_interno">
                                            <x-input-error for="materialNuevo.codigo_interno" class="text-xs" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="text-xs text-slate-600">Nombre</label>
                                            <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                                wire:model="materialNuevo.nombre">
                                            <x-input-error for="materialNuevo.nombre" class="text-xs" />
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-600">Unidad</label>
                                            <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                                wire:model="materialNuevo.unidad">
                                            <x-input-error for="materialNuevo.unidad" class="text-xs" />
                                        </div>
                                        <div class="md:col-span-4">
                                            <label class="text-xs text-slate-600">Descripción</label>
                                            <input type="text" class="w-full border rounded px-2 py-1 text-sm"
                                                wire:model="materialNuevo.descripcion">
                                            <x-input-error for="materialNuevo.descripcion" class="text-xs" />
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button"
                                            class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700"
                                            wire:click="crearMaterial">
                                            <i class="fa-solid fa-floppy-disk mr-1"></i> Guardar
                                        </button>
                                        <button type="button"
                                            class="px-3 py-2 rounded-lg border text-sm hover:bg-slate-50"
                                            wire:click="$set('modoCrearMaterial', false)">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Adjuntos por bien --}}
                        <div class="rounded-xl border p-4 mt-4">
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-slate-700 flex items-center gap-2">
                                    <i class="fa-solid fa-paperclip text-slate-500"></i> Adjuntos del bien
                                </h4>
                                <label for="filesBien-{{ $idb }}"
                                    class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer">
                                    <i class="fa-solid fa-upload"></i> Subir
                                </label>
                                <input id="filesBien-{{ $idb }}" type="file" multiple
                                    wire:model="adjuntosBienNuevos.{{ $idb }}" class="hidden" />
                            </div>

                            @if (!empty($adjuntosBienNuevos[$idb] ?? []))
                                <ul class="mt-3 divide-y text-sm">
                                    @foreach ($adjuntosBienNuevos[$idb] as $i => $f)
                                        <li class="py-2 flex items-center justify-between">
                                            <span class="truncate">{{ $f->getClientOriginalName() }}</span>
                                            <button type="button"
                                                wire:click="quitarAdjuntoBienNuevo({{ $idb }}, {{ $i }})"
                                                class="text-red-600 hover:text-red-700 text-xs font-semibold">
                                                <i class="fa-solid fa-xmark mr-1"></i>Quitar
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (!empty($adjuntosBienListado[$idb] ?? []))
                                <p class="text-xs text-slate-500 mt-3">Existentes</p>
                                <ul class="mt-1 divide-y text-sm">
                                    @foreach ($adjuntosBienListado[$idb] as $a)
                                        <li class="py-2 flex items-center justify-between">
                                            <span class="truncate">{{ $a['nombre'] }}</span>
                                            <a href="{{ Storage::disk('s3')->url($a['ruta']) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-indigo-600 hover:underline text-xs">
                                                <i class="fa-regular fa-eye"></i> Ver
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4 text-sm text-yellow-800">
                        La orden no tiene bienes asociados.
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-8 flex justify-end">
            <button type="button"
                class="px-4 py-2 rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 inline-flex items-center gap-2"
                wire:click="cerrarOrden">
                <i class="fa-solid fa-lock"></i> Cerrar orden
            </button>
        </div>
    </div>
</div>

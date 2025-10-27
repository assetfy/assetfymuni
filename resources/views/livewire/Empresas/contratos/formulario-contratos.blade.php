<div class="w-full max-w-none px-2 sm:px-4 lg:px-6 py-6">
    <h1 class="text-3xl font-semibold text-center mb-2"><i
            class="fa-solid fa-file-contract mr-2 text-black"></i>Formulario de Carga de Contrato</h1>
    <p class="text-center text-gray-500 mb-6">Complete los campos requeridos para registrar un nuevo contrato</p>

    {{-- Tabs --}}
    @php $esTabPrincipal = $activeTab === 'principal'; @endphp
    <div class="mb-6 flex justify-center">
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden shadow-sm ring-1 ring-gray-200">
            <button type="button" wire:click="goTab('principal')"
                class="px-4 py-2 text-sm font-medium transition-all duration-200 {{ $esTabPrincipal ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                <i class="fa-solid fa-file-lines mr-1"></i> Datos del Contrato
            </button>
            <button type="button" wire:click="goTab('servicios')"
                class="px-4 py-2 text-sm font-medium transition-all duration-200 {{ !$esTabPrincipal ? 'bg-black text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                <i class="fa-solid fa-toolbox mr-1"></i> Servicios
            </button>
        </div>
    </div>

    @if ($activeTab === 'principal')
        <form wire:submit.prevent="enviar" class="space-y-8">

            {{-- Información Básica --}}
            <section class="bg-white rounded-xl p-6 space-y-6 border border-gray-200 shadow">
                <h2 class="text-lg font-semibold"><i class="fa-solid fa-circle-info mr-2 text-gray-700"></i>Información
                    Básica</h2>

                <div>
                    <label class="block text-sm font-medium mb-1">Nombre del Contrato *</label>
                    <input type="text" wire:model.defer="form.nombre"
                        class="w-full rounded-lg border-gray-300 shadow-sm" placeholder="Ej: Contrato de Mantenimiento">
                    @error('form.nombre')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo de Solicitud (selección única) --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Tipo de Contrato * <span class="text-gray-400 text-xs">(Seleccione uno)</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($tipoContratos ?? collect() as $t)
                            @php
                                $val = $t->id_tipo_contrato ?? (method_exists($t, 'getKey') ? $t->getKey() : null);
                                $rid = 'tipo_contrato_' . $val;
                            @endphp
                            <label for="{{ $rid }}"
                                class="flex items-center gap-2 border rounded-lg p-3 hover:bg-gray-50 transition">
                                <input id="{{ $rid }}" type="radio" name="id_tipo_contrato"
                                    value="{{ $val }}" wire:model="form.id_tipo_contrato"
                                    class="rounded text-black">
                                <span>{{ $t->nombre }}</span>
                            </label>
                        @endforeach

                        @if (($tipoContratos ?? collect())->isEmpty())
                            <p class="text-sm text-gray-500 col-span-full">No hay tipos de contrato.</p>
                        @endif
                    </div>

                    @error('form.id_tipo_contrato')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Estado del Contrato *</label>
                    <select wire:model.live="estadoContrato" class="w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">Seleccione el estado</option>
                        @foreach ($estadosContratos ?? collect() as $e)
                            <option value="{{ $e->id_estado_contrato }}">{{ $e->nombre_estado }}</option>
                        @endforeach
                    </select>
                    @error('estadoContrato')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- Partes del Contrato --}}
            <section class="bg-white rounded-xl p-6 space-y-6 border border-gray-200 shadow">
                <h2 class="text-lg font-semibold"><i class="fa-solid fa-user-group mr-2 text-gray-700"></i>Partes del
                    Contrato</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Número de Contrato *</label>
                        <input type="text" wire:model.defer="form.nro_contrato" readonly
                            class="w-full rounded-lg border-gray-300 shadow-sm bg-gray-100 cursor-not-allowed"
                            placeholder="Se genera automáticamente (CTR-AAAA-###)" aria-readonly="true">
                        @error('form.nro_contrato')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Prestador *</label>
                        <select wire:model.live="cuitPrestador" class="w-full rounded-lg border-gray-300 shadow-sm">
                            <option value="">Seleccione el prestador</option>
                            @foreach ($proveedores ?? collect() as $p)
                                <option value="{{ $p->cuit }}">{{ $p->razon_social ?? $p->cuit }}</option>
                            @endforeach
                        </select>
                        @error('cuitPrestador')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha de Inicio *</label>
                        <input type="date" wire:model.defer="form.fecha_inicio"
                            class="w-full rounded-lg border-gray-300 shadow-sm">
                        @error('form.fecha_inicio')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fecha de Fin *</label>
                        <input type="date" wire:model.defer="form.fecha_fin"
                            class="w-full rounded-lg border-gray-300 shadow-sm">
                        @error('form.fecha_fin')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Información Económica --}}
            <section class="bg-white rounded-xl p-6 space-y-6 border border-gray-200 shadow">
                <h2 class="text-lg font-semibold"><i class="fa-solid fa-dollar-sign mr-2 text-gray-700"></i>Información
                    Económica</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Monto del Contrato</label>
                        <input type="number" step="0.01" wire:model.defer="form.monto"
                            class="w-full rounded-lg border-gray-300 shadow-sm" placeholder="0.00">
                        @error('form.monto')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Moneda</label>
                        <select wire:model.defer="form.moneda" class="w-full rounded-lg border-gray-300 shadow-sm">
                            <option value="">Seleccione moneda</option>
                            <option value="ARS">ARS</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                        @error('form.moneda')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Aplicación del Contrato --}}
            <section class="bg-white rounded-xl p-6 space-y-6 border border-gray-200 shadow">
                <h2 class="text-lg font-semibold"><i
                        class="fa-solid fa-map-location-dot mr-2 text-gray-700"></i>Aplicación del Contrato</h2>
                <p class="text-sm text-gray-500 -mt-4 mb-2">Ubicaciones y bienes incluidos en el contrato</p>

                <div>
                    <label class="block text-sm font-medium mb-2">Ubicación de Bienes </label>
                    <div class="max-h-64 overflow-y-auto border rounded-lg p-2 space-y-2 bg-gray-50">
                        @foreach ($ubicaciones ?? collect() as $u)
                            @php
                                $uid = $u->id_ubicacion ?? ($u->id ?? null);
                                $label =
                                    $u->direccion_completa ??
                                    ((trim(($u->calle ?? '') . ' ' . ($u->numero ?? '')) ?: $u->nombre ?? null) ??
                                        'Ubicación #' . $uid);
                                $label = trim($label . ' ' . ($u->localidad ?? ''));
                                $selected = in_array($uid, (array) ($form['ubicaciones'] ?? []), true);
                            @endphp
                            <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white transition">
                                {{-- Radio para selección única, pero seguimos guardando en form.ubicaciones (array con un solo valor) --}}
                                <input type="radio" name="ubicacion_unica" value="{{ $uid }}"
                                    @checked($selected) wire:click="setUbicacionUnica({{ (int) $uid }})"
                                    class="rounded text-black">
                                <span class="text-sm font-medium">{{ $label }}</span>
                            </label>
                        @endforeach

                        @if (($ubicaciones ?? collect())->isEmpty())
                            <p class="text-sm text-gray-500">No hay ubicaciones disponibles.</p>
                        @endif
                    </div>
                    @error('form.ubicaciones')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Bienes</label>
                    <select wire:model.live="TipoBien"
                        class="w-full rounded-xl border border-gray-300 bg-gray-50 shadow-sm">
                        <option value="">Seleccione el tipo de bien</option>
                        @foreach ($tiposActivos ?? collect() as $t)
                            <option value="{{ $t->id_tipo }}">{{ $t->nombre ?? 'Tipo #' . $t->id_tipo }}</option>
                        @endforeach
                    </select>
                    @error('TipoBien')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </section>
            {{-- ===================== CATEGORÍAS (múltiple con checkboxes) ===================== --}}
            @if (!empty($categorias))
                <section class="bg-white rounded-xl p-6 space-y-4 border border-gray-200 shadow">
                    <h2 class="text-lg font-semibold">Categorias</h2>

                    <label class="block text-sm font-medium mb-2">Categorías (selección múltiple)</label>

                    <div class="max-h-64 overflow-y-auto border rounded-lg p-3 bg-gray-50 space-y-2">
                        @foreach ($categorias as $c)
                            <label class="flex items-start gap-2 p-2 rounded hover:bg-white transition">
                                <input type="checkbox" class="mt-1 rounded" value="{{ $c['id_categoria'] }}"
                                    wire:model.live="categoriasSel" />
                                <span class="text-sm font-medium">{{ $c['nombre'] }}</span>
                            </label>
                        @endforeach
                    </div>

                    @error('categoriasSel')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Chips con lo seleccionado --}}
                    @php $catsById = collect($categorias)->keyBy('id_categoria'); @endphp
                    @if (!empty($categoriasSel))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($categoriasSel as $cid)
                                <span class="px-2 py-1 rounded-full text-xs bg-black text-white">
                                    {{ data_get($catsById, "$cid.nombre", "Cat #$cid") }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </section>
            @else
                {{-- SIN CATEGORÍA --}}
                <section class="bg-white rounded-xl p-6 space-y-2 border border-gray-200 shadow">
                    <h2 class="text-lg font-semibold">Categorias</h2>
                    <p class="text-sm text-gray-500">Sin categoría (no hay categorías disponibles para el tipo
                        seleccionado).</p>
                </section>
            @endif


            {{-- ===================== SUBCATEGORÍAS (múltiple con checkboxes) ===================== --}}
            @if (!empty($categoriasSel))
                <section class="bg-white rounded-xl p-6 space-y-4 border border-gray-200 shadow mt-4">
                    <h2 class="text-lg font-semibold">Subcategorías</h2>

                    <label class="block text-sm font-medium mb-2">Subcategorías (selección múltiple)</label>

                    <div class="max-h-72 overflow-y-auto border rounded-lg p-3 bg-gray-50">
                        <div class="grid grid-cols-1 gap-2">
                            @forelse ($subcategorias as $s)
                                @php $id = 'subcat_'.$s['id_subcategoria']; @endphp
                                <div class="flex items-start gap-2 p-2 rounded hover:bg-white transition"
                                    wire:key="subcat-{{ $s['id_subcategoria'] }}">
                                    <input id="{{ $id }}" type="checkbox" class="mt-1 rounded"
                                        value="{{ $s['id_subcategoria'] }}" wire:model.live="subcategoriasSel">
                                    <label for="{{ $id }}"
                                        class="text-sm font-medium">{{ $s['nombre'] }}</label>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500">Sin subcategoría (no hay subcategorías para las
                                    categorías seleccionadas).</p>
                            @endforelse
                        </div>
                    </div>

                    @error('subcategoriasSel')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    {{-- Chips --}}
                    @php $subsById = collect($subcategorias)->keyBy('id_subcategoria'); @endphp
                    @if (!empty($subcategoriasSel))
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($subcategoriasSel as $sid)
                                <span class="px-2 py-1 rounded-full text-xs bg-gray-800 text-white"
                                    wire:key="subcat-chip-{{ $sid }}">
                                    {{ data_get($subsById, "$sid.nombre", "Sub #$sid") }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </section>
            @else
                {{-- SIN SUBCATEGORÍA (porque no hay categorías seleccionadas) --}}
                <section class="bg-white rounded-xl p-6 space-y-2 border border-gray-200 shadow mt-4">
                    <h2 class="text-lg font-semibold">Subcategorías</h2>
                    <p class="text-sm text-gray-500">Sin subcategoría (seleccione primero una o más categorías).</p>
                </section>
            @endif
            {{-- Enviar --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-black text-white px-5 py-2.5 rounded-xl shadow hover:bg-gray-800 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                    Enviar Contrato
                </button>
            </div>
        </form>
    @else
        {{-- Pestaña Servicios --}}
        <div class="space-y-6">
            <section class="bg-white rounded-xl p-6 space-y-6 border border-gray-200">
                <h2 class="text-lg font-semibold">Servicios del Contrato</h2>

                @if (collect($servicios)->count() > 0)
                    <p class="text-sm text-gray-500">Seleccione los servicios; debajo aparecerán los materiales de cada
                        servicio elegido.</p>

                    {{-- 1) Selección de servicios --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach (collect($servicios) as $s)
                            @php $sid = $s->id_servicio ?? $s['id_servicio']; @endphp
                            <label class="flex items-start gap-2 border rounded-lg p-3">
                                <input type="checkbox" class="mt-1 rounded" value="{{ $sid }}"
                                    wire:model="form.servicios" wire:change="onServiciosChange" />
                                <div class="flex-1">
                                    <div class="font-medium">
                                        {{ $s->nombre ?? ($s['nombre'] ?? 'Servicio #' . $sid) }}
                                    </div>
                                    @php $desc = $s->descripcion ?? ($s['descripcion'] ?? null); @endphp
                                    @if ($desc)
                                        <div class="text-xs text-gray-500 mt-1">{{ $desc }}</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('form.servicios')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    {{-- 2) Materiales por cada servicio seleccionado --}}
                    @php
                        $serviciosSeleccionados = collect($form['servicios'] ?? []);
                        $serviciosPorId = collect($servicios)->keyBy('id_servicio');
                    @endphp

                    @foreach ($serviciosSeleccionados as $sid)
                        @php
                            $servicioActual = $serviciosPorId->get($sid);
                            $materialesDeEsteServicio = $materialesVisiblesPorServicio[$sid] ?? [];
                        @endphp

                        <div class="border rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold">
                                    Materiales para: {{ $servicioActual->nombre ?? 'Servicio #' . $sid }}
                                </h3>
                                <span class="text-xs text-gray-500">Servicio ID: {{ $sid }}</span>
                            </div>

                            @if (empty($materialesDeEsteServicio))
                                <p class="text-sm text-gray-500">Este servicio no tiene materiales predefinidos.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($materialesDeEsteServicio as $mat)
                                        @php
                                            $mid = $mat['id_material'];
                                            $base = "form.materiales.$sid.$mid";
                                        @endphp
                                        <div class="flex items-start gap-3 border rounded-lg p-3">
                                            <div class="pt-1">
                                                <input type="checkbox" class="rounded"
                                                    wire:model="{{ $base }}.selected">
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium">
                                                    {{ $mat['nombre'] }}
                                                    @if (!empty($mat['codigo_interno']))
                                                        <span
                                                            class="text-xs text-gray-500">({{ $mat['codigo_interno'] }})</span>
                                                    @endif
                                                </div>
                                                @if (!empty($mat['descripcion']))
                                                    <div class="text-xs text-gray-500">{{ $mat['descripcion'] }}</div>
                                                @endif
                                                <div class="mt-2 flex items-center gap-2">
                                                    <input type="number" min="0.01" step="0.01"
                                                        placeholder="Cantidad" class="w-36 rounded-lg border-gray-300"
                                                        wire:model.lazy="{{ $base }}.cantidad">
                                                    <span
                                                        class="text-sm text-gray-600">{{ $mat['unidad'] ?? 'un' }}</span>
                                                </div>
                                                @error("form.materiales.$sid.$mid.cantidad")
                                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500">No hay servicios disponibles para el prestador seleccionado.
                        Seleccione un prestador en la pestaña <strong>Datos del Contrato</strong>.</p>
                @endif

                <div class="pt-2">
                    <button type="button" wire:click="goTab('principal')"
                        class="inline-flex items-center gap-2 border border-gray-300 px-5 py-2.5 rounded-xl">
                        ← Volver
                    </button>
                </div>
            </section>
        </div>
    @endif
</div>

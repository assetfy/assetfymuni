<div class="w-full min-h-screen bg-gradient-to-b from-slate-50 to-white text-slate-800">
    <!-- Topbar / Breadcrumb + Título -->
    <header class="sticky top-0 z-30 border-b bg-white/90 backdrop-blur">
        <div class="max-w-[1680px] mx-auto px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <nav class="text-sm text-slate-500">
                    <ol class="flex items-center gap-2">
                        <li>Órdenes</li>
                        <li class="opacity-60">/</li>
                        <li class="text-slate-700 font-medium">Nueva orden</li>
                    </ol>
                </nav>
                <span
                    class="inline-flex items-center gap-2 text-xs font-medium px-2.5 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                    </svg>
                    Gestión → Proveedor
                </span>
            </div>

            <div class="mt-3 flex items-start justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-slate-900">Nueva Orden de Trabajo
                    </h1>
                    <p class="text-slate-500 text-sm mt-1">Cree una solicitud clara con SLA, adjuntos y trazabilidad por
                        empresa/contrato/activo.</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenido -->
    <main class="max-w-[1680px] mx-auto px-4 lg:px-6 py-8">
        <!-- Stepper -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
            <section class="xl:col-span-10 space-y-6">
                <div class="bg-white border rounded-2xl shadow-sm p-4 md:p-6">
                    <ol class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <li class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full border bg-blue-600 text-white font-semibold">1</span>
                            <div>
                                <p class="font-medium">Contexto</p>
                                <p class="text-xs text-slate-500">Empresa · Contrato</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full border bg-slate-100 text-slate-700 font-semibold">2</span>
                            <div>
                                <p class="font-medium">Activo</p>
                                <p class="text-xs text-slate-500">Categoría · Selección</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full border bg-slate-100 text-slate-700 font-semibold">3</span>
                            <div>
                                <p class="font-medium">Servicio</p>
                                <p class="text-xs text-slate-500">Tipo · SLA · Detalle</p>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- BLOQUE 1: Empresa + Contrato -->
                <div class="bg-white border rounded-2xl shadow-sm p-4 md:p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Empresa -->
                        <div class="space-y-2">
                            <label class="text-[13px] font-semibold text-slate-600">Proveedor</label>
                            <div x-data="{
                                open: false,
                                style: '',
                                toggle() {
                                    this.open = !this.open;
                                    if (this.open) { this.$nextTick(() => this.place()) }
                                },
                                place() {
                                    const r = this.$refs.btn.getBoundingClientRect();
                                    const top = r.bottom + window.scrollY;
                                    const left = r.left + window.scrollX;
                                    this.style = `top:${top}px;left:${left}px;width:${r.width}px;`;
                                }
                            }" class="relative">
                                <button type="button" x-ref="btn" @click="toggle()"
                                    class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-left focus:outline-none focus:ring-2 focus:ring-blue-500/30 hover:border-blue-400 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="truncate text-slate-700">
                                            @if ($cuit && $empresaBusqueda)
                                                {{ $empresaBusqueda->razon_social }}
                                            @else
                                                <span class="text-slate-400">Seleccione un proveedor</span>
                                            @endif
                                        </span>
                                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" />
                                        </svg>
                                    </div>
                                </button>

                                <!-- El panel se teleporta al body y queda por encima de todo -->
                                <template x-teleport="body">
                                    <div x-show="open" x-transition
                                        class="fixed z-[9999] rounded-xl bg-white shadow-lg border"
                                        :style="style" @click.outside="open=false">
                                        <div class="p-2 border-b">
                                            <input type="text" wire:model.live="searchEmpresa"
                                                @keydown.enter="open=false" @keydown.escape="open=false"
                                                class="w-full border border-blue-300/60 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                placeholder="Buscar Empresa…">
                                        </div>

                                        <ul class="max-h-64 overflow-auto py-2 text-sm">
                                            @forelse(($filteredEmpresas ?? collect()) as $empresa)
                                                <li wire:click="setCuitEmpresa({{ $empresa->cuit }})"
                                                    @click="open=false; @this.set('searchEmpresa', '');"
                                                    class="cursor-pointer px-3 py-2 hover:bg-blue-50">
                                                    <span class="block truncate">{{ $empresa->razon_social }}</span>
                                                    <span class="text-[10px] text-slate-400">CUIT:
                                                        {{ $empresa->cuit }}</span>
                                                </li>
                                            @empty
                                                <li class="px-3 py-4 text-center text-slate-400">Sin resultado</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </template>
                            </div>
                            @error('cuit')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contrato (condicional) -->
                        <div class="space-y-2">
                            <label class="text-[13px] font-semibold text-slate-600">Contrato</label>

                            @if (($contratosEmpresa ?? collect())->isNotEmpty())
                                <div x-data="{
                                    open: false,
                                    style: '',
                                    toggle() {
                                        this.open = !this.open;
                                        if (this.open) { this.$nextTick(() => this.place()) }
                                    },
                                    place() {
                                        const r = this.$refs.btnContrato.getBoundingClientRect();
                                        const top = r.bottom + window.scrollY;
                                        const left = r.left + window.scrollX;
                                        this.style = `top:${top}px;left:${left}px;width:${r.width}px;`;
                                    }
                                }" class="relative">
                                    <button type="button" x-ref="btnContrato" @click="toggle()"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-left focus:outline-none focus:ring-2 focus:ring-blue-500/30 hover:border-blue-400 transition">
                                        <div class="flex items-center justify-between">
                                            <span class="truncate text-slate-700">
                                                @if ($id_contrato && $contratoSeleccionado)
                                                    #{{ $contratoSeleccionado->nro_contrato }} —
                                                    {{ $contratoSeleccionado->nombre }}
                                                @else
                                                    <span class="text-slate-400">Seleccione un Contrato</span>
                                                @endif
                                            </span>
                                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" />
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- Panel teletransportado al <body> para evitar stacking/overflow -->
                                    <template x-teleport="body">
                                        <div x-show="open" x-transition
                                            class="fixed z-[9999] rounded-xl bg-white shadow-lg border"
                                            :style="style" @click.outside="open=false"
                                            @resize.window="open && place()" @scroll.window.passive="open && place()">
                                            <div class="p-2 border-b">
                                                <input type="text" wire:model.live="searchContrato"
                                                    @keydown.enter="open=false" @keydown.escape="open=false"
                                                    class="w-full border border-blue-300/60 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                    placeholder="Buscar contrato…">
                                            </div>

                                            @php
                                                $listaContratos =
                                                    ($filteredContratos ?? null) instanceof
                                                    \Illuminate\Support\Collection
                                                        ? $filteredContratos
                                                        : $contratosEmpresa ?? collect();
                                            @endphp

                                            <ul class="max-h-64 overflow-auto py-2 text-sm">
                                                @forelse($listaContratos as $c)
                                                    <li wire:click="setContrato({{ $c->id_contrato }})"
                                                        @click="open=false; @this.set('searchContrato', '');"
                                                        class="cursor-pointer px-3 py-2 hover:bg-blue-50">
                                                        <div class="font-medium truncate">#{{ $c->nro_contrato }} —
                                                            {{ $c->nombre }}</div>
                                                        <div class="text-[11px] text-slate-500">
                                                            {{ $c->fecha_inicio }} — {{ $c->fecha_fin }}
                                                            @if (!is_null($c->monto))
                                                                · {{ $c->moneda }}
                                                                {{ number_format($c->monto, 2, ',', '.') }}
                                                            @endif
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="px-3 py-4 text-center text-slate-400">Sin resultado</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            @else
                                <div
                                    class="border border-dashed rounded-xl px-3 py-3 text-sm text-slate-500 bg-slate-50">
                                    Seleccione un proveedor con contratos disponibles.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- BLOQUE 3: Tipo de Servicio + SLA + Descripción -->
                @if ($this->hayActivosSeleccionados)
                    <div class="bg-white border rounded-2xl shadow-sm p-4 md:p-6 space-y-6">
                        <div class="grid md:grid-cols-3 gap-6">
                            <!-- Tipo de Servicio -->
                            <div class="md:col-span-1">
                                <label class="text-[13px] font-semibold text-slate-600">Tipo de servicio</label>
                                <div class="mt-3 flex flex-col gap-2">
                                    <label
                                        class="flex items-center gap-3 rounded-xl border px-3 py-2.5 cursor-pointer hover:border-blue-400 transition">
                                        <input type="radio" wire:model.live="selectedTipoServicio"
                                            value="Correctivo/Reparación"
                                            class="h-4 w-4 text-blue-600 border-slate-300">
                                        <span>Correctivo / Reparación</span>
                                    </label>
                                    <label
                                        class="flex items-center gap-3 rounded-xl border px-3 py-2.5 cursor-pointer hover:border-blue-400 transition">
                                        <input type="radio" wire:model.live="selectedTipoServicio"
                                            value="Preventivo" class="h-4 w-4 text-blue-600 border-slate-300">
                                        <span>Preventivo</span>
                                    </label>
                                </div>
                                @error('selectedTipoServicio')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- SLA / Programación -->
                            <div class="md:col-span-2">
                                @if ($selectedTipoServicio == 'Preventivo')
                                    <div class="rounded-2xl border p-4">
                                        <h4 class="font-semibold text-slate-700 mb-3">Acuerdo de Nivel de Servicio
                                            (SLA)</h4>
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" wire:click="selectSLA('sla_4hs')"
                                                    wire:model="sla_4hs" class="h-4 w-4 text-blue-600"
                                                    :disabled="$sla_8hs || $sla_24hs || $sla_12hs">
                                                <span>Menos de 4 hs</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" wire:click="selectSLA('sla_8hs')"
                                                    wire:model="sla_8hs" class="h-4 w-4 text-blue-600"
                                                    :disabled="$sla_4hs || $sla_24hs || $sla_12hs">
                                                <span>Dentro de 8 hs</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" wire:click="selectSLA('sla_24hs')"
                                                    wire:model="sla_24hs" class="h-4 w-4 text-blue-600"
                                                    :disabled="$sla_4hs || $sla_8hs || $sla_12hs">
                                                <span>Dentro de 24 hs</span>
                                            </label>
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" wire:click="selectSLA('sla_12hs')"
                                                    wire:model="sla_12hs" class="h-4 w-4 text-blue-600"
                                                    :disabled="$sla_4hs || $sla_8hs || $sla_24hs">
                                                <span>Dentro de 12 hs</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                @if ($selectedTipoServicio == 'Correctivo/Reparación')
                                    <div class="rounded-2xl border p-4 space-y-4">
                                        <h4 class="font-semibold text-slate-700">Acuerdo de Nivel de Servicio (SLA)
                                        </h4>
                                        <div class="grid md:grid-cols-3 gap-4">
                                            <div class="space-y-2 md:col-span-1">
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="slaTipo" wire:model.live="slaTipo"
                                                        value="programado" class="h-4 w-4 text-blue-600">
                                                    <span>Programado</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="radio" name="slaTipo" wire:model.live="slaTipo"
                                                        value="periodico" class="h-4 w-4 text-blue-600">
                                                    <span>Periódico</span>
                                                </label>
                                            </div>

                                            <div class="md:col-span-2 space-y-4">
                                                @if ($slaTipo === 'programado')
                                                    <div class="flex items-center gap-3">
                                                        <label class="text-sm text-slate-600">Definir fecha</label>
                                                        <input type="date" wire:model="fechaProgramada"
                                                            min="{{ date('Y-m-d') }}"
                                                            class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                                    </div>
                                                    @error('fechaProgramada')
                                                        <p class="text-red-600 text-xs">{{ $message }}</p>
                                                    @enderror
                                                @endif

                                                @if ($slaTipo === 'periodico')
                                                    {{-- 3 columnas responsivas: Periodicidad / Inicia / Fin  --}}
                                                    <div class="grid grid-cols-12 gap-4 min-w-0">
                                                        <!-- Periodicidad -->
                                                        <div class="col-span-12 sm:col-span-6 md:col-span-4 min-w-0">
                                                            <label
                                                                class="block text-sm text-slate-600 mb-1">Periodicidad</label>
                                                            <select wire:model.live="periodicidad"
                                                                class="w-full min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                                                <option value="">Seleccione</option>
                                                                https://chatgpt.com/c/68dc3d47-7988-832e-ac95-4d11b5831604
                                                                <option value="diario">Cada día</option>
                                                                <option value="semana">Cada semana</option>
                                                                <option value="2semanas">Cada 2 semanas</option>
                                                                <option value="mes">Cada mes</option>
                                                            </select>
                                                            @error('periodicidad')
                                                                <p class="text-red-600 text-xs mt-1">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>

                                                        <!-- Inicia -->
                                                        <div class="col-span-6 md:col-span-4 min-w-0">
                                                            <label
                                                                class="block text-sm text-slate-600 mb-1">Inicia</label>
                                                            <input type="date" x-model="inicio"
                                                                wire:model="fechaInicio" min="{{ date('Y-m-d') }}"
                                                                class="w-full min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                                            @error('fechaInicio')
                                                                <p class="text-red-600 text-xs mt-1">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>

                                                        <!-- Fin -->
                                                        <div class="col-span-6 md:col-span-4 min-w-0">
                                                            <label
                                                                class="block text-sm text-slate-600 mb-1">Fin</label>
                                                            <input type="date" wire:model="fechaFin"
                                                                class="w-full min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                                            @error('fechaFin')
                                                                <p class="text-red-600 text-xs mt-1">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    @if (in_array($periodicidad, ['semana', '2semanas', 'mes']))
                                                        <div class="mt-4 min-w-0">
                                                            <label class="block text-sm text-slate-600 mb-2">Días de la
                                                                semana</label>
                                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                                                                @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                                                    <label
                                                                        class="inline-flex items-center gap-2 rounded-lg border px-3 py-2">
                                                                        <input type="checkbox"
                                                                            value="{{ $dia }}"
                                                                            wire:model="diasSeleccionados"
                                                                            class="h-4 w-4 text-blue-600">
                                                                        <span>{{ $dia }}</span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Descripción (solo correctivo) -->
                        @if ($selectedTipoServicio == 'Correctivo/Reparación')
                            <div class="space-y-2">
                                <label for="descripcion" class="text-[13px] font-semibold text-slate-600">Descripción
                                    de la solicitud</label>
                                <textarea id="descripcion" wire:model.live="descripcion" rows="5"
                                    class="w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                    placeholder="Describa el problema, impacto, componentes afectados y cualquier referencia interna…"></textarea>
                                @error('descripcion')
                                    <p class="text-red-600 text-xs">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Adjuntos -->
                        <div class="rounded-2xl border border-dashed p-4">
                            <h4 class="font-semibold text-slate-700 mb-3">Adjuntar evidencia (opcional)</h4>
                            <div class="flex items-center justify-between gap-3">
                                <input type="file" wire:model="newImages" multiple
                                    class="block w-full text-sm text-slate-700 file:mr-4 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                            </div>
                            @error('newImages.*')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            @if (is_array($imagenesTrabajo ?? null) && count($imagenesTrabajo) > 0)
                                <ul class="mt-3 divide-y text-sm">
                                    @foreach ($imagenesTrabajo as $index => $file)
                                        <li class="py-2 flex items-center justify-between">
                                            <span class="truncate">{{ $file->getClientOriginalName() }}</span>
                                            <button type="button" wire:click="removeImage({{ $index }})"
                                                class="text-red-600 hover:text-red-700 text-xs font-semibold">Quitar</button>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
                <!-- BLOQUE 2: Categoría + Activo -->
                <div class="bg-white border rounded-2xl shadow-sm p-4 md:p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Categoría -->
                        <div class="space-y-2">
                            <label class="text-[13px] font-semibold text-slate-600">Categoría</label>
                            @if ($cuit)
                                <div x-data="{ openDropdownCategoria: false }" @click.away="openDropdownCategoria = false"
                                    class="relative z-[60]">
                                    <button type="button" @click="openDropdownCategoria = !openDropdownCategoria"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-left focus:outline-none focus:ring-2 focus:ring-blue-500/30 hover:border-blue-400 transition">
                                        <div class="flex items-center justify-between">
                                            <span class="truncate text-slate-700">
                                                @if ($id_categoria && $categoriaBusqueda)
                                                    {{ $categoriaBusqueda->nombre }}
                                                @else
                                                    <span class="text-slate-400">Seleccione una Categoría</span>
                                                @endif
                                            </span>
                                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" />
                                            </svg>
                                        </div>
                                    </button>

                                    <div x-show="openDropdownCategoria" x-transition
                                        class="absolute mt-2 w-full rounded-xl bg-white shadow-lg border z-10">
                                        <div class="p-2 border-b">
                                            <input type="text" wire:model.live="searchCategoria"
                                                @keydown.enter="openDropdownCategoria = false"
                                                @keydown.escape="openDropdownCategoria = false"
                                                class="w-full border border-blue-300/60 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                placeholder="Buscar categoría…">
                                        </div>
                                        <ul class="max-h-64 overflow-auto py-2 text-sm">
                                            @forelse(($filteredCategorias ?? collect()) as $cat)
                                                <li wire:click="setIdCategoria({{ $cat->id_categoria }})"
                                                    @click="openDropdownCategoria = false; @this.set('searchCategoria', '');"
                                                    class="cursor-pointer px-3 py-2 hover:bg-blue-50">
                                                    <span class="block truncate">{{ $cat->nombre }}</span>
                                                </li>
                                            @empty
                                                <li class="px-3 py-4 text-center text-slate-400">Sin resultado</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="border border-dashed rounded-xl px-3 py-3 text-sm text-slate-500 bg-slate-50">
                                    Seleccione un proveedor para habilitar categorías.
                                </div>
                            @endif
                            @error('id_categoria')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Activo -->
                        <div class="space-y-2">
                            <label class="text-[13px] font-semibold text-slate-600">Bienes</label>

                            @if ($cuit && $id_categoria)
                                <div x-data="{ openDropdownActivo: false }" @click.away="openDropdownActivo = false"
                                    class="relative z-[60]">

                                    {{-- Botón del dropdown --}}
                                    <button type="button" @click="openDropdownActivo = !openDropdownActivo"
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-left focus:outline-none focus:ring-2 focus:ring-blue-500/30 hover:border-blue-400 transition">

                                        <div class="flex items-center justify-between">
                                            <span class="truncate text-slate-700">
                                                @php $countSel = is_array($seleccionActivos ?? null) ? count($seleccionActivos) : 0; @endphp
                                                @if ($countSel > 0)
                                                    {{ $countSel }} activo{{ $countSel > 1 ? 's' : '' }}
                                                    seleccionado{{ $countSel > 1 ? 's' : '' }}
                                                @else
                                                    <span class="text-slate-400">Seleccione uno o varios Bienes</span>
                                                @endif
                                            </span>
                                            <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" />
                                            </svg>
                                        </div>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div x-show="openDropdownActivo" x-transition
                                        class="absolute mt-2 w-full rounded-xl bg-white shadow-lg border z-10">

                                        {{-- Header: búsqueda + acciones --}}
                                        <div class="p-2 border-b">
                                            <div class="flex items-center gap-2">
                                                <input type="text" wire:model.live="searchActivo"
                                                    @keydown.enter="openDropdownActivo = false"
                                                    @keydown.escape="openDropdownActivo = false"
                                                    class="w-full border border-blue-300/60 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                    placeholder="Buscar Bien…">

                                                {{-- Seleccionar todos (sobre la lista filtrada) --}}
                                                <button type="button" wire:click="seleccionarTodos" @click.stop
                                                    class="px-2 py-1 text-xs rounded border text-slate-700 bg-white hover:bg-slate-50">
                                                    Seleccionar todos
                                                </button>

                                                {{-- Limpiar --}}
                                                <button type="button" wire:click="limpiarSeleccion" @click.stop
                                                    class="px-2 py-1 text-xs rounded border text-slate-700 bg-white hover:bg-slate-50">
                                                    Limpiar
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Lista de activos con checkboxes --}}
                                        <ul class="max-h-64 overflow-auto py-2 text-sm divide-y">
                                            @forelse(($filteredActivos ?? collect()) as $activo)
                                                <li class="px-3 py-2 hover:bg-blue-50"
                                                    wire:key="act-{{ $activo->id_activo }}">
                                                    <label
                                                        class="flex items-center justify-between cursor-pointer gap-3">
                                                        <span
                                                            class="truncate font-medium">{{ $activo->nombre }}</span>

                                                        <input type="checkbox"
                                                            class="h-4 w-4 text-blue-600 border-slate-300 rounded"
                                                            value="{{ $activo->id_activo }}"
                                                            wire:model.live="seleccionActivos" @click.stop>
                                                    </label>
                                                </li>
                                            @empty
                                                <li class="px-3 py-4 text-center text-slate-400">Sin resultado</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            @else
                                <div
                                    class="border border-dashed rounded-xl px-3 py-3 text-sm text-slate-500 bg-slate-50">
                                    Seleccione una categoría para habilitar Bienes.
                                </div>
                            @endif

                            @error('seleccionActivos')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Detalles del activo -->
                    <div class="mt-6">
                        <div class="rounded-2xl border bg-slate-50/60 p-4">
                            <h3 class="font-semibold text-slate-700 mb-3">
                                Detalles de Bienes seleccionados
                                @if (!empty($activosSeleccionados))
                                    <span
                                        class="text-slate-500 text-sm font-normal">({{ count($activosSeleccionados) }})</span>
                                @endif
                            </h3>

                            @if (empty($activosSeleccionados))
                                <p class="text-sm text-slate-500">Seleccioná uno o varios bienes para ver sus
                                    detalles.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach ($activosSeleccionados as $a)
                                        <div class="bg-white border rounded-xl p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <div class="font-medium text-slate-800">{{ $a->nombre }}</div>
                                                    <div class="text-[12px] text-slate-500">ID: {{ $a->id_activo }}
                                                    </div>
                                                </div>
                                                <button type="button" wire:click="removeActivo({{ $a->id_activo }})"
                                                    class="text-red-600 hover:text-red-700 text-xs font-semibold">Quitar</button>
                                            </div>

                                            <div class="grid md:grid-cols-4 gap-4 text-sm mt-3">
                                                <div>
                                                    <p class="text-slate-500">Ubicación</p>
                                                    <p class="font-medium">
                                                        {{ optional($a->ubicacion)->nombre ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-slate-500">Estado General</p>
                                                    <p class="font-medium">
                                                        {{ optional($a->estadoGeneral)->nombre ?? 'N/A' }}</p>
                                                </div>
                                                <div class="md:col-span-2">
                                                    <p class="text-slate-500">Responsable</p>
                                                    @php
                                                        $resp =
                                                            $a->asignaciones && $a->asignaciones->count()
                                                                ? $a->asignaciones->first()->responsable
                                                                : null;
                                                    @endphp
                                                    <p class="font-medium">{{ $resp ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- ASIDE: Resumen -->
            <aside class="xl:col-span-2">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white border rounded-2xl shadow-sm p-4">
                        <h3 class="font-semibold text-slate-700 mb-3">Resumen</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Empresa</dt>
                                <dd class="font-medium text-right truncate max-w-[55%]">
                                    @if ($cuit && $empresaBusqueda)
                                        {{ $empresaBusqueda->razon_social }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Contrato</dt>
                                <dd class="font-medium text-right truncate max-w-[55%]">
                                    @if ($id_contrato && $contratoSeleccionado)
                                        #{{ $contratoSeleccionado->nro_contrato }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Categoría</dt>
                                <dd class="font-medium text-right truncate max-w-[55%]">
                                    @if ($id_categoria && $categoriaBusqueda)
                                        {{ $categoriaBusqueda->nombre }}
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-slate-500">Tipo</dt>
                                <dd class="font-medium text-right truncate max-w-[55%]">
                                    {{ $selectedTipoServicio ?: '—' }}
                                </dd>
                            </div>

                            @if ($selectedTipoServicio == 'Correctivo/Reparación')
                                <div class="pt-2 border-t">
                                    <p class="text-slate-500 text-xs mb-1">SLA · Correctivo</p>
                                    <p class="text-sm">
                                        @if ($slaTipo === 'programado' && $fechaProgramada)
                                            Programado el <span
                                                class="font-medium">{{ \Carbon\Carbon::parse($fechaProgramada)->format('d/m/Y') }}</span>
                                        @elseif ($slaTipo === 'periodico')
                                            <span class="font-medium">Periódico</span>
                                            @if ($periodicidad)
                                                · {{ $periodicidad }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            @endif

                            @if ($selectedTipoServicio == 'Preventivo')
                                <div class="pt-2 border-t">
                                    <p class="text-slate-500 text-xs mb-1">SLA · Preventivo</p>
                                    <p class="text-sm">
                                        @php
                                            $sla = collect([
                                                $sla_4hs ? '≤4h' : null,
                                                $sla_8hs ? '≤8h' : null,
                                                $sla_12hs ? '≤12h' : null,
                                                $sla_24hs ? '≤24h' : null,
                                            ])
                                                ->filter()
                                                ->implode(' / ');
                                        @endphp
                                        {{ $sla ?: '—' }}
                                    </p>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                        <p class="text-sm text-blue-800">
                            Consejos: incluí contexto del impacto, fotos nítidas y referencias internas
                            (nro. de equipo, sector, piso). Acelera la respuesta del proveedor.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    <footer class="sticky bottom-0 z-30 border-t bg-white/95 backdrop-blur">
        <div class="mx-auto max-w-[1680px] px-4 lg:px-6 py-4"> {{-- mismo ancho que header/main --}}
            {{-- misma grilla que arriba: 12 columnas, form 10/12 + aside 2/12 --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-center">
                {{-- Zona alineada al ancho del FORM (10/12) --}}
                <div class="xl:col-span-10">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            @if ($id_activo)
                                Listo para enviar. Revise el resumen antes de continuar.
                            @else
                                Complete los pasos para habilitar el envío.
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" onclick="window.history.back()"
                                class="px-4 py-2 rounded-xl border bg-white hover:bg-slate-50 text-slate-700">
                                Cancelar
                            </button>
                            <button wire:click="save" @disabled(!$this->hayActivosSeleccionados) @class([
                                'px-4 py-2 rounded-xl text-white bg-blue-600 hover:bg-blue-700',
                                'opacity-50 cursor-not-allowed' => !$this->hayActivosSeleccionados,
                            ])>
                                Enviar orden
                            </button>
                        </div>
                    </div>
                </div>
                <div class="hidden xl:block xl:col-span-2"></div>
            </div>
        </div>
    </footer>
</div>

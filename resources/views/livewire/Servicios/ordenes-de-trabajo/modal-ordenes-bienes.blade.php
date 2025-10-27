<div>
    <x-dialog-modal wire:model.live="open" maxWidth="3xl">
        {{-- TITULO --}}
        <x-slot name="title">
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Nueva Orden de Trabajo</h2>
                    <p class="text-xs text-gray-500">
                        Activo:
                        <span class="font-medium">
                            {{ $activo->nombre ?? 'N/D' }} (ID: {{ $id_activo ?? '—' }})
                        </span>
                    </p>
                </div>
                <span class="text-[11px] px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                    Gestión → Proveedor
                </span>
            </div>
        </x-slot>

        {{-- CONTENIDO --}}
        <x-slot name="content">
            <div class="space-y-6">
                {{-- PROVEEDOR / CONTRATO --}}
                {{-- PROVEEDOR / CONTRATO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Proveedor --}}
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-gray-700">Proveedor</label>
                        <select wire:model.live="proveedorCuit"
                            class="w-full min-h-[44px] border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white">
                            <option value="">Seleccione…</option>
                            @foreach ($proveedores as $p)
                                <option value="{{ $p['cuit'] }}">{{ $p['razon_social'] }} · CUIT {{ $p['cuit'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="proveedorCuit" class="text-xs" />
                    </div>

                    {{-- Contrato (opcional) --}}
                    @php $disableContrato = empty($proveedorCuit); @endphp
                    <div class="space-y-1.5">
                        <label class="text-[13px] font-semibold text-gray-700">Contrato (opcional)</label>
                        <select wire:model.live="id_contrato"
                            class="w-full min-h-[44px] border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 bg-white {{ $disableContrato ? 'bg-gray-50 text-gray-400 cursor-not-allowed' : '' }}"
                            @if ($disableContrato) disabled @endif>
                            <option value="">— Sin contrato —</option>
                            @foreach ($contratos as $c)
                                <option value="{{ $c->id_contrato }}">
                                    #{{ $c->nro_contrato }} — {{ $c->nombre }}
                                    ({{ \Illuminate\Support\Str::limit((string) $c->fecha_inicio, 10, '') }}
                                    → {{ \Illuminate\Support\Str::limit((string) $c->fecha_fin, 10, '') }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="id_contrato" class="text-xs" />
                        @if ($disableContrato)
                            <p class="text-[11px] text-gray-500">Elegí un proveedor para ver sus contratos aplicables.
                            </p>
                        @endif
                    </div>
                </div>
                {{-- Tipo de servicio --}}
                <div>
                    <label class="text-[13px] font-semibold text-gray-700">Tipo de servicio</label>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <label class="inline-flex items-center gap-2 border rounded-xl px-3 py-2.5 cursor-pointer">
                            <input type="radio" wire:model.live="selectedTipoServicio" value="Correctivo/Reparación"
                                class="h-4 w-4">
                            <span>Correctivo / Reparación</span>
                        </label>
                        <label class="inline-flex items-center gap-2 border rounded-xl px-3 py-2.5 cursor-pointer">
                            <input type="radio" wire:model.live="selectedTipoServicio" value="Preventivo"
                                class="h-4 w-4">
                            <span>Preventivo</span>
                        </label>
                    </div>
                    <x-input-error for="selectedTipoServicio" class="mt-1 text-xs" />
                </div>

                {{-- SLA / Programación --}}
                @if ($selectedTipoServicio === 'Preventivo')
                    <div class="rounded-2xl border p-4">
                        <h4 class="font-semibold text-gray-700 mb-3">SLA</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    wire:click="$set('sla_8hs', false); $set('sla_12hs', false); $set('sla_24hs', false)"
                                    wire:model="sla_4hs" class="h-4 w-4"> ≤4h
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    wire:click="$set('sla_4hs', false); $set('sla_12hs', false); $set('sla_24hs', false)"
                                    wire:model="sla_8hs" class="h-4 w-4"> ≤8h
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    wire:click="$set('sla_4hs', false); $set('sla_8hs', false); $set('sla_24hs', false)"
                                    wire:model="sla_12hs" class="h-4 w-4"> ≤12h
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="checkbox"
                                    wire:click="$set('sla_4hs', false); $set('sla_8hs', false); $set('sla_12hs', false)"
                                    wire:model="sla_24hs" class="h-4 w-4"> ≤24h
                            </label>
                        </div>
                        @error('sla_preventivo')
                            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif ($selectedTipoServicio === 'Correctivo/Reparación')
                    <div class="rounded-2xl border p-4 space-y-4">
                        <div class="flex items-center gap-6 text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" wire:model.live="slaTipo" value="programado" class="h-4 w-4">
                                Programado
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" wire:model.live="slaTipo" value="periodico" class="h-4 w-4">
                                Periódico
                            </label>
                        </div>

                        @if ($slaTipo === 'programado')
                            <div class="flex items-center gap-3">
                                <label class="text-sm text-gray-600">Fecha programada</label>
                                <input type="date" wire:model="fechaProgramada" min="{{ date('Y-m-d') }}"
                                    class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                            <x-input-error for="fechaProgramada" class="text-xs" />
                        @endif

                        @if ($slaTipo === 'periodico')
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 sm:col-span-4">
                                    <label class="block text-sm text-gray-600 mb-1">Periodicidad</label>
                                    <select wire:model.live="periodicidad"
                                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                        <option value="">Seleccione</option>
                                        <option value="diario">Cada día</option>
                                        <option value="semana">Cada semana</option>
                                        <option value="2semanas">Cada 2 semanas</option>
                                        <option value="mes">Cada mes</option>
                                    </select>
                                    <x-input-error for="periodicidad" class="mt-1 text-xs" />
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block text-sm text-gray-600 mb-1">Inicia</label>
                                    <input type="date" wire:model="fechaInicio" min="{{ date('Y-m-d') }}"
                                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <x-input-error for="fechaInicio" class="mt-1 text-xs" />
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label class="block text-sm text-gray-600 mb-1">Fin</label>
                                    <input type="date" wire:model="fechaFin"
                                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                    <x-input-error for="fechaFin" class="mt-1 text-xs" />
                                </div>
                            </div>

                            @php $mostrarDias = in_array($periodicidad, ['semana','2semanas','mes'], true); @endphp
                            @if ($mostrarDias)
                                <div class="mt-3">
                                    <label class="block text-sm text-gray-600 mb-2">Días</label>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-sm">
                                        @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                            <label class="inline-flex items-center gap-2 border rounded-lg px-3 py-2">
                                                <input type="checkbox" wire:model="diasSeleccionados"
                                                    value="{{ $dia }}" class="h-4 w-4">
                                                <span>{{ $dia }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div>
                            <label class="text-[13px] font-semibold text-gray-700">Descripción</label>
                            <textarea rows="3" wire:model.live="descripcion"
                                class="mt-1 w-full border rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                                placeholder="Describa el problema, impacto, referencias internas…"></textarea>
                            <x-input-error for="descripcion" class="mt-1 text-xs" />
                        </div>
                    </div>
                @endif

                {{-- Adjuntos --}}
                <div class="rounded-2xl border p-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-semibold text-gray-700">Adjuntos (opcional)</h4>
                        <label for="fileOrden"
                            class="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 cursor-pointer">
                            Subir archivos
                        </label>
                        <input id="fileOrden" type="file" multiple wire:model="newImages" class="hidden" />
                    </div>

                    @error('imagenesTrabajo.*')
                        <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
                    @enderror

                    @if (!empty($imagenesTrabajo))
                        <ul class="mt-3 divide-y text-sm">
                            @foreach ($imagenesTrabajo as $idx => $f)
                                <li class="py-2 flex items-center justify-between">
                                    <span class="truncate">{{ $f->getClientOriginalName() }}</span>
                                    <button type="button" wire:click="removeImage({{ $idx }})"
                                        class="text-red-600 hover:text-red-700 text-xs font-semibold">
                                        Quitar
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-xs text-gray-500">Podés adjuntar fotos, PDFs, etc. (hasta 10 MB c/u).</p>
                    @endif
                </div>
            </div>
        </x-slot>

        {{-- FOOTER --}}
        <x-slot name="footer">
            @php
                $btnDisabled = empty($proveedorCuit) || empty($selectedTipoServicio);
            @endphp

            <div class="w-full flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    {{ $proveedorCuit && $selectedTipoServicio ? 'Listo para enviar.' : 'Complete los campos requeridos.' }}
                </div>

                <div class="flex gap-2">
                    <x-secondary-button type="button" wire:click="$set('open', false)" class="px-4 py-2 text-sm">
                        Cancelar
                    </x-secondary-button>

                    {{-- Si tu <x-button> acepta :disabled --}}
                    <x-button type="button" wire:click="save" :disabled="$btnDisabled"
                        class="px-4 py-2 text-sm text-white {{ $btnDisabled ? 'bg-indigo-600 opacity-50 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                        Enviar
                    </x-button>

                    {{-- Alternativa botón nativo si tu componente no soporta :disabled
                    <button type="button" wire:click="save"
                            {{ $btnDisabled ? 'disabled' : '' }}
                            class="px-4 py-2 text-sm text-white {{ $btnDisabled ? 'bg-indigo-600 opacity-50 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700' }}">
                        Enviar
                    </button>
                    --}}
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

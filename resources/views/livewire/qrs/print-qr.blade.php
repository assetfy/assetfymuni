<div
    x-data="{
    zoom: 0.7,                       // 70% por defecto
    async start(){ /* tu mismo start */ }
  }"
    x-on:print-qr.window="start()">


    <x-dialog-modal wire:model.live="open" maxWidth="3xl" class="modal-qr">
        <!-- WRAPPER para limitar altura total del modal -->
        <div class="flex flex-col max-h-[85vh]">

            <!-- Title (queda igual) -->
            <x-slot name="title">
                <h2 class="text-lg font-bold text-black border-b pb-2">
                    Configurar Impresión de Códigos QR
                </h2>
            </x-slot>

            <x-slot name="content">
                <div class="modal-body overflow-y-auto overflow-x-hidden pr-1 space-y-5">
                    @php
                    // Dimensiones de página
                    [$pw, $ph] = $this->pageDims;

                    // Normaliza la selección
                    $ids = is_array($selectedIds) ? $selectedIds : [];
                    $perPage = max(1, (int) ($this->perPage ?? 0));

                    // Siempre un array, aunque no haya ids
                    $pageChunks = $ids ? array_chunk($ids, $perPage) : [];

                    [$uw,$uh] = $this->usableDims;

                    @endphp
                    {{-- Resumen selección --}}
                    <div class="mt-1 text-sm text-slate-600">
                        <span class="font-bold text-black">{{ $selectedCount }} elemento(s) seleccionados para imprimir.</span>
                    </div>

                    {{-- Configuración --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <x-label class="text-sm font-medium text-black">Ancho (mm)</x-label>
                            <x-input type="number" min="10" max="{{ (int) floor($uw) }}" step="1"
                                wire:model.live.debounce.300ms="labelW"
                                wire:loading.attr="disabled" wire:target="labelW"
                                class="w-full !h-10 !bg-gray-100 !border !border-gray-800 !rounded-xl !px-4 !shadow-inner
                                        focus:!ring-2 focus:!ring-gray-800 focus:!border-gray-800" />
                        </div>
                        <div>
                            <x-label class="text-sm font-medium text-black">Alto (mm)</x-label>
                            <x-input type="number" min="10" max="{{ (int) floor($uh) }}" step="1"
                                wire:model.live.debounce.300ms="labelH"
                                wire:loading.attr="disabled" wire:target="labelH"
                                class="w-full !h-10 !bg-gray-100 !border !border-gray-800 !rounded-xl !px-4 !shadow-inner
                focus:!ring-2 focus:!ring-gray-800 focus:!border-gray-800" />
                        </div>
                    </div>

                    {{-- ===== Vista previa con zoom y altura limitada ===== --}}
                    <div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <label for="zoomPreview" class="text-black font-medium">Zoom de vista previa</label>
                            <input id="zoomPreview"
                                type="range" min="0.4" max="1" step="0.05"
                                x-model.number="zoom"
                                aria-describedby="zoomHelp"
                                class="w-40">
                            <span class="text-black" x-text="Math.round(zoom*100)+'%'"></span>

                            <!-- Reset rápido a 100% -->
                            <button type="button"
                                class="ml-2 px-2 py-0.5 text-xs rounded border border-slate-300 hover:bg-slate-100"
                                @click="zoom = 1"
                                title="Restaurar 100%">
                                100%
                            </button>
                        </div>

                        <p id="zoomHelp" class="mt-1 text-xs text-slate-500">
                            Este control solo cambia el tamaño de la <strong>vista previa</strong> en esta ventana.
                            <strong>No</strong> afecta el tamaño de impresión ni de las etiquetas. Para eso usa
                            <em>Ancho (mm)</em> y <em>Alto (mm)</em>.
                        </p>


                        <div class="mt-3 rounded-xl border border-slate-200 bg-white overflow-hidden">
                            <!-- Métricas -->
                            <div class="grid grid-cols-3 gap-6 px-5 py-3 text-sm text-slate-600">
                                <div>QR por fila: <span class="font-semibold text-slate-900">{{ $this->isLayoutValid ? $this->perRow : '—' }}</span></div>
                                <div>QR por página: <span class="font-semibold text-slate-900">{{ $this->isLayoutValid ? $this->perPage : '—' }}</span></div>
                                <div>Total de páginas: <span class="font-semibold text-slate-900">{{ $this->isLayoutValid ? $this->totalPages : '—' }}</span></div>
                            </div>

                            <div class="overflow-hidden p-0">
                                @php
                                [$uw,$uh] = $this->usableDims;

                                $cols = max(1, $this->perRow);
                                $rows = max(1, $this->perCol);

                                $gapXPct = $uw>0 ? ($gap/$uw)*100 : 0;
                                $gapYPct = $uh>0 ? ($gap/$uh)*100 : 0;

                                // tamaño de celda compensando gaps
                                $colPct = (100 - ($cols-1)*$gapXPct)/$cols;
                                $rowPct = (100 - ($rows-1)*$gapYPct)/$rows;

                                // pequeño margen para evitar “mordidas” por redondeo
                                $eps = 0.15; // %
                                $colPct = max(0, $colPct - $eps/$cols);
                                $rowPct = max(0, $rowPct - $eps/$rows);
                                @endphp
                                @if (! $this->isLayoutValid)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 text-center text-slate-500">
                                    Ingrese dimensiones válidas para ver la vista previa
                                </div>
                                @if ($this->validationError)
                                <p class="mt-3 text-sm text-rose-600 text-center">{{ $this->validationError }}</p>
                                @endif
                                @else
                                <!-- VIEWPORT con altura limitada y scroll si hace falta -->
                                <div class="overflow-hidden p-0">
                                    <!-- SIZER: su tamaño ya viene escalado, así no queda scroll vacío -->
                                    <div
                                        x-data
                                        :style="`--z:${zoom}; width: calc({{ $uw }}mm * var(--z)); height: calc({{ $uh }}mm * var(--z));`"
                                        class="relative">
                                        <!-- CANVAS real, a mm, escalado con transform -->
                                        <div class="absolute top-0 left-0 origin-top-left leading-[0]"
                                            :style="`transform: scale(${zoom}); width: {{ $uw }}mm; height: {{ $uh }}mm;`">

                                            <div class="relative" style="width: {{ $uw }}mm; height: {{ $uh }}mm;">
                                                <!-- Punteado como overlay (no recorta) -->
                                                <div class="absolute inset-0 rounded-lg pointer-events-none"
                                                    style="outline:1.5px dashed rgba(148,163,184,.85); outline-offset:-1px;"></div>

                                                <!-- GRID ocupa todo el área; sin overflow y con 1px de respiro -->
                                                <div class="absolute inset-0 w-full h-full box-border" style="padding:1px;">
                                                    <div class="grid w-full h-full"
                                                        style="
                 grid-template-columns: repeat({{ $cols }}, {{ $colPct }}%);
                 grid-auto-rows: {{ $rowPct }}%;
                 column-gap: {{ $gapXPct }}%;
                 row-gap: {{ $gapYPct }}%;
               ">
                                                        @for ($i = 0; $i < max(0, $this->perPage); $i++)
                                                            <div class="grid place-items-center rounded-lg ring-1 ring-slate-300 bg-slate-100/70 box-border">
                                                                <span class="text-[10px] text-slate-500">QR</span>
                                                            </div>
                                                            @endfor
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
                    <x-danger-button onclick="printMode('masiva')" :disabled="$this->perPage === 0">
                        Vista previa / Imprimir
                    </x-danger-button>
                </div>
            </x-slot>
    </x-dialog-modal>

    {{-- === BLOQUE DE IMPRESIÓN REAL === --}}
    @php
    // Dimensiones
    [$pw, $ph] = $this->pageDims;

    // Normaliza ids
    $ids = is_array($selectedIds) ? array_values($selectedIds) : [];
    $perPage = max(1, (int) ($this->perPage ?? 0));

    // >>> RELLENO para que cada página tenga EXACTAMENTE $perPage etiquetas
    if ($ids && $perPage > 0) {
    $total = count($ids);
    $resto = $total % $perPage;
    if ($resto !== 0) {
    $faltan = $perPage - $resto; // lo que falta para completar la última página
    for ($i = 0; $i < $faltan; $i++) {
        // repetimos desde el principio (o podés cambiar la estrategia)
        $ids[]=$ids[$i % $total];
        }
        }
        }

        // Ahora sí, chunkeamos en páginas con EXACTAMENTE $perPage
        $pageChunks=$ids ? array_chunk($ids, $perPage) : [];
        @endphp

        <div id="masiva" class="print-masiva">
        @foreach ($pageChunks as $p => $pageIds)
        @php
        // Área útil de la hoja (mm)
        [$uw,$uh] = $this->usableDims;

        $cols = max(1, $this->perRow);
        $cnt = count($pageIds); // QRs en ESTA página
        $rowsThis = max(1, (int) ceil($cnt / $cols));
        $autoFit = ($cnt <= 2); // regla: 1 o 2 -> agrandar

            // Dimensiones efectivas por celda
            $useW = $labelW;
            $useH = $labelH;
            $useGap= $gap;

            if ($autoFit) {
            // llenar la página respetando gaps
            $useW = round( ($uw - ($cols - 1) * $useGap) / $cols, 2 );
            $useH = round( ($uh - ($rowsThis - 1) * $useGap) / $rowsThis, 2 );
            }
            @endphp
            <div class="print-page"
                style="width: {{ $pw }}mm; height: {{ $ph }}mm; padding: {{ $margin }}mm;">
                <div class="grid"
                    style="
       grid-template-columns: repeat({{ $cols }}, {{ $useW }}mm);
       grid-auto-rows: {{ $useH }}mm;
       gap: {{ $useGap }}mm;
     ">
                    @foreach ($pageIds as $assetId)
                    @php
                    $qrUrl = $this->qrUrlFor($assetId);
                    $nombre = $assetNames[$assetId] ?? ('ID '.$assetId);

                    // Dimensiones internas útiles
                    $innerH = max(0, $useH - 2*$padding);
                    $innerW = max(0, $useW - 2*$padding);

                    // Contexto (1 o 2 por página => agrandar)
                    $isOne = ($cnt === 1);
                    $isTwo = ($cnt === 2);
                    $autoFit = ($cnt <= 2);

                        // Bases
                        $qrBase=$autoFit ? min($innerW, $innerH) : min($qrSize, $innerW);
                        $logoBase=$autoFit
                        ? min($innerH * 0.22, $innerW * 0.18, 26.0)
                        : min($innerH * 0.14, 9.0);

                        $gap1Base=0.8; // logo ↔ QR
                        $bottom=0.3; // respiración inferior

                        $stackBase=$logoBase + $gap1Base + $qrBase + $bottom;

                        // Escala
                        $scaleUpH=$stackBase> 0 ? ($innerH / $stackBase) : 1;
                        $scaleUpW = $qrBase > 0 ? ($innerW / $qrBase) : 1;
                        $scaleCap = $autoFit ? 2.2 : 1.45;
                        $scale = min($scaleUpH, $scaleUpW, $scaleCap);

                        // Paddings
                        $padTop = $padding;
                        $padSide = $padding;
                        $padBottom = max(0.6, round($padding * 0.25, 2));

                        // Banda para el nombre
                        $nameZonePct = $isOne ? 0.30 : ($isTwo ? 0.22 : 0.12);
                        $nameZoneMin = max($isOne ? 14.0 : ($isTwo ? 11.0 : 2.0), round($useH * $nameZonePct, 2));

                        $scaleCapByName = ($useH - $padTop - $padBottom - $nameZoneMin) / max(0.001, $stackBase);
                        $scale = min($scale, $scaleCapByName, $scaleCap);

                        // Medidas finales
                        $logoH = $logoBase * $scale;
                        $qrFinal = min($innerW, $qrBase * $scale);
                        $gapLogoQr = round($gap1Base * $scale, 2);

                        $nameZoneH = max(0, $useH - $padTop - $logoH - $gapLogoQr - $qrFinal - $padBottom);

                        // Tamaño del nombre
                        $len = mb_strlen($nombre ?? '');
                        $lines = $len > 20 ? 2 : 1;
                        $lh = 1.02;

                        if ($len <= 8) { $fsBase=7.2; }
                            elseif ($len <=14) { $fsBase=6.4; }
                            elseif ($len <=20) { $fsBase=5.8; }
                            elseif ($len <=28) { $fsBase=5.2; }
                            else { $fsBase=4.8; }

                            $fsBoost=$isOne ? 2.6 : ($isTwo ? 2.0 : 1.0);

                            $fsMaxH=max(5.0, ($nameZoneH - 0.8) / ($lh * $lines)); // tope por alto
                            $fsMaxW=max(5.0, ($innerW - 2) * 0.28); // tope aproximado por ancho

                            $fsRaw=$fsBase * $scale * $fsBoost;
                            $fs=min($fsRaw, $fsMaxH, $fsMaxW); // ← esta es la única asignación
                            @endphp

                            <div class="box-border qr-paper"
                            style="width: {{ $useW }}mm; height: {{ $useH }}mm;
                            padding: {{ $padTop }}mm {{ $padSide }}mm {{ $padBottom }}mm;">
                            <div class="label-inner" style="height:100%; display:flex; flex-direction:column; align-items:center;">
                                <img src="{{ asset('logos/asset-fy.png') }}"
                                    class="label-logo"
                                    style="max-height: {{ $logoH }}mm; object-fit:contain;">

                                <div class="qr-wrap" style="--qrs: {{ $qrFinal }}mm; margin-top: {{ $gapLogoQr }}mm;">
                                    <img src="{{ $qrUrl }}" alt="QR" class="qr-img" loading="eager" decoding="sync">
                                </div>

                                <div class="name-zone"
                                    style="height: {{ $nameZoneH }}mm; width:100%;
                    display:flex; align-items:center; justify-content:center;">
                                    <div class="label-name"
                                        style="font-size: {{ number_format($fs,2,'.','') }}mm !important; line-height: 1.02 !important;">
                                        {{ $nombre }}
                                    </div>
                                </div>
                            </div>
                </div>
                @endforeach
            </div>
</div>
@endforeach
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('css/edit-activos.css') }}">
</div>
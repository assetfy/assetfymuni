<!-- Detalles de Compra -->
<br>
<div class="mb-6">
    <x-label>
        <span class="flex items-center text-sm font-semibold text-gray-700 mb-1">
            <i class="fas fa-receipt text-blue-500 mr-2"></i>
            Detalles de Compra <span class="text-gray-400 ml-2">(Opcional)</span>
        </span>
    </x-label>
    <br>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
        {{-- Fecha de Compra --}}
        <div>
            @if ($editMode)
                <x-label for="fecha_compra" value="Fecha de Compra" />
                <input type="date" id="fecha_compra" wire:model.live="fechaCompra" max="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                @error('fechaCompra')
                    <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                @enderror
            @else
                <x-label>
                    <span class="flex items-center text-sm font-medium">
                        <i class="fas fa-calendar-alt text-purple-500 mr-2"></i> Fecha de Compra
                    </span>
                </x-label>
                <p class="text-gray-700 mt-1">
                    {{ $fechaCompra ? \Carbon\Carbon::parse($fechaCompra)->format('d/m/Y') : 'Sin datos' }}
                </p>
            @endif
        </div>

        {{-- Factura de Compra --}}
        <div>
            @if ($editMode)
                @if ($facturaCompra)
                    <a href="{{ Storage::disk('s3')->temporaryUrl($facturaCompra, now()->addMinutes(10)) }}"
                        target="_blank"
                        class="inline-block bg-blue-500 text-white font-medium py-2 px-4 rounded shadow hover:bg-blue-600 transition"
                        download>
                        <i class="fas fa-download mr-2"></i> Descargar Factura
                    </a>
                @else
                    <x-label for="factura_compra" value="Factura de Compra (PDF)" />
                    <div class="relative">
                        <i
                            class="fa-solid fa-file-invoice-dollar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="file" id="factura_compra" wire:model="factura_compra" accept="application/pdf"
                            class="pl-10 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
                    </div>
                    @error('factura_compra')
                        <span class="text-red-600 text-sm mt-1 block">{{ $message }}</span>
                    @enderror

                    @if ($factura_compra)
                        <div class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded mt-2">
                            Factura seleccionada: {{ $factura_compra->getClientOriginalName() }}
                        </div>
                    @endif
                @endif
            @else
                @if ($facturaCompra)
                    <a href="{{ Storage::disk('s3')->temporaryUrl($facturaCompra, now()->addMinutes(10)) }}"
                        target="_blank"
                        class="inline-block bg-blue-500 text-white font-medium py-2 px-4 rounded shadow hover:bg-blue-600 transition"
                        download>
                        <i class="fas fa-download mr-2"></i> Descargar Factura
                    </a>
                @else
                    <x-label>
                        <span class="flex items-center text-sm font-medium">
                            <i class="fas fa-file-alt text-blue-500 mr-2"></i> Factura de Compra
                        </span>
                    </x-label>
                    <p class="text-gray-500 mt-1">Sin factura</p>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Garantía -->
<div class="mb-6">
    <x-label>
        <span class="flex items-center text-sm font-semibold text-gray-700 mb-1">
            <i class="fas fa-shield-alt text-indigo-500 mr-2"></i>
            Garantía <span class="text-gray-400 ml-2">(Opcional)</span>
        </span>
    </x-label>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-2">
        {{-- Vencimiento --}}
        <div>
            @if ($garantiaVigente === 'Si' && $vencimientoGarantia)
                <x-label class="text-sm text-gray-700 mb-1">Fecha de Vigencia</x-label>
                <p class="text-gray-700">{{ \Carbon\Carbon::parse($vencimientoGarantia)->format('d/m/Y') }}</p>
            @endif

            @if ($editMode)
                <label for="up_vencimiento_garantia" class="text-sm text-gray-600">Fecha de Vigencia</label>
                <input type="date" id="up_vencimiento_garantia" wire:model.defer="up_vencimiento_garantia"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                @error('up_vencimiento_garantia')
                    <span class="text-danger mt-1 d-block">{{ $message }}</span>
                @enderror
            @endif
        </div>

        {{-- Certificado --}}
        <div>
            @if ($certificadoSubido)
                <x-label class="text-sm text-gray-700 mb-1">Certificado (PDF)</x-label>
                <a href="{{ asset('storage/' . $up_cert_garantia) }}" target="_blank"
                    class="inline-block bg-blue-500 text-white font-medium py-2 px-4 rounded shadow hover:bg-blue-600 transition"
                    download>
                    <i class="fas fa-download mr-2"></i> Descargar Certificado
                </a>
            @endif

            @if ($editMode && !$certificadoSubido)
                <label for="cert_garantia" class="text-sm text-gray-600">Certificado (PDF)</label>
                <input type="file" id="cert_garantia" wire:model="cert_garantia" accept="application/pdf"
                    class="mt-1 w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                @error('cert_garantia')
                    <span class="text-danger mt-1 d-block">{{ $message }}</span>
                @enderror
            @endif
        </div>
    </div>

    @if ($garantiaVigente === 'No' && !$editMode)
        <p class="text-gray-500 mt-2">Sin garantía vigente.</p>
    @endif
</div>

<!-- DETALLES DE COMPRA Y GARANTÍA -->
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 flex items-center gap-2">
        <i class="fa-solid fa-receipt text-indigo-600"></i> Detalles de Compra y Garantía <span
            class="text-sm text-gray-500 font-normal">(Opcional)</span>
    </h2>
    <!-- Fecha de Compra -->
    <div class="mb-4">
        <x-label for="fecha_compra" value="Fecha de Compra" />
        <div class="relative">
            <i class="fa-solid fa-calendar-day absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="date" id="fecha_compra" wire:model.live="fecha_compra" max="{{ date('Y-m-d') }}"
                class="pl-10 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
        </div>
        @error('fecha_compra')
            <span class="text-red-600 text-sm mt-1 d-block">{{ $message }}</span>
        @enderror
    </div>

    <!-- Factura de Compra -->
    <div class="mb-4">
        <x-label for="factura_compra" value="Factura de Compra (PDF)" />
        <div class="relative">
            <i class="fa-solid fa-file-invoice-dollar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="file" id="factura_compra" wire:model="factura_compra" accept="application/pdf"
                class="pl-10 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
        </div>
        @error('factura_compra')
            <span class="text-red-600 text-sm mt-1 d-block">{{ $message }}</span>
        @enderror

        @if ($factura_compra)
            <div class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded mt-2">
                Factura seleccionada: {{ $factura_compra->getClientOriginalName() }}
            </div>
        @endif
    </div>

    <!-- Garantía Vigente -->
    <div class="mb-4">
        <x-label for="garantia_vigente" value="¿Garantía Vigente?" />
        <div class="relative">
            <i class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <select id="garantia_vigente" wire:model.lazy="garantia_vigente"
                class="mt-1 block w-full appearance-none pl-10 pr-4 bg-white border border-gray-300 text-gray-700 py-2 rounded-md leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <option value="" hidden>Seleccione</option>
                <option value="Si">Sí</option>
                <option value="No">No</option>
            </select>
        </div>
        @error('garantia_vigente')
            <span class="text-red-600 text-sm mt-1 d-block">{{ $message }}</span>
        @enderror
    </div>

    <!-- Si tiene garantía vigente -->
    @if ($garantia_vigente === 'Si')
        <!-- Certificado de Garantía -->
        <div class="mb-4">
            <x-label for="cert_garantia" value="Certificado de Garantía (PDF)" />
            <div class="relative">
                <i class="fa-solid fa-file-shield absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="file" id="cert_garantia" wire:model="cert_garantia" accept="application/pdf"
                    class="pl-10 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            @error('cert_garantia')
                <span class="text-red-600 text-sm mt-1 d-block">{{ $message }}</span>
            @enderror

            @if ($cert_garantia)
                <div class="bg-blue-100 text-blue-800 text-sm font-medium px-4 py-2 rounded mt-2">
                    Archivo seleccionado: {{ $cert_garantia->getClientOriginalName() }}
                </div>
            @endif
        </div>

        <!-- Fecha de Vencimiento -->
        <div class="mb-4">
            <x-label for="vencimiento_garantia" value="Vencimiento de Garantía" />
            <div class="relative">
                <i class="fa-solid fa-calendar-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="date" id="vencimiento_garantia" wire:model.defer="vencimiento_garantia"
                    @if ($fecha_compra) min="{{ $fecha_compra }}" @endif
                    class="pl-10 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 pr-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            </div>
            @error('vencimiento_garantia')
                <span class="text-red-600 text-sm mt-1 d-block">{{ $message }}</span>
            @enderror
        </div>
    @endif
</div>

@if ($errors->has('step5'))
    <div class="text-red-500 font-semibold">{{ $errors->first('step5') }}</div>
@endif

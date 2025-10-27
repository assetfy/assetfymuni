@php
  $qrPreviewSrc = 'https://quickchart.io/qr?text=Vista%20Previa&size=400';
@endphp

<div x-data="{ previewUrl: null }" class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-3xl font-semibold text-center">Configuración de QR</h1>
  <p class="text-sm text-gray-600 text-center mt-1">Agregá detalles que se verán cuando imprimas los QR de tus bienes.</p>

  <div class="mt-6 grid gap-10 lg:grid-cols-12 items-start">
    {{-- PREVIEW al centro --}}
    <section class="lg:col-span-6 flex justify-center">
      <div class="origin-top" style="transform: scale(.95); transform-origin: top center;">
        <x-qr.label
          :qr-src="$qrPreviewSrc"
          :logo-url="$logoUrl"     {{-- ← sin temporaryUrl() --}}
          :text="$updateDescripcion"
          asset-name="Nombre del bien"
          w="70mm" h="100mm" padding="3mm" space="2mm" qr-size="40mm" :border="true"
        />
      </div>
    </section>

    {{-- FORM a la derecha --}}
    <aside class="lg:col-span-6">
      <form id="form-qr" wire:submit.prevent="guardar" class="space-y-6">
        <div>
          <label class="text-sm font-medium text-gray-800">Texto que aparecerá bajo el QR</label>
          <textarea
            wire:model.defer="updateDescripcion"
            maxlength="200"   {{-- ← alineado con rules --}}
            rows="4"
            class="mt-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 p-3 text-sm"
            placeholder="Ej.: Mensaje de contacto, instrucciones, etc. (máx. 200 caracteres)"></textarea>
          <div class="text-xs text-gray-500 text-right"
               x-data="{t:@entangle('updateDescripcion')}" x-text="`${(t ?? '').length}/200`"></div>
          <x-input-error for="updateDescripcion" class="mt-1 text-sm text-red-500" />
        </div>

        {{-- Input de archivo debajo del texto --}}
        <div>
          <label class="block text-sm font-medium text-gray-800">Logo de la empresa (opcional)</label>

          <input type="file"
                 wire:model="photoAgregar"
                 accept="image/png, image/jpeg, image/webp"
                 @change="previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                 class="mt-2 block w-full text-sm">

          <div class="text-xs text-gray-500 mt-1" wire:loading wire:target="photoAgregar">Subiendo imagen…</div>
          <x-input-error for="photoAgregar" class="mt-1 text-sm text-red-500" />

          {{-- mini preview --}}
          <template x-if="previewUrl">
            <img :src="previewUrl" class="mt-2 h-10 object-contain" alt="Preview logo">
          </template>

          @if($logoUrl)
            <img x-show="!previewUrl" x-cloak src="{{ $logoUrl }}" class="mt-2 h-10 object-contain" alt="Logo actual">
          @endif

          @if($logoUrl)
            <button type="button"
                    wire:click="eliminarFoto"
                    @click="previewUrl = null"
                    class="mt-2 px-3 py-1.5 text-xs rounded-md border border-red-300 text-red-700 bg-white hover:bg-red-50">
              Quitar logo
            </button>
          @endif
        </div>

        <div class="flex gap-3">
          <button type="submit"
                  class="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 disabled:opacity-60"
                  wire:loading.attr="disabled" wire:target="guardar,photoAgregar">
            <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
            <span wire:loading wire:target="guardar">Guardando…</span>
          </button>

          <button type="button"
                  class="px-4 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50"
                  wire:click="resetForm"
                  @click="previewUrl = null">
            Cancelar
          </button>
        </div>
      </form>
    </aside>
  </div>
</div>
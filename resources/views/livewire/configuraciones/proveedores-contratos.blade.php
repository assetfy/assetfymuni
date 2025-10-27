<div>
  <x-dialog-modal wire:model.live="open" maxWidth="2xl">
    <x-slot name="title">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Editar parámetro</h2>
        <button wire:click="closeModal" class="text-red-500 hover:text-red-700">
          <i class="fas fa-times-circle"></i>
        </button>
      </div>
    </x-slot>

    <x-slot name="content">
      <div class="p-2">
        <div class="mt-2">
          <p class="font-medium text-slate-900">Permitir órdenes a proveedores sin contrato</p>
          <div x-data="{ on: @entangle('ordersWithoutContract').live }" class="mt-3 flex items-center gap-3">
            <span class="text-sm text-slate-600">No</span>
            <button
              type="button"
              role="switch"
              :aria-checked="on"
              @click="on = !on"
              :class="on ? 'bg-green-600' : 'bg-slate-300'"
              class="relative inline-flex h-6 w-11 items-center rounded-full transition">
              <span
                :class="on ? 'translate-x-5' : 'translate-x-0'"
                class="inline-block h-5 w-5 transform rounded-full bg-white transition"></span>
            </button>

            <span class="text-sm text-slate-600">Sí</span>
          </div>
          <p class="mt-2 text-sm text-slate-500">
            Cuando está activado, permite crear órdenes de trabajo a proveedores que no tienen un contrato vigente
          </p>
        </div>
    </x-slot>

    <x-slot name="footer">
      <div class="mt-8 flex items-center justify-end gap-3">
        <button type="button" wire:click="closeModal"
          class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          Cancelar
        </button>
        <button type="button" wire:click="save"
          class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:opacity-95">
          Guardar cambios
        </button>
      </div>
    </x-slot>
  </x-dialog-modal>
</div>
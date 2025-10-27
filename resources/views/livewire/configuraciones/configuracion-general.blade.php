<div class="space-y-4">
  {{-- Encabezado de página --}}
  <div>
    <h1 class="text-2xl font-bold text-slate-900">Configuración General</h1>
    <p class="mt-1 text-slate-500">Gestiona los parámetros generales del sistema</p>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="p-6">
      <h2 class="text-lg font-semibold text-slate-900">Órdenes a proveedores</h2>
      <p class="mt-1 text-slate-500">Configuración para el manejo de órdenes a proveedores</p>

      <div class="mt-6 flex items-start justify-between gap-6">
        <div>
          <p class="font-medium text-slate-900">Permitir órdenes a proveedores sin contrato</p>
          <p class="mt-1 text-sm text-slate-500">
            Cuando está activado, permite crear órdenes de trabajo a proveedores que no tienen un contrato vigente
          </p>
        </div>

        <div class="shrink-0 flex items-center gap-3">
          <button type="button"
                  wire:click="openModal"
                  class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-pen text-slate-500"></i> Editar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

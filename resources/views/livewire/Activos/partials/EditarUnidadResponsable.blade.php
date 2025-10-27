<br>
@if ($editMode)
    {{-- Solo en modo edición y si tiene permiso mostramos el dropdown --}}
    <div class="mt-6">
        <div wire:ignore>
            <input type="hidden" id="selectedLevelInput" wire:model="selectedLevel">
            <input id="jstree_search" type="text" placeholder="Buscar nivel…" class="form-control mb-2">
            <div id="jstree_container-edit"
                style="max-height: 300px; overflow: auto; border: 1px solid #ddd; border-radius: 6px; padding: 6px;">
            </div>
        </div>
    </div>
@else
    {{-- Lectura: input deshabilitado igual que en otros campos --}}
    <div class="mb-4">
        <x-label value="Depende de" />
        <div class="relative">
            <i class="fa-solid fa-sitemap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" value="{{ $this->padreNombre ?? 'Sin Dependencia' }}" disabled
                class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md pl-10 pr-4 py-2 text-gray-600 cursor-not-allowed" />
        </div>
    </div>
@endif

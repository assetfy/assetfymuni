<x-dialog-modal wire:model.live="open" maxWidth="2xl">
    <x-slot name="title">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold">Editar Nivel Dependencia</h2>
            <button wire:click="close" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </x-slot>

    <x-slot name="content">
        <div class="mt-6">
            <div wire:ignore>
                <input type="hidden" id="selectedLevelInput" wire:model="selectedLevel">
                <input id="jstree_search" type="text" placeholder="Buscar nivel…" class="form-control mb-2">
                <div id="jstree_container-edit"
                    style="max-height: 300px; overflow: auto; border: 1px solid #ddd; border-radius: 6px; padding: 6px;">
                </div>
            </div>
        </div>

        <div class="mb-4">
            <x-label value="Depende de" />
            <div class="relative">
                <i class="fa-solid fa-sitemap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" value="{{ $this->padreNombre ?? 'Sin Dependencia' }}" disabled
                    class="mt-1 block w-full bg-gray-100 border-gray-300 rounded-md pl-10 pr-4 py-2 text-gray-600 cursor-not-allowed" />
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        @if($esCargaMasiva)
        <div class="flex justify-end space-x-2">
            <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
            <x-danger-button wire:click="seleccionarParaCargaMasiva" wire:loading.attr="disabled">
                Seleccionar
            </x-danger-button>
        </div>
        @else
        <div class="flex justify-end space-x-2">
            <x-secondary-button wire:click="close">Cancelar</x-secondary-button>
            <x-danger-button wire:click="guardar" wire:loading.attr="disabled">
                Guardar
            </x-danger-button>
        </div>
        @endif
    </x-slot>
</x-dialog-modal>
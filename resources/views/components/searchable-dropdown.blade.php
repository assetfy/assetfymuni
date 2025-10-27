@props([
    'label', // Texto de la etiqueta ("Tipo", "Categoría", ...)
    'icon', // Clase FontAwesome p.ej. 'fa-solid fa-tags'
    'options' => [], // Colección de items (p.ej. $tipoPrueba)
    'model', // Propiedad Livewire p.ej. 'id_tipo'
    'searchModel', // Propiedad Livewire p.ej. 'searchTipo'
    'selectMethod', // Método Livewire p.ej. 'setTipo'
    'valueKey', // Clave del valor p.ej. 'id_tipo'
    'labelKey', // Clave del texto p.ej. 'nombre'
    'selected' => null, // Valor actualmente seleccionado (p.ej. $selectedTipoNombre)
])

<div class="mb-4" x-data="{ open: false }" @click.away="open = false">
    {{-- Etiqueta --}}
    <x-label :value="$label" class="text-sm font-semibold text-gray-700 mb-1" />

    <div class="relative">
        {{-- Icono --}}
        <i class="{{ $icon }} absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>

        {{-- Botón que muestra la selección actual --}}
        <button type="button" @click="open = !open"
            class="w-full bg-white border border-gray-300 rounded-md shadow-sm pl-10 pr-4 py-2 flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            <span>{{ $selected ?: "Seleccione $label" }}</span>
            <i class="fas fa-chevron-down text-gray-400"></i>
        </button>

        {{-- Panel desplegable --}}
        <div x-show="open" x-transition class="absolute mt-1 w-full bg-white rounded-md shadow-lg z-20">
            <div class="p-2 relative">
                {{-- Input de búsqueda --}}
                <input type="text" wire:model.live="{{ $searchModel }}" @keydown.escape="open = false"
                    class="w-full border border-gray-300 rounded px-2 py-1 mb-2 focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="Buscar…">
                <span wire:loading wire:target="{{ $searchModel }}" class="absolute right-3 top-3 text-gray-500">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </div>

            <ul class="max-h-40 overflow-auto divide-y divide-gray-100">
                @forelse($options as $opt)
                    <li @click="open = false" wire:click="{{ $selectMethod }}({{ $opt->{$valueKey} }})"
                        class="px-3 py-2 hover:bg-blue-100 cursor-pointer">
                        {{ $opt->{$labelKey} }}
                    </li>
                @empty
                    <li class="px-3 py-2 text-gray-500">No se encontraron resultados.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Campo oculto para enlazar el id seleccionado a Livewire --}}
    <input type="hidden" wire:model="{{ $model }}" />
    <x-input-error :for="$model" class="mt-1" />
</div>

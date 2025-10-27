<div class="px-3 py-2">
    <span class="block whitespace-nowrap truncate" title="{{ $filter->label() }}">
        {{ $filter->label() }}
    </span>
    <div class="flex flex-col gap-1">
        @if($filter->code() == 'fecha_inicio' || $filter->code() == 'fechaHora')
        <div>
            <input type="date"
                   class="w-full border border-neutral-200 rounded-md shadow-sm outline-none bg-white text-black focus:border-blue-300 mr-auto px-3 py-2 font-normal transition ease-in-out dark:bg-neutral-900 dark:border-neutral-700 dark:focus:border-blue-600 dark:text-white"
                   wire:model.live="filters.{{ $filter->code() }}.from">
        </div>
        @endif
        @if($filter->code() == 'fecha_fin')
        <div>
            <input type="date"
                   class="w-full border border-neutral-200 rounded-md shadow-sm outline-none bg-white text-black focus:border-blue-300 mr-auto px-3 py-2 font-normal transition ease-in-out dark:bg-neutral-900 dark:border-neutral-700 dark:focus:border-blue-600 dark:text-white"
                   wire:model.live="filters.{{ $filter->code() }}.to">
        </div>
        @endif
    </div>
</div>

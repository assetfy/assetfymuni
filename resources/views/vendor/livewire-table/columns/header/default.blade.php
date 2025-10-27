@if($column->isSortable())
<a href="#"
    class="flex items-center gap-1 font-bold text-md px-3 py-2 whitespace-nowrap"
    wire:click.prevent="sort('{{ $column->code() }}')">
    <span class="text-black">{{ $column->label() }}</span>
    @if(! $this->isReordering())

    @php
    $isSorted = $this->sortColumn === $column->code();
    $isAsc = $this->sortDirection === 'asc';
    @endphp
    <span>
        @if($isSorted && $isAsc)
        <!-- Icon "chevron-up" -->
        <strong><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24"
            stroke-width="4.5" stroke="black">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg></strong>
        @elseif($isSorted)
        <!-- Icon "chevron-down" -->
        <strong><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24"
            stroke-width="4.5" stroke="black">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg></strong>
        @else
        <!-- Icon placeholder (hidden but keeps layout and tooltip) -->
        <strong><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-black" fill="none" viewBox="0 0 24 24"
            stroke-width="4.5" stroke="black">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg></strong>
        @endif
    </span>
    @endif
</a>
@else
<span class="flex font-bold text-md px-3 py-2 whitespace-nowrap text-black">{{ $column->label() }}</span>
@endif
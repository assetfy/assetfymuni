
<div class="flex flex-col gap-3 relative"
    wire:init="init"
    @if(strlen($polling = $this->polling()) > 0) wire:poll.{{ $polling }} @endif
>
<br>
    @include('livewire-table::bar.bar')
    
    <!-- Ajustes para el comportamiento de colapso de la tabla -->
    <div class="bg-white border border-neutral-200 dark:bg-neutral-900 dark:border-neutral-700 shadow-sm rounded-md">
        <div class="overflow-x-auto overscroll-x-none">
            @include('livewire-table::table.table')
        </div>
    </div>

    <!-- Paginación -->
    {{ $paginator->links('livewire-table::pagination.pagination') }}
</div>

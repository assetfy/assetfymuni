<div class="overflow-x-auto overflow-y-auto max-h-[475px] xl:max-h-[70vh] w-screen md:w-full">
    <table class="min-w-full table-auto leading-normal shadow rounded-lg w-full relative" x-data="{ selected: @entangle('selected') }">
        <thead class="border-b border-gray-200 bg-gray-100">
            <tr class="group">
                @if ($this->canSelect())
                    <th
                        class="sticky top-0 left-0 px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-100 z-0">
                        <input type="checkbox" wire:model.live="selectedPage"
                            class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                    </th>
                @endif

                @foreach ($table['columns'] as $column)
                    @continue(!in_array($column->code(), $this->columns))
                    <th
                        class="sticky top-0 px-2 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-100 z-0">
                        {{ $column->renderHeader() }}
                    </th>
                @endforeach
            </tr>

            <tr class="group">
                @if ($this->canSelect())
                    <th class="sticky top-[38px] left-0 px-2 py-2 text-left bg-gray-100 z-0"></th>
                @endif

                @foreach ($table['columns'] as $column)
                    @continue(!in_array($column->code(), $this->columns))
                    <th class="sticky top-[38px] px-2 py-2 text-left text-xs text-gray-600 bg-gray-100 z-0">
                        @if ($column->isSearchable())
                            {{ $column->renderSearch() }}
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @if ($this->deferLoading && !$this->initialized)
                <tr class="group">
                    <td colspan="{{ $table['columns']->count() + ($this->canSelect() ? 1 : 0) }}"
                        class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        @lang('Fetching records...')
                    </td>
                </tr>
            @else
                @forelse($paginator->items() as $item)
                    <tr class="group border-b border-gray-200 bg-white hover:bg-gray-100"
                        wire:key="row-{{ $item->getKey() }}"
                        @if ($this->isReordering()) draggable="true"
                            x-on:dragstart="e => e.dataTransfer.setData('key', '{{ $item->getKey() }}')"
                            x-on:dragover.prevent=""
                            x-on:drop="e => {
                                $wire.call(
                                    'reorderItem',
                                    e.dataTransfer.getData('key'),
                                    '{{ $item->getKey() }}',
                                    e.target.offsetHeight / 2 > e.offsetY
                                )
                            }" @endif>
                        @if ($this->canSelect())
                            <td class="px-2 py-2 text-left">
                                <input type="checkbox" wire:model.live="selected" value="{{ $item->getKey() }}"
                                    class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                            </td>
                        @endif

                        @foreach ($table['columns'] as $column)
                            @continue(!in_array($column->code(), $this->columns))
                            @include('components.table-cell', ['column' => $column, 'item' => $item])
                        @endforeach
                    </tr>
                @empty
                    <tr class="group">
                        <td colspan="{{ $table['columns']->count() + ($this->canSelect() ? 1 : 0) }}"
                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            @lang('Sin Resultados')
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>

        <tfoot class="border-t border-gray-200 bg-gray-100">
            <tr class="group">
                @if ($this->canSelect())
                    <th class="px-2 py-2 text-left bg-gray-100"></th>
                @endif

                @foreach ($table['columns'] as $column)
                    @continue(!in_array($column->code(), $this->columns))
                    <th class="px-2 py-2 text-left text-xs text-gray-600 bg-gray-100">
                        {{ $column->renderFooter() }}
                    </th>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>

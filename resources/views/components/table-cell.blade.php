<td class="px-2 py-2 text-left text-sm text-gray-700 cursor-pointer select-none bg-white"
    @if ($column->isClickable() && !$this->isReordering()) @if (($link = $this->link($item)) !== null)
        x-on:click.prevent="window.location.href = '{{ $link }}'"
      @elseif($this->canSelect())
        x-on:click="$wire.selectItem('{{ $item->getKey() }}')" @endif
    @endif
    :class="{ 'hover:bg-gray-100': !(selected && selected.includes('{{ $item->getKey() }}')) }">
    {{ $column->render($item) }}
</td>

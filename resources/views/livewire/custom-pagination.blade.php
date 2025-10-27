@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination" class="inline-flex items-center gap-1">
    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="px-3 py-2 text-sm rounded border cursor-not-allowed opacity-50 select-none">&lsaquo;</span>
    @else
        <button
            wire:click="previousPage('{{ $paginator->getPageName() }}')"
            wire:loading.attr="disabled"
            rel="prev"
            class="px-3 py-2 text-sm rounded border hover:bg-gray-50">
            &lsaquo;
        </button>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-3 py-2 text-sm text-gray-500 select-none">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="px-3 py-2 text-sm border rounded bg-gray-900 text-white select-none">{{ $page }}</span>
                @else
                    <button
                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                        class="px-3 py-2 text-sm border rounded hover:bg-gray-50">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button
            wire:click="nextPage('{{ $paginator->getPageName() }}')"
            rel="next"
            class="px-3 py-2 text-sm rounded border hover:bg-gray-50">
            &rsaquo;
        </button>
    @else
        <span class="px-3 py-2 text-sm rounded border cursor-not-allowed opacity-50 select-none">&rsaquo;</span>
    @endif
</nav>
@endif

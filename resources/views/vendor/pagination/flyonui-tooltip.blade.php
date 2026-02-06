@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-center">
        <div class="join">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="btn btn-sm join-item" disabled aria-disabled="true">
                    Previous
                </button>
            @else
                <a
                    class="btn btn-sm join-item"
                    href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                >
                    Previous
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <button class="btn btn-sm join-item btn-disabled" disabled>
                        {{ $element }}
                    </button>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="btn btn-sm join-item btn-primary" aria-current="page">
                                {{ $page }}
                            </button>
                        @else
                            {{-- Tooltip wrapper --}}
                            <div class="tooltip" data-tip="Go to page {{ $page }}">
                                <a class="btn btn-sm join-item" href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a
                    class="btn btn-sm join-item"
                    href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                >
                    Next
                </a>
            @else
                <button class="btn btn-sm join-item" disabled aria-disabled="true">
                    Next
                </button>
            @endif

        </div>
    </nav>
@endif

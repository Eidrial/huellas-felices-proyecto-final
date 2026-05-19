@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-center mt-8">
        <div class="flex items-center gap-2 bg-white border border-[#d9ddd0] rounded-2xl px-3 py-2">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm rounded-xl text-[#c0bdb8] cursor-not-allowed">
                    Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="px-3 py-2 text-sm rounded-xl text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200">
                    Anterior
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)

                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-2 py-2 text-sm text-[#8a8e84]">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 text-sm rounded-xl bg-[#3a7a2e] text-[#f0ede6]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                                class="px-3 py-2 text-sm rounded-xl text-[#1e2e1a] hover:bg-[#eef5e8] hover:text-[#2d5a27] transition-colors duration-200">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="px-3 py-2 text-sm rounded-xl text-[#2d5a27] hover:bg-[#eef5e8] transition-colors duration-200">
                    Siguiente
                </a>
            @else
                <span class="px-3 py-2 text-sm rounded-xl text-[#c0bdb8] cursor-not-allowed">
                    Siguiente
                </span>
            @endif
        </div>
    </nav>
@endif
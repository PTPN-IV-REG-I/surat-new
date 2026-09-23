@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1 text-xs">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-slate-400 cursor-not-allowed">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Sebelumnya</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                <span>Sebelumnya</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="px-2 py-1 text-slate-400">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[#033F63] font-bold text-white shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded-lg font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 font-medium text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                <span>Selanjutnya</span>
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <span class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-slate-400 cursor-not-allowed">
                <span>Selanjutnya</span>
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif
    </nav>
@endif

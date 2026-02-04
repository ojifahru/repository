@if ($paginator->hasPages())
    <nav class="ml-auto flex w-fit max-w-full flex-col gap-3 sm:flex-row sm:items-center sm:gap-4" role="navigation"
        aria-label="Pagination">
        <div class="text-right text-xs text-gray-600 sm:text-left">
            <span class="font-medium text-gray-900">{{ $paginator->firstItem() ?? 0 }}</span>
            <span class="text-gray-400">–</span>
            <span class="font-medium text-gray-900">{{ $paginator->lastItem() ?? 0 }}</span>
            <span class="text-gray-400">dari</span>
            <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
        </div>

        <div class="flex items-center justify-end gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Previous page"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white/70 px-4 py-2 text-sm font-semibold text-gray-400 shadow-sm ring-1 ring-gray-900/5">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.79 15.77a.75.75 0 0 1-1.06.02l-4.24-4.24a.75.75 0 0 1 0-1.06l4.24-4.24a.75.75 0 1 1 1.06 1.06L9.06 10l3.73 3.71a.75.75 0 0 1 0 1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white/80 px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.79 15.77a.75.75 0 0 1-1.06.02l-4.24-4.24a.75.75 0 0 1 0-1.06l4.24-4.24a.75.75 0 1 1 1.06 1.06L9.06 10l3.73 3.71a.75.75 0 0 1 0 1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden items-center gap-1 sm:flex">
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $startPage = max(2, $currentPage - 1);
                    $endPage = min($lastPage - 1, $currentPage + 1);
                @endphp

                {{-- First Page --}}
                @if ($lastPage >= 1)
                    @if ($currentPage === 1)
                        <span aria-current="page"
                            class="inline-flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-semibold text-white shadow-sm ring-1 ring-indigo-600">1</span>
                    @else
                        <a href="{{ $paginator->url(1) }}" aria-label="Go to page 1"
                            class="inline-flex size-10 items-center justify-center rounded-2xl bg-white/80 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white hover:ring-indigo-500/20 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">1</a>
                    @endif
                @endif

                {{-- Left Ellipsis --}}
                @if ($currentPage > 3)
                    <span aria-disabled="true" class="px-2 text-sm font-semibold text-gray-400">…</span>
                @endif

                {{-- Middle Pages --}}
                @for ($page = $startPage; $page <= $endPage; $page++)
                    @if ($page >= 2 && $page <= $lastPage - 1)
                        @if ($page === $currentPage)
                            <span aria-current="page"
                                class="inline-flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-semibold text-white shadow-sm ring-1 ring-indigo-600">{{ $page }}</span>
                        @else
                            <a href="{{ $paginator->url($page) }}" aria-label="Go to page {{ $page }}"
                                class="inline-flex size-10 items-center justify-center rounded-2xl bg-white/80 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white hover:ring-indigo-500/20 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">{{ $page }}</a>
                        @endif
                    @endif
                @endfor

                {{-- Right Ellipsis --}}
                @if ($currentPage < $lastPage - 2)
                    <span aria-disabled="true" class="px-2 text-sm font-semibold text-gray-400">…</span>
                @endif

                {{-- Last Page --}}
                @if ($lastPage > 1)
                    @if ($currentPage === $lastPage)
                        <span aria-current="page"
                            class="inline-flex size-10 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-semibold text-white shadow-sm ring-1 ring-indigo-600">{{ $lastPage }}</span>
                    @else
                        <a href="{{ $paginator->url($lastPage) }}" aria-label="Go to page {{ $lastPage }}"
                            class="inline-flex size-10 items-center justify-center rounded-2xl bg-white/80 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white hover:ring-indigo-500/20 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">{{ $lastPage }}</a>
                    @endif
                @endif
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white/80 px-4 py-2 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <span>Berikutnya</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.21 4.23a.75.75 0 0 1 1.06-.02l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 1 1-1.06-1.06L10.94 10 7.21 6.29a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Next page"
                    class="inline-flex items-center gap-2 rounded-2xl bg-white/70 px-4 py-2 text-sm font-semibold text-gray-400 shadow-sm ring-1 ring-gray-900/5">
                    <span>Berikutnya</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.21 4.23a.75.75 0 0 1 1.06-.02l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 1 1-1.06-1.06L10.94 10 7.21 6.29a.75.75 0 0 1 0-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif

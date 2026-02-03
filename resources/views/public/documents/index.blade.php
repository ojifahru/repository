<x-public-layouts.app :seo="$seo" title="Dokumen">
    <form action="{{ route('public.documents.index') }}" method="get">
        <div class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Dokumen</h1>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        Hanya dokumen yang sudah dipublikasikan. Gunakan pencarian dan filter untuk mempersempit hasil.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
                    <span class="rounded-full bg-white/70 px-3 py-1 shadow-sm ring-1 ring-gray-900/5">
                        {{ $documents->total() }} hasil
                    </span>

                    <button
                        class="inline-flex items-center justify-center rounded-full bg-white/80 px-4 py-2 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15 md:hidden"
                        type="button" data-drawer-open="doc-filters">
                        Filter
                    </button>
                </div>
            </div>

            <div class="mt-6">
                <label class="sr-only" for="q">Cari dokumen</label>
                <div class="mx-auto max-w-4xl">
                    <div
                        class="relative rounded-3xl bg-white shadow-lg shadow-indigo-500/10 ring-1 ring-gray-900/5 focus-within:ring-4 focus-within:ring-indigo-500/15">
                        <div class="flex flex-col gap-2 p-2 sm:flex-row sm:items-center">
                            <div class="relative w-full">
                                <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.2 4.31l3.24 3.25a1 1 0 0 1-1.42 1.41l-3.25-3.24A7 7 0 0 1 2 9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <input id="q" type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                    placeholder="Cari judul, abstrak, author…"
                                    class="w-full rounded-2xl border-0 bg-transparent py-4 pl-11 pr-4 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0">
                            </div>

                            <button
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:w-auto"
                                type="submit">
                                Cari
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 text-xs text-gray-600">
                        Tip: gunakan sidebar filter untuk mempersempit hasil.
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed inset-0 z-[60] hidden md:hidden" data-drawer="doc-filters" role="dialog" aria-modal="true"
            aria-labelledby="docFiltersTitle">
            <button class="absolute inset-0 bg-gray-900/30" type="button" data-drawer-close="doc-filters"
                aria-label="Tutup filter"></button>

            <div
                class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl ring-1 ring-gray-900/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div id="docFiltersTitle" class="text-base font-semibold text-gray-900">Filter Dokumen</div>
                        <div class="mt-0.5 text-xs text-gray-600">Gunakan metadata untuk mempersempit hasil.</div>
                    </div>
                    <button
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-gray-800 ring-1 ring-gray-900/5 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                        type="button" data-drawer-close="doc-filters">
                        Tutup
                    </button>
                </div>

                <div class="mt-5 rounded-2xl bg-white">
                    @include('public.documents._filters')
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 md:grid-cols-[1fr_20rem] md:items-start">
            <div>
                <div class="grid gap-4 md:grid-cols-2">
                    @forelse($documents as $doc)
                        <a href="{{ route('public.repository.show', $doc) }}"
                            class="group rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md hover:ring-indigo-500/15 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div
                                        class="text-base font-semibold leading-snug text-gray-900 group-hover:underline">
                                        {{ $doc->title ?? 'Untitled' }}
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        {{ $doc->authors->pluck('name')->implode(', ') ?: '-' }}
                                    </div>
                                </div>
                                <x-ui.badge variant="primary" class="border-indigo-200 bg-indigo-50 text-indigo-800">
                                    {{ $doc->publish_year ?? '-' }}
                                </x-ui.badge>
                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($doc->category)
                                    <x-ui.badge
                                        class="border-0 bg-gray-100 text-gray-700">{{ $doc->category->name }}</x-ui.badge>
                                @endif
                                @if ($doc->documentType)
                                    <x-ui.badge
                                        class="border-0 bg-gray-100 text-gray-700">{{ $doc->documentType->name }}</x-ui.badge>
                                @endif
                                @if ($doc->faculty)
                                    <x-ui.badge
                                        class="border-0 bg-gray-100 text-gray-700">{{ $doc->faculty->name }}</x-ui.badge>
                                @endif
                            </div>

                            @php
                                $abstractPreview = trim(strip_tags((string) $doc->abstract));
                            @endphp
                            @if ($abstractPreview !== '')
                                <p class="mt-4 text-sm leading-relaxed text-gray-700">
                                    {{ \Illuminate\Support\Str::limit($abstractPreview, 220) }}
                                </p>
                            @endif

                            <div class="mt-5 flex items-center justify-between text-xs text-gray-500">
                                <span>Download: {{ $doc->download_count ?? 0 }}</span>
                                <span class="inline-flex items-center gap-1 font-semibold text-indigo-700">
                                    Detail
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.06-.02Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/80 p-8 text-sm text-gray-600 shadow-sm ring-1 ring-gray-900/5">
                            Tidak ada hasil.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $documents->links('pagination.public') }}
                </div>
            </div>

            <aside class="hidden md:block">
                <div class="sticky top-24 rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-gray-900/5">
                    @include('public.documents._filters')
                </div>
            </aside>
        </div>
    </form>
</x-public-layouts.app>

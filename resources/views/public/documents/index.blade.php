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
                    @include('public.documents._filters', ['scope' => 'drawer'])
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-8 md:grid-cols-[1fr_20rem] md:items-start">
            <div>
                <div class="grid gap-5 md:grid-cols-2">
                    @forelse($documents as $doc)
                        <x-ui.card
                            class="group flex h-full flex-col rounded-3xl border border-slate-200/70 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md focus-within:ring-4 focus-within:ring-indigo-500/15">
                            @php
                                $filterQuery = request()->except('page');
                            @endphp

                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <a href="{{ route('public.repository.show', $doc) }}"
                                        class="block focus:outline-none">
                                        <h3
                                            class="line-clamp-2 text-lg font-semibold leading-snug text-slate-900 group-hover:text-indigo-700 group-hover:underline">
                                            {{ $doc->title ?? 'Untitled' }}
                                        </h3>
                                    </a>

                                    <p class="mt-1 line-clamp-1 text-sm font-medium text-slate-700">
                                        @forelse ($doc->authors as $author)
                                            <a href="{{ route('public.documents.index', array_merge($filterQuery, ['author_id' => $author->id])) }}"
                                                class="hover:text-indigo-700 hover:underline focus:outline-none">
                                                {{ $author->name }}
                                            </a>
                                            @if (!$loop->last)
                                                ,
                                            @endif
                                        @empty
                                            -
                                        @endforelse
                                    </p>
                                </div>

                                {{-- Tahun (dipisah, bukan tag) --}}
                                @if ($doc->publish_year)
                                    <a href="{{ route('public.documents.index', array_merge($filterQuery, ['publish_year' => $doc->publish_year])) }}"
                                        class="shrink-0 focus:outline-none">
                                        <x-ui.badge class="border-slate-200 bg-slate-50 text-slate-700">
                                            {{ $doc->publish_year }}
                                        </x-ui.badge>
                                    </a>
                                @else
                                    <x-ui.badge
                                        class="shrink-0 border-slate-200 bg-slate-50 text-slate-700">-</x-ui.badge>
                                @endif
                            </div>

                            {{-- TAG (tanpa tahun) --}}
                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                @if ($doc->category)
                                    <a href="{{ route('public.documents.index', array_merge($filterQuery, ['category_id' => $doc->category->id])) }}"
                                        class="focus:outline-none">
                                        <x-ui.badge
                                            class="max-w-[14rem] cursor-pointer truncate border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200">
                                            {{ $doc->category->name }}
                                        </x-ui.badge>
                                    </a>
                                @endif

                                @if ($doc->documentType)
                                    <a href="{{ route('public.documents.index', array_merge($filterQuery, ['document_type_id' => $doc->documentType->id])) }}"
                                        class="focus:outline-none">
                                        <x-ui.badge
                                            class="max-w-[14rem] cursor-pointer truncate border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                            {{ $doc->documentType->name }}
                                        </x-ui.badge>
                                    </a>
                                @endif

                                @if ($doc->faculty)
                                    <a href="{{ route('public.documents.index', array_merge($filterQuery, ['faculty_id' => $doc->faculty->id])) }}"
                                        class="focus:outline-none">
                                        <x-ui.badge
                                            class="max-w-[14rem] cursor-pointer truncate border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                            {{ $doc->faculty->name }}
                                        </x-ui.badge>
                                    </a>
                                @endif

                                @if ($doc->studyProgram)
                                    <a href="{{ route('public.documents.index', array_merge($filterQuery, ['study_program_id' => $doc->studyProgram->id])) }}"
                                        class="focus:outline-none">
                                        <x-ui.badge
                                            class="max-w-[14rem] cursor-pointer truncate border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                                            {{ $doc->studyProgram->name }}
                                        </x-ui.badge>
                                    </a>
                                @endif
                            </div>

                            {{-- Abstract --}}
                            @php
                                $abstractPreview = trim(strip_tags((string) $doc->abstract));
                            @endphp
                            @if ($abstractPreview !== '')
                                <p class="mt-4 line-clamp-4 text-sm leading-relaxed text-slate-700">
                                    {{ \Illuminate\Support\Str::limit($abstractPreview, 200) }}
                                </p>
                            @endif

                            {{-- Footer --}}
                            <div
                                class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4 text-xs text-slate-500">
                                <span class="inline-flex items-center gap-1">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                                        <path
                                            d="M10 2a1 1 0 0 1 1 1v7.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 6.7 8.3L9 10.59V3a1 1 0 0 1 1-1Z" />
                                        <path
                                            d="M4 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a1 1 0 1 0-2 0v1H6v-1a1 1 0 1 0-2 0v1Z" />
                                    </svg>
                                    {{ $doc->download_count ?? 0 }} unduhan
                                </span>
                                <a href="{{ route('public.repository.show', $doc) }}"
                                    class="inline-flex items-center gap-1 font-medium text-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 rounded-xl px-2 py-1 -mr-2">
                                    Lihat Detail
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-4" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75.75 0 0 1-1.06-.02Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </x-ui.card>
                    @empty
                        <div class="rounded-3xl bg-white/80 p-8 text-sm text-slate-600 shadow-sm ring-1 ring-slate-200">
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
                    @include('public.documents._filters', ['scope' => 'sidebar'])
                </div>
            </aside>
        </div>
    </form>
</x-public-layouts.app>

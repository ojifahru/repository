<x-public-layouts.app :seo="$seo" :title="config('app.name')">
    <section
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-50/70 via-white to-white p-6 sm:p-10">
        <div class="pointer-events-none absolute -left-28 -top-24 size-72 rounded-full bg-indigo-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-28 -right-24 size-72 rounded-full bg-indigo-500/10 blur-3xl">
        </div>

        <div class="relative mx-auto max-w-3xl text-center">
            <div
                class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-1 text-xs font-medium text-gray-600 shadow-sm">
                <span class="size-1.5 rounded-full bg-indigo-600"></span>
                <span>Repository Institusi</span>
            </div>

            <h1 class="mt-4 text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
                Repository Institusi
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-gray-600 sm:text-[15px]">
                Pusat dokumen TriDharma kampus yang mudah ditelusuri — judul, abstrak, dan author.
            </p>

            <form class="mt-8" action="{{ route('public.documents.index') }}" method="get">
                <div class="mx-auto max-w-2xl">
                    <label class="sr-only" for="q">Cari dokumen</label>
                    <div
                        class="group relative rounded-3xl bg-white shadow-lg shadow-indigo-500/10 ring-1 ring-gray-900/5 focus-within:ring-4 focus-within:ring-indigo-500/15">
                        <div class="flex flex-col gap-2 p-2 sm:flex-row sm:items-center">
                            <div class="relative w-full">
                                <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.2 4.31l3.24 3.25a1 1 0 0 1-1.42 1.41l-3.25-3.24A7 7 0 0 1 2 9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>

                                <input id="q" type="text" name="q" value="{{ request('q') }}"
                                    placeholder="Cari judul, abstrak, atau author…"
                                    class="w-full rounded-2xl border-0 bg-transparent py-4 pl-11 pr-4 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0">
                            </div>

                            <button
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:w-auto"
                                type="submit">
                                Telusuri Dokumen
                            </button>
                        </div>
                    </div>

                    <div class="mx-auto mt-3 flex flex-wrap justify-center gap-2 text-xs text-gray-600">
                        <x-ui.badge class="border-0 bg-white/70 text-gray-700">Search cepat</x-ui.badge>
                        <x-ui.badge class="border-0 bg-white/70 text-gray-700">Filter lengkap di /dokumen</x-ui.badge>
                        <x-ui.badge class="border-0 bg-white/70 text-gray-700">Download via controller</x-ui.badge>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="mt-10">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card class="border-0 bg-white/80 p-6 shadow-sm hover:shadow-md">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                            <path
                                d="M4 3.5A2.5 2.5 0 0 1 6.5 1H15a2 2 0 0 1 2 2v11.5A2.5 2.5 0 0 1 14.5 17H6.5A2.5 2.5 0 0 1 4 14.5V3.5Z" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Dokumen</div>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-gray-900">{{ $stats['documents'] }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">Terpublikasi</div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="border-0 bg-white/80 p-6 shadow-sm hover:shadow-md">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                            <path d="M10 10a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm-7 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Author</div>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-gray-900">{{ $stats['authors'] }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">Terdaftar</div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="border-0 bg-white/80 p-6 shadow-sm hover:shadow-md">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                            <path d="M3 8.5 10 4l7 4.5v8A1.5 1.5 0 0 1 15.5 18h-11A1.5 1.5 0 0 1 3 16.5v-8Z" />
                        </svg>
                    </span>
                    <div>
                        <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Fakultas</div>
                        <div class="mt-1 text-3xl font-semibold tabular-nums text-gray-900">{{ $stats['faculties'] }}
                        </div>
                        <div class="mt-1 text-xs text-gray-500">Aktif</div>
                    </div>
                </div>
            </x-ui.card>

            @if (isset($stats['downloads']))
                <x-ui.card class="border-0 bg-white/80 p-6 shadow-sm hover:shadow-md">
                    <div class="flex items-start gap-3">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700">
                            <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                                <path
                                    d="M10 2a1 1 0 0 1 1 1v7.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 6.7 8.3L9 10.59V3a1 1 0 0 1 1-1Z" />
                                <path
                                    d="M4 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a1 1 0 1 0-2 0v1H6v-1a1 1 0 1 0-2 0v1Z" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Download</div>
                            <div class="mt-1 text-3xl font-semibold tabular-nums text-gray-900">
                                {{ $stats['downloads'] }}</div>
                            <div class="mt-1 text-xs text-gray-500">Total</div>
                        </div>
                    </div>
                </x-ui.card>
            @endif
        </div>
    </section>

    <section class="mt-12">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900">Dokumen Terbaru</h2>
                <p class="mt-1 text-sm text-gray-600">Kumpulan dokumen yang baru dipublikasikan.</p>
            </div>
            <a class="inline-flex items-center justify-center rounded-2xl bg-white/80 px-5 py-3 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                href="{{ route('public.documents.index') }}">
                Lihat semua
            </a>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($latestDocuments->take(6) as $doc)
                <a href="{{ route('public.repository.show', $doc) }}"
                    class="group rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md hover:ring-indigo-500/15 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-base font-semibold leading-snug text-gray-900 group-hover:underline">
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

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if ($doc->category?->name)
                            <x-ui.badge class="border-0 bg-gray-100 text-gray-700">
                                {{ $doc->category?->name ?? '' }}
                            </x-ui.badge>
                        @endif
                        <span class="ml-auto inline-flex items-center gap-1 text-sm font-semibold text-indigo-700">
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
                    Belum ada dokumen yang dipublikasikan.
                </div>
            @endforelse
        </div>
    </section>
</x-public-layouts.app>

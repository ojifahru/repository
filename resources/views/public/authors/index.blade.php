<x-public-layouts.app :seo="$seo" title="Penulis">
    <form action="{{ route('public.authors.index') }}" method="get">
        <section class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Penulis</h1>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">
                        Daftar penulis dengan publikasi terbit di repository. Gunakan pencarian untuk menemukan nama
                        lebih cepat.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
                    <span class="rounded-full bg-white/70 px-3 py-1 shadow-sm ring-1 ring-gray-900/5">
                        {{ $authors->total() }} hasil
                    </span>
                </div>
            </div>

            <div class="mt-6">
                <label class="sr-only" for="q">Cari penulis</label>

                <div class="mx-auto max-w-3xl">
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

                                <input id="q" type="text" name="q" value="{{ $search ?? '' }}"
                                    placeholder="Cari nama atau ID…"
                                    class="w-full rounded-2xl border-0 bg-transparent py-4 pl-11 pr-4 text-sm text-gray-900 outline-none placeholder:text-gray-400 focus:ring-0">
                            </div>

                            <button
                                class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-6 py-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 sm:w-auto"
                                type="submit">
                                Cari
                            </button>
                        </div>
                    </div>

                    @if (!empty($search))
                        <div class="mt-3 text-xs text-gray-600">
                            <a class="font-semibold text-gray-700 hover:text-gray-900 hover:underline"
                                href="{{ route('public.authors.index') }}">Reset pencarian</a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </form>

    <section class="mt-8">
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($authors as $author)
                @php
                    $authorImageUrl = null;
                    if (!empty($author->image_url) && \Illuminate\Support\Facades\Storage::disk('public')->exists($author->image_url)) {
                        $authorImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($author->image_url);
                    }
                @endphp
                <a href="{{ route('public.authors.show', $author) }}"
                    class="group rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md hover:ring-indigo-500/15 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex size-12 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5">
                            @if ($authorImageUrl)
                                <img class="h-full w-full object-cover" src="{{ $authorImageUrl }}"
                                    alt="Foto {{ $author->name }}">
                            @else
                                <span
                                    class="text-lg font-semibold text-indigo-700">{{ mb_substr($author->name, 0, 1) }}</span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="text-base font-semibold leading-snug text-gray-900 group-hover:underline">
                                {{ $author->name }}
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $author->published_documents_count ?? 0 }} dokumen terbit
                            </div>

                            @if (!empty($author->identifier))
                                <div class="mt-3 text-xs text-gray-500">
                                    ID: {{ $author->identifier }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl bg-white/80 p-8 text-sm text-gray-600 shadow-sm ring-1 ring-gray-900/5">
                    Tidak ada penulis yang cocok.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $authors->links('pagination.public') }}
        </div>
    </section>
</x-public-layouts.app>

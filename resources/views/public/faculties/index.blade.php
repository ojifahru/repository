<x-public-layouts.app :seo="$seo" title="Fakultas">
    <section class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Fakultas</h1>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">
                    Telusuri fakultas untuk melihat program studi dan dokumen repository yang terbit.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
                <span class="rounded-full bg-white/70 px-3 py-1 shadow-sm ring-1 ring-gray-900/5">
                    {{ $faculties->count() }} fakultas
                </span>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="grid gap-4 md:grid-cols-2">
            @forelse($faculties as $faculty)
                <a href="{{ route('public.faculties.show', $faculty) }}"
                    class="group rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md hover:ring-indigo-500/15 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-base font-semibold leading-snug text-gray-900 group-hover:underline">
                                {{ $faculty->name }}
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                {{ $faculty->study_programs_count ?? 0 }} program studi ·
                                {{ $faculty->published_documents_count ?? 0 }} dokumen terbit
                            </div>
                            @if (!empty($faculty->kode))
                                <div class="mt-3 text-xs text-gray-500">Kode: {{ $faculty->kode }}</div>
                            @endif
                        </div>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-700">
                            Lihat
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
                    Belum ada data fakultas.
                </div>
            @endforelse
        </div>
    </section>
</x-public-layouts.app>

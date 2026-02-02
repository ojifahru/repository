<x-public-layouts.app :seo="$seo" title="Arsip Tahun">
    <section class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Arsip Tahun</h1>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">
                    Pilih tahun untuk melihat daftar dokumen terbit.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 text-xs font-medium text-gray-600">
                <span class="rounded-full bg-white/70 px-3 py-1 shadow-sm ring-1 ring-gray-900/5">
                    {{ $years->count() }} tahun
                </span>
            </div>
        </div>
    </section>

    <section class="mt-8">
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
            @forelse($years as $year)
                <a href="{{ route('public.years.show', ['year' => $year]) }}"
                    class="group rounded-2xl bg-white/80 p-6 text-center shadow-sm ring-1 ring-gray-900/5 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-md hover:ring-indigo-500/15 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                    <div class="text-2xl font-semibold text-gray-900 group-hover:text-indigo-700">
                        {{ $year }}
                    </div>
                    <div class="mt-2 text-sm text-gray-600">Lihat dokumen</div>
                </a>
            @empty
                <div class="rounded-2xl bg-white/80 p-8 text-sm text-gray-600 shadow-sm ring-1 ring-gray-900/5">
                    Belum ada arsip tahun.
                </div>
            @endforelse
        </div>
    </section>
</x-public-layouts.app>

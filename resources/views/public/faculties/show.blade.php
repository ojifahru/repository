<x-public-layouts.app :seo="$seo" :title="$faculty->name">
    <nav class="mb-5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-600" aria-label="Breadcrumb">
        <a class="font-medium text-gray-700 hover:text-gray-900 hover:underline"
            href="{{ route('public.faculties.index') }}">Fakultas</a>
        <span class="text-gray-300" aria-hidden="true">/</span>
        <span class="min-w-0 truncate text-gray-900">{{ $faculty->name }}</span>
    </nav>

    <section class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">{{ $faculty->name }}</h1>
                <div class="mt-3 flex flex-wrap gap-2">
                    <x-ui.badge variant="primary" class="border-indigo-200 bg-indigo-50 text-indigo-800">
                        {{ $faculty->studyPrograms->count() }} program studi
                    </x-ui.badge>
                    @if (!empty($faculty->kode))
                        <x-ui.badge class="border-0 bg-white/70 text-gray-700">Kode: {{ $faculty->kode }}</x-ui.badge>
                    @endif
                </div>
            </div>
            <a class="inline-flex items-center justify-center rounded-2xl bg-white/80 px-5 py-3 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                href="{{ route('public.documents.index', ['faculty_id' => $faculty->id]) }}">
                Filter dokumen
            </a>
        </div>

        @if ($faculty->studyPrograms->isNotEmpty())
            <div class="mt-6 rounded-2xl bg-white/70 p-5 shadow-sm ring-1 ring-gray-900/5">
                <div class="text-sm font-semibold text-gray-900">Program Studi</div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($faculty->studyPrograms as $sp)
                        <a href="{{ route('public.study-programs.show', $sp) }}"
                            class="rounded-xl bg-white px-3 py-2 text-sm font-medium text-gray-800 shadow-sm ring-1 ring-gray-900/5 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15">
                            {{ $sp->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    <section class="mt-10">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight text-gray-900">Dokumen Terbit</h2>
                <p class="mt-1 text-sm text-gray-600">Daftar dokumen published untuk fakultas ini.</p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            @forelse($documents as $doc)
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
                            @if ($doc->studyProgram)
                                <div class="mt-2 text-xs text-gray-500">{{ $doc->studyProgram->name }}</div>
                            @endif
                        </div>
                        <x-ui.badge variant="primary" class="border-indigo-200 bg-indigo-50 text-indigo-800">
                            {{ $doc->publish_year ?? '-' }}
                        </x-ui.badge>
                    </div>

                    @php
                        $abstractPreview = trim(strip_tags((string) $doc->abstract));
                    @endphp
                    @if ($abstractPreview !== '')
                        <p class="mt-4 text-sm leading-relaxed text-gray-700">
                            {{ \Illuminate\Support\Str::limit($abstractPreview, 180) }}
                        </p>
                    @endif
                </a>
            @empty
                <div class="rounded-2xl bg-white/80 p-8 text-sm text-gray-600 shadow-sm ring-1 ring-gray-900/5">
                    Belum ada dokumen published untuk fakultas ini.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $documents->links('pagination.public') }}
        </div>
    </section>
</x-public-layouts.app>

<x-public-layouts.app :seo="$seo" :title="'Arsip ' . $year">
    <nav class="mb-5 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-gray-600" aria-label="Breadcrumb">
        <a class="font-medium text-gray-700 hover:text-gray-900 hover:underline"
            href="{{ route('public.years.index') }}">Arsip Tahun</a>
        <span class="text-gray-300" aria-hidden="true">/</span>
        <span class="min-w-0 truncate text-gray-900">{{ $year }}</span>
    </nav>

    <section class="rounded-3xl bg-gradient-to-br from-indigo-50/60 via-white to-white p-6 sm:p-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <h1 class="text-3xl font-semibold tracking-tight text-gray-900">Dokumen {{ $year }}</h1>
                <p class="mt-2 text-sm leading-relaxed text-gray-600">
                    Daftar dokumen repository yang terbit pada tahun {{ $year }}.
                </p>
            </div>
            <a class="inline-flex items-center justify-center rounded-2xl bg-white/80 px-5 py-3 text-sm font-semibold text-gray-800 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                href="{{ route('public.documents.index', ['publish_year' => $year]) }}">
                Filter dokumen
            </a>
        </div>
    </section>

    <section class="mt-10">
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
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                                @if ($doc->category)
                                    <span>{{ $doc->category->name }}</span>
                                @endif
                                @if ($doc->documentType)
                                    <span>{{ $doc->documentType->name }}</span>
                                @endif
                                @if ($doc->faculty)
                                    <span>{{ $doc->faculty->name }}</span>
                                @endif
                                @if ($doc->studyProgram)
                                    <span>{{ $doc->studyProgram->name }}</span>
                                @endif
                            </div>
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
                    Belum ada dokumen published untuk tahun ini.
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $documents->links('pagination.public') }}
        </div>
    </section>
</x-public-layouts.app>

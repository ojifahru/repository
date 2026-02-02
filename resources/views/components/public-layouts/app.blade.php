@props([
    'title' => null,
    'seo' => null,
])

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    @php
        $seoData = is_array($seo) ? $seo : [];
        $seoData['title'] = $seoData['title'] ?? ($title ?? config('app.name'));
    @endphp

    <x-seo.head :seo="$seoData" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-b from-gray-50 via-white to-gray-50 text-gray-900">
    <a href="#content"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-xl focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-gray-900 focus:shadow">
        Lewati ke konten
    </a>

    <header class="sticky top-0 z-50 border-b border-gray-200/70 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:py-5">
            <a href="{{ route('public.home') }}" class="group flex items-center gap-3">
                <span
                    class="flex h-11 w-[148px] items-center justify-center rounded-2xl bg-white px-3 ring-1 ring-gray-200/60 sm:w-[170px]">
                    <img class="h-8 w-auto max-w-full object-contain" src="{{ asset('images/logo.png') }}"
                        alt="Logo {{ config('app.name') }}">
                </span>
                <span class="leading-tight">
                    <span class="block text-sm font-semibold text-gray-900 sm:text-base">
                        {{ config('app.name') }}
                    </span>
                    <span class="block text-xs text-gray-500">
                        Repository Institusi
                    </span>
                </span>
            </a>

            <nav class="hidden items-center gap-2 text-sm md:flex" aria-label="Navigasi utama">
                <a class="rounded-xl px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-900/5 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.documents.*') ? 'bg-gray-900/5 text-gray-900' : '' }}"
                    href="{{ route('public.documents.index') }}">
                    Dokumen
                </a>

                <a class="rounded-xl px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-900/5 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.study-programs.*') ? 'bg-gray-900/5 text-gray-900' : '' }}"
                    href="{{ route('public.study-programs.index') }}">
                    Program Studi
                </a>

                <a class="rounded-xl px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-900/5 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.faculties.*') ? 'bg-gray-900/5 text-gray-900' : '' }}"
                    href="{{ route('public.faculties.index') }}">
                    Fakultas
                </a>

                <a class="rounded-xl px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-900/5 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.authors.*') ? 'bg-gray-900/5 text-gray-900' : '' }}"
                    href="{{ route('public.authors.index') }}">
                    Penulis
                </a>
            </nav>

            <details class="relative md:hidden">
                <summary
                    class="list-none rounded-2xl bg-white/80 p-3 text-gray-700 shadow-sm ring-1 ring-gray-900/5 transition hover:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                    aria-label="Buka menu">
                    <svg viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 4.25A.75.75 0 0 1 3.75 9.25h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </summary>

                <div
                    class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl bg-white shadow-lg ring-1 ring-gray-900/10">
                    <div class="p-2 text-sm">
                        <a class="block rounded-xl px-3 py-3 font-medium text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.documents.*') ? 'bg-gray-50' : '' }}"
                            href="{{ route('public.documents.index') }}">
                            Dokumen
                        </a>
                        <a class="block rounded-xl px-3 py-3 font-medium text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.study-programs.*') ? 'bg-gray-50' : '' }}"
                            href="{{ route('public.study-programs.index') }}">
                            Program Studi
                        </a>
                        <a class="block rounded-xl px-3 py-3 font-medium text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.faculties.*') ? 'bg-gray-50' : '' }}"
                            href="{{ route('public.faculties.index') }}">
                            Fakultas
                        </a>
                        <a class="block rounded-xl px-3 py-3 font-medium text-gray-800 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.authors.*') ? 'bg-gray-50' : '' }}"
                            href="{{ route('public.authors.index') }}">
                            Penulis
                        </a>
                    </div>
                </div>
            </details>
        </div>
    </header>

    <main id="content" class="mx-auto max-w-6xl px-4 py-8 sm:py-10">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200/70 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-8">
            <div class="flex flex-col gap-4 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-[120px] items-center justify-center rounded-xl bg-white px-2 ring-1 ring-gray-200/60">
                        <a href="https://github.com/ojifahru/of-digital-repository"><img
                                class="h-6 w-auto max-w-full object-contain" src="{{ asset('images/logo.png') }}"
                                alt="Logo {{ config('app.name') }}"></a>
                    </span>
                    <div>
                        <div class="font-medium text-gray-900">{{ config('app.name') }}</div>
                        <div class="text-xs text-gray-500">© {{ now()->year }} — Repository publik</div>
                    </div>
                </div>

                <div class="text-xs text-gray-500">
                    TriDharma • Dokumen ilmiah • Akses publik
                </div>
            </div>
        </div>
    </footer>
</body>

</html>

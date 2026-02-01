@props(['title' => null])

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
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

            <nav class="flex items-center gap-2 text-sm">
                <a class="rounded-xl px-3 py-2 font-medium text-gray-700 transition hover:bg-gray-900/5 hover:text-gray-900 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 {{ request()->routeIs('public.documents.*') ? 'bg-gray-900/5 text-gray-900' : '' }}"
                    href="{{ route('public.documents.index') }}">
                    Dokumen
                </a>
            </nav>
        </div>
    </header>

    <main id="content" class="mx-auto max-w-6xl px-4 py-10">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200/70 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-8">
            <div class="flex flex-col gap-4 text-sm text-gray-600 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-[120px] items-center justify-center rounded-xl bg-white px-2 ring-1 ring-gray-200/60">
                        <img class="h-6 w-auto max-w-full object-contain" src="{{ asset('images/logo.png') }}"
                            alt="Logo {{ config('app.name') }}">
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

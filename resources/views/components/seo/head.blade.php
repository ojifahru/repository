@props([
    'seo' => [],
])

@php
    if (!is_array($seo)) {
        $seo = [];
    }

    $title = $seo['title'] ?? config('app.name');
    $description = $seo['description'] ?? null;
    $canonical = $seo['canonical'] ?? url()->current();
    $robots = $seo['robots'] ?? null;

    $og = $seo['og'] ?? [];
    $scholar = $seo['scholar'] ?? [];
    $jsonLd = $seo['jsonLd'] ?? [];

    if (!is_array($og)) {
        $og = [];
    }

    if (!is_array($scholar)) {
        $scholar = [];
    }

    if (!is_array($jsonLd)) {
        $jsonLd = [];
    }
@endphp

<title>{{ $title }}</title>

@if (!empty($description))
    <meta name="description" content="{{ $description }}">
@endif

<link rel="canonical" href="{{ $canonical }}">

@if (!empty($robots))
    <meta name="robots" content="{{ $robots }}">
@endif

<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ $og['title'] ?? $title }}">
@if (!empty($description) || !empty($og['description']))
    <meta property="og:description" content="{{ $og['description'] ?? $description }}">
@endif
<meta property="og:url" content="{{ $og['url'] ?? $canonical }}">
<meta property="og:type" content="{{ $og['type'] ?? 'website' }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $og['title'] ?? $title }}">
@if (!empty($description) || !empty($og['description']))
    <meta name="twitter:description" content="{{ $og['description'] ?? $description }}">
@endif

@foreach ($scholar as $name => $value)
    @if (is_string($name) && $name !== '' && is_string($value) && trim($value) !== '')
        <meta name="{{ $name }}" content="{{ $value }}">
    @elseif (is_string($name) && $name !== '' && is_array($value))
        @foreach ($value as $repeatValue)
            @if (is_string($repeatValue) && trim($repeatValue) !== '')
                <meta name="{{ $name }}" content="{{ $repeatValue }}">
            @endif
        @endforeach
    @endif
@endforeach

@foreach ($jsonLd as $block)
    @if (is_array($block))
        <script type="application/ld+json">@json($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
    @endif
@endforeach

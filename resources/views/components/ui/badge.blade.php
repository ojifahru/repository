@props([
    'variant' => 'neutral',
])

@php
    $base = 'inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-medium';

    $variants = [
        'neutral' => 'border-gray-200 bg-gray-50 text-gray-700',
        'primary' => 'border-gray-200 bg-white text-gray-900',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->class([$classes]) }}>
    {{ $slot }}
</span>

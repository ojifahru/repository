<?php

namespace App\Support\Seo;

use Illuminate\Support\Str;

class Seo
{
    /**
     * @param  array<int, string>  $parts
     */
    public static function title(array $parts, ?string $siteName = null): string
    {
        $siteName = $siteName ?? (string) config('app.name');

        $parts = array_values(array_filter($parts, fn ($part) => is_string($part) && trim($part) !== ''));

        if ($parts === []) {
            return self::limitTitle($siteName);
        }

        $base = implode(' — ', $parts);

        if (! Str::contains($base, $siteName)) {
            $base = $base.' — '.$siteName;
        }

        return self::limitTitle($base);
    }

    public static function description(?string $text): ?string
    {
        if (! is_string($text)) {
            return null;
        }

        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');

        if ($text === '') {
            return null;
        }

        return Str::limit($text, 160, '…');
    }

    public static function limitTitle(string $title): string
    {
        $title = trim(preg_replace('/\s+/u', ' ', $title) ?? $title);

        if ($title === '') {
            return (string) config('app.name');
        }

        return Str::limit($title, 60, '…');
    }

    public static function slugBase(?string $text, string $fallback = 'item'): string
    {
        $slug = Str::slug((string) $text);

        return $slug !== '' ? $slug : $fallback;
    }
}

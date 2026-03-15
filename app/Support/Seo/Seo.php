<?php

namespace App\Support\Seo;

use App\Settings\PublicSiteSettings;
use Illuminate\Support\Str;
use Throwable;

class Seo
{
    public static function siteName(): string
    {
        try {
            $siteName = trim((string) app(PublicSiteSettings::class)->site_name);

            if ($siteName !== '') {
                return $siteName;
            }
        } catch (Throwable) {
        }

        return (string) config('app.name');
    }

    /**
     * @param  array<int, string>  $parts
     */
    public static function title(array $parts, ?string $siteName = null): string
    {
        $siteName = $siteName ?? self::siteName();

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
            return self::siteName();
        }

        return Str::limit($title, 60, '…');
    }

    public static function slugBase(?string $text, string $fallback = 'item'): string
    {
        $slug = Str::slug((string) $text);

        return $slug !== '' ? $slug : $fallback;
    }
}

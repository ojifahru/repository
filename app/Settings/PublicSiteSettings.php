<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PublicSiteSettings extends Settings
{
    public string $site_name;

    public string $site_description;

    public ?string $logo_path;

    public ?string $favicon_path;

    public string $hero_badge;

    public string $hero_title;

    public string $hero_description;

    public string $footer_tagline;

    public static function group(): string
    {
        return 'public_site';
    }
}

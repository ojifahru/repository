<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('public_site', function (SettingsBlueprint $settings): void {
            $settings->add('site_name', config('app.name'));
            $settings->add('site_description', 'Repository institusi kampus untuk skripsi, tesis, jurnal, artikel, dan dokumen TriDharma. Telusuri judul, abstrak, penulis, dan unduh PDF.');
            $settings->add('hero_badge', config('app.name'));
            $settings->add('hero_title', config('app.name'));
            $settings->add('hero_description', 'Pusat dokumen TriDharma kampus yang mudah ditelusuri - judul, abstrak, dan author.');
            $settings->add('footer_tagline', 'TriDharma • Dokumen ilmiah • Akses publik');
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('public_site', function (SettingsBlueprint $settings): void {
            $settings->delete('site_name');
            $settings->delete('site_description');
            $settings->delete('hero_badge');
            $settings->delete('hero_title');
            $settings->delete('hero_description');
            $settings->delete('footer_tagline');
        });
    }
};

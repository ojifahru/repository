<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('public_site', function (SettingsBlueprint $settings): void {
            $settings->add('logo_path', null);
            $settings->add('favicon_path', null);
        });
    }

    public function down(): void
    {
        $this->migrator->inGroup('public_site', function (SettingsBlueprint $settings): void {
            $settings->delete('logo_path');
            $settings->delete('favicon_path');
        });
    }
};

<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\TriDharmaPerYear;
use App\Filament\Widgets\TriDharmaStatsOverview;

class Dashboard extends BaseDashboard
{
    public function canView(): bool
    {
        if (auth()->user()->can('View:Dashboard')) {
            return true;
        }
        return false;
    }
    /**
     * Grid layout dashboard
     */
    public function getColumns(): int
    {
        return 6; // 6 kolom untuk layout yang lebih fleksibel
    }

    /**
     * Widget kecil (stats) di bagian atas
     */
    protected function getHeaderWidgets(): array
    {
        return [
            TriDharmaStatsOverview::class, // Widget gabungan
        ];
    }

    /**
     * Widget utama (chart, table, dll)
     */
    public function getWidgets(): array
    {
        return [
            TriDharmaPerYear::class,
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\TriDharma;
use App\Models\Author;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TriDharmaStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Single query untuk semua data TriDharma (lebih efisien)
        $triDharmaCounts = TriDharma::query()
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published_count,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_count
            ")
            ->first();

        $authorCount = Author::count();

        return [
            Stat::make('Total Dokumen', $triDharmaCounts->total ?? 0)
                ->icon('heroicon-o-document-text')
                ->description('Seluruh dokumen Tri Dharma')
                ->color('primary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),

            Stat::make('Total Authors', $authorCount)
                ->icon('heroicon-o-user-group')
                ->description('Penulis terdaftar')
                ->color('secondary'),

            Stat::make('Published', $triDharmaCounts->published_count ?? 0)
                ->icon('heroicon-o-check-circle')
                ->description('Dokumen yang telah dipublikasi')
                ->color('success')
                ->descriptionIcon('heroicon-m-check-badge'),

            Stat::make('Draft', $triDharmaCounts->draft_count ?? 0)
                ->icon('heroicon-o-pencil-square')
                ->description('Dokumen dalam proses')
                ->color('warning')
                ->descriptionIcon('heroicon-m-clock'),
        ];
    }
}

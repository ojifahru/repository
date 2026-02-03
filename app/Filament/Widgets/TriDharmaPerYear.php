<?php

namespace App\Filament\Widgets;

use App\Models\TriDharma;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class TriDharmaPerYear extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';

    protected ?string $heading = 'Tri Dharma Per Tahun';
    protected ?string $maxHeight = '300px';

    protected ?string $description = 'Distribusi dokumen Tri Dharma per tahun publikasi';

    protected function getData(): array
    {
        // Cache data untuk 5 menit untuk mengurangi query database
        $data = Cache::remember('tri_dharma_per_year', 300, function () {
            try {
                return TriDharma::query()
                    ->whereNotNull('publish_year')
                    ->selectRaw('publish_year, COUNT(*) as total')
                    ->groupBy('publish_year')
                    ->orderBy('publish_year')
                    ->pluck('total', 'publish_year');
            } catch (\Exception $e) {
                // Log error dan return data kosong
                \Log::error('Error fetching TriDharma per year: ' . $e->getMessage());
                return collect();
            }
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dokumen',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#1d4ed8',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Optional: Tambahkan options untuk chart
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}

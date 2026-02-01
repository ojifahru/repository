<?php

namespace App\Filament\Resources\TriDharmas\Pages;

use App\Filament\Resources\TriDharmas\TriDharmaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TriDharma;

class ListTriDharmas extends ListRecords
{
    protected static string $resource = TriDharmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Tri Dharma')
                ->visible(fn() => auth()->user()->can('Create:TriDharma')),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'published' => Tab::make('Published')
                ->icon('heroicon-o-check-circle')
                ->badge(fn() => TriDharma::where('status', 'published')->count())
                ->query(fn(Builder $query) => $query->where('status', 'published')),

            'draft' => Tab::make('Draft')
                ->icon('heroicon-o-pencil-square')
                ->badge(fn() => TriDharma::where('status', 'draft')->count())
                ->query(fn(Builder $query) => $query->where('status', 'draft')),

            'all' => Tab::make('Semua')
                ->icon('heroicon-o-archive-box')
                ->badge(fn() => TriDharma::count()),
        ];

        // 👑 SUPER ADMIN SAJA
        if (auth()->user()->hasRole('super_admin')) {
            $tabs['deleted'] = Tab::make('Dihapus')
                ->icon('heroicon-o-trash')
                ->badge(fn() => TriDharma::onlyTrashed()->count())
                ->query(fn(Builder $query) => $query->onlyTrashed());
        }

        return $tabs;
    }
}

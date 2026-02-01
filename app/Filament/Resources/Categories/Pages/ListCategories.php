<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoriesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Container\Attributes\Auth;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Categories;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        if (! Auth()->user()->hasRole('super_admin')) {
            return [
                'active' => Tab::make('Kategori Aktif')
                    ->query(fn(Builder $query) => $query->whereNull('deleted_at')),
            ];
        }
        return [
            'active' => Tab::make('Kategori Aktif')
                ->query(fn(Builder $query) => $query->whereNull('deleted_at'))
                ->badge(fn() => Categories::whereNull('deleted_at')->count())
                ->icon('heroicon-o-tag'),
            'trashed' => Tab::make('Kategori Terhapus')
                ->query(fn(Builder $query) => $query->onlyTrashed())
                ->badge(fn() => Categories::onlyTrashed()->count())
                ->icon('heroicon-o-trash'),
        ];
    }
}

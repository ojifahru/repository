<?php

namespace App\Filament\Resources\Authors\Pages;

use App\Filament\Resources\Authors\AuthorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Author;

class ListAuthors extends ListRecords
{
    protected static string $resource = AuthorResource::class;

    public function getTabs(): array
    {
        // 🔒 TAB HANYA UNTUK SUPER ADMIN
        if (! auth()->user()->hasRole('super_admin')) {
            return [
                'active' => Tab::make('Author Aktif')
                    ->query(fn(Builder $query) => $query->whereNull('deleted_at')),
            ];
        }

        // 👑 SUPER ADMIN
        return [
            'active' => Tab::make('Author Aktif')
                ->query(fn(Builder $query) => $query->whereNull('deleted_at'))
                ->badge(fn() => Author::whereNull('deleted_at')->count())
                ->icon('heroicon-o-user-group'),

            'trashed' => Tab::make('Author Terhapus')
                ->query(fn(Builder $query) => $query->onlyTrashed())
                ->badge(fn() => Author::onlyTrashed()->count())
                ->icon('heroicon-o-trash'),
        ];
    }
    public function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Author'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Faculties\Pages;

use App\Filament\Resources\Faculties\FacultyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Faculty;

class ListFaculties extends ListRecords
{
    protected static string $resource = FacultyResource::class;

    public function getTabs(): array
    {
        if (! auth()->user()->hasRole('super_admin')) {
            return [
                'active' => Tab::make('Fakultas Aktif')
                    ->query(fn(Builder $query) => $query->whereNull('deleted_at')),
            ];
        }
        return [
            'active' => Tab::make('Fakultas Aktif')
                ->query(fn(Builder $query) => $query->whereNull('deleted_at'))
                ->badge(fn() => Faculty::whereNull('deleted_at')->count())
                ->icon('heroicon-o-building-office'),

            'trashed' => Tab::make('Fakultas Terhapus')
                ->query(fn(Builder $query) => $query->onlyTrashed())
                ->badge(fn() => Faculty::onlyTrashed()->count())
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Fakultas'),
        ];
    }
}

<?php

namespace App\Filament\Resources\StudyPrograms\Pages;

use App\Filament\Resources\StudyPrograms\StudyProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Container\Attributes\Auth;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\StudyProgram;

class ListStudyPrograms extends ListRecords
{
    protected static string $resource = StudyProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        if (! auth()->user()->hasRole('super_admin')) {
            return [
                'active' => Tab::make('Program Studi Aktif')
                    ->query(fn(Builder $query) => $query->whereNull('deleted_at')),
            ];
        }
        return [
            'active' => Tab::make('Program Studi Aktif')
                ->query(fn(Builder $query) => $query->whereNull('deleted_at'))
                ->badge(fn() => StudyProgram::whereNull('deleted_at')->count())
                ->icon('heroicon-o-academic-cap'),

            'trashed' => Tab::make('Program Studi Terhapus')
                ->query(fn(Builder $query) => $query->onlyTrashed())
                ->badge(fn() => StudyProgram::onlyTrashed()->count())
                ->icon('heroicon-o-trash'),
        ];
    }
}

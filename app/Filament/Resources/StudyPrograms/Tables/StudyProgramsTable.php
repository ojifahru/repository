<?php

namespace App\Filament\Resources\StudyPrograms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudyProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Program Studi')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->faculty?->name),

                TextColumn::make('degree')
                    ->label('Jenjang')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable(),
            ])

            // ================= FILTER =================
            ->filters([
                SelectFilter::make('faculty_id')
                    ->label('Fakultas')
                    ->relationship('faculty', 'name'),
            ])

            // ================= ACTION =================
            ->recordActions([
                EditAction::make()
                    ->label('ubah')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Update:StudyProgram')
                    ),
                RestoreAction::make()
                    ->label('pulihkan')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('Restore:StudyProgram')
                    ),
                DeleteAction::make()
                    ->label('hapus')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Delete:StudyProgram')
                    ),
                ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:StudyProgram')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('name');
    }
}

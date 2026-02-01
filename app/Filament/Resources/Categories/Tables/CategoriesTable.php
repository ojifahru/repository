<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Slug disalin'),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('ubah')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Update:Categories')
                    ),
                DeleteAction::make()
                    ->label('arsipkan')
                    ->visible(
                        fn($record) =>
                        is_null($record->deleted_at)
                            && auth()->user()->can('Delete:Categories')
                    ),
                RestoreAction::make()
                    ->label('pulihkan')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('Restore:Categories')
                    ),
                ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:Categories')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('name');
    }
}

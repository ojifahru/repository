<?php

namespace App\Filament\Resources\Faculties\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FacultiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nama Fakultas')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('kode')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

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
                        fn ($record) => auth()->user()->can('Update:Faculty')
                    ),

                DeleteAction::make()
                    ->label('arsipkan')
                    ->visible(
                        fn ($record) => is_null($record->deleted_at)
                            && auth()->user()->can('Delete:Faculty')
                    ),

                RestoreAction::make()
                    ->label('Pulihkan')
                    ->visible(
                        fn ($record) => ! is_null($record->deleted_at)
                            && auth()->user()->can('Restore:Faculty')
                    ),

                ForceDeleteAction::make()
                    ->label('Hapus Permanen')
                    ->visible(
                        fn ($record) => ! is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:Faculty')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('name');
    }
}

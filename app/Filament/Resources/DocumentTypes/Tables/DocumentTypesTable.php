<?php

namespace App\Filament\Resources\DocumentTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Jenis Dokumen')
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
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn($record) => $record->triDharmas()->exists())
                    ->tooltip('Jenis dokumen masih digunakan')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Delete:DocumentType')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('name');
    }
}

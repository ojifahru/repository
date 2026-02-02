<?php

namespace App\Filament\Resources\Degrees\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DegreesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('slug_suffix')
                    ->label('Suffix')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('ubah')
                    ->visible(fn() => auth()->user()->can('Update:Degree')),

                DeleteAction::make()
                    ->label('hapus')
                    ->visible(fn() => auth()->user()->can('Delete:Degree')),
            ])
            ->toolbarActions([])
            ->defaultSort('name');
    }
}

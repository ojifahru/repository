<?php

namespace App\Filament\Resources\ProgramTypes\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProgramTypesTable
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
            ])
            ->recordActions([
                EditAction::make()
                    ->label('ubah')
                    ->visible(fn() => auth()->user()->can('Update:ProgramType')),

                DeleteAction::make()
                    ->label('hapus')
                    ->visible(fn() => auth()->user()->can('Delete:ProgramType')),
            ])
            ->toolbarActions([])
            ->defaultSort('name');
    }
}

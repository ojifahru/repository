<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->email),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(', ')
                    ->colors([
                        'primary',
                        'success' => 'admin',
                        'secondary' => 'user',
                    ]),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('ubah')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Update:User')
                    ),
                RestoreAction::make()
                    ->label('pulihkan')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('Restore:User')
                    ),
                DeleteAction::make()
                    ->label('hapus')
                    ->visible(
                        fn($record) =>
                        is_null($record->deleted_at)
                            && auth()->user()->can('Delete:User')
                    ),
                ForceDeleteAction::make()
                    ->label('hapus permanen')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:User')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('name');
    }
}

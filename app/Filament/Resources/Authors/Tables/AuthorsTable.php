<?php

namespace App\Filament\Resources\Authors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class AuthorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query->withCount('triDharmas')
            )

            ->columns([

                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->circular()
                    ->disk('public')
                    ->imageSize(40)
                    ->defaultImageUrl(
                        fn($record) =>
                        $record->image_url
                            ? null
                            : 'https://ui-avatars.com/api/?name=' . urlencode($record->name)
                    ),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->email),

                TextColumn::make('identifier')
                    ->label('NIM / NIDN')
                    ->toggleable(),

                TextColumn::make('tri_dharmas_count')
                    ->label('Dokumen')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->sortable(),
            ])

            // ================= FILTER =================
            ->filters([
                Filter::make('has_documents')
                    ->label('Punya Dokumen')
                    ->query(
                        fn(Builder $query) => $query->has('triDharmas')
                    ),
            ])

            // ================= ROW ACTION =================
            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Update:Author')
                    ),

                // Arsipkan (soft delete)
                DeleteAction::make()
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->visible(
                        fn($record) =>
                        is_null($record->deleted_at)
                            && auth()->user()->can('Delete:Author')
                    )
                    ->modalHeading('Arsipkan Author')
                    ->modalDescription('Apakah Anda yakin ingin mengarsipkan author ini?')
                    ->modalSubmitActionLabel('Arsipkan')
                    ->modalIcon('heroicon-o-archive-box'),

                // Restore (hanya kalau terhapus)
                RestoreAction::make()
                    ->visible(
                        fn($record) =>
                        ! is_null($record->deleted_at)
                            && auth()->user()->can('Restore:Author')
                    )
                    ->modalHeading('Pulihkan Author')
                    ->modalDescription('Apakah Anda yakin ingin memulihkan author ini?')
                    ->modalSubmitActionLabel('Pulihkan')
                    ->modalIcon('heroicon-o-arrow-uturn-left'),

                // Force delete (hanya kalau terhapus)
                ForceDeleteAction::make()
                    ->visible(
                        fn($record) =>
                        ! is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:Author')
                    )
                    ->modalHeading('Hapus Permanen Author')
                    ->modalDescription('Apakah Anda yakin ingin menghapus permanen author ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Hapus Permanen')
                    ->modalIcon('heroicon-o-trash'),
            ])


            // ================= BULK ACTION =================
            ->toolbarActions([])

            ->defaultSort('name');
    }
}

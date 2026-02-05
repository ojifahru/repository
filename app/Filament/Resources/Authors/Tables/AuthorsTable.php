<?php

namespace App\Filament\Resources\Authors\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuthorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->withCount('triDharmas')
            )
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Foto')
                    ->circular()
                    ->imageSize(40)
                    ->getStateUsing(function ($record): ?string {
                        if (empty($record->image_url)) {
                            return null;
                        }

                        if (! Storage::disk('public')->exists($record->image_url)) {
                            return null;
                        }

                        return $record->image_url;
                    })
                    ->disk('public')
                    ->defaultImageUrl(function ($record): ?string {
                        if (! empty($record->image_url) && Storage::disk('public')->exists($record->image_url)) {
                            return null;
                        }

                        return 'https://ui-avatars.com/api/?name='.urlencode($record->name);
                    }),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->email),

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

            ->filters([
                Filter::make('has_documents')
                    ->label('Punya Dokumen')
                    ->query(fn (Builder $query) => $query->has('triDharmas')),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->visible(fn (): bool => Auth::user()?->can('Update:Author') ?? false),

                DeleteAction::make()
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->visible(fn ($record): bool => is_null($record->deleted_at) && (Auth::user()?->can('Delete:Author') ?? false))
                    ->modalHeading('Arsipkan Author')
                    ->modalDescription('Apakah Anda yakin ingin mengarsipkan author ini?')
                    ->modalSubmitActionLabel('Arsipkan')
                    ->modalIcon('heroicon-o-archive-box'),

                RestoreAction::make()
                    ->visible(fn ($record): bool => ! is_null($record->deleted_at) && (Auth::user()?->can('Restore:Author') ?? false))
                    ->modalHeading('Pulihkan Author')
                    ->modalDescription('Apakah Anda yakin ingin memulihkan author ini?')
                    ->modalSubmitActionLabel('Pulihkan')
                    ->modalIcon('heroicon-o-arrow-uturn-left'),

                ForceDeleteAction::make()
                    ->visible(fn ($record): bool => ! is_null($record->deleted_at) && (Auth::user()?->can('ForceDelete:Author') ?? false))
                    ->modalHeading('Hapus Permanen Author')
                    ->modalDescription('Apakah Anda yakin ingin menghapus permanen author ini? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Hapus Permanen')
                    ->modalIcon('heroicon-o-trash'),
            ])
            ->toolbarActions([])
            ->defaultSort('name');
    }
}

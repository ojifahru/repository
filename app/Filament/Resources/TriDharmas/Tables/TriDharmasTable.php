<?php

namespace App\Filament\Resources\TriDharmas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class TriDharmasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                TextColumn::make('authors.name')
                    ->label('Author')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('documentType.name')
                    ->label('Jenis Dokumen')
                    ->sortable(),

                TextColumn::make('faculty.name')
                    ->label('Fakultas')
                    ->toggleable(),

                TextColumn::make('studyProgram.name')
                    ->label('Program Studi')
                    ->toggleable(),

                TextColumn::make('publish_year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                    ]),
            ])

            ->filters([
                SelectFilter::make('authors')
                    ->label('Author')
                    ->relationship('authors', 'name'),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),

                SelectFilter::make('document_type_id')
                    ->label('Jenis Dokumen')
                    ->relationship('documentType', 'name'),

                SelectFilter::make('faculty_id')
                    ->label('Fakultas')
                    ->relationship('faculty', 'name'),

                SelectFilter::make('study_program_id')
                    ->label('Program Studi')
                    ->relationship('studyProgram', 'name'),
            ])

            ->recordActions([
                EditAction::make()
                    ->label('Ubah')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Update:TriDharma')
                    ),
                RestoreAction::make()
                    ->label('Pulihkan')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('Restore:TriDharma')
                    ),
                DeleteAction::make()
                    ->label('Hapus')
                    ->visible(
                        fn($record) =>
                        auth()->user()->can('Delete:TriDharma')
                    ),
                ForceDeleteAction::make()
                    ->label('Hapus Permanen')
                    ->visible(
                        fn($record) =>
                        !is_null($record->deleted_at)
                            && auth()->user()->can('ForceDelete:TriDharma')
                    ),
            ])

            ->toolbarActions([])

            ->defaultSort('created_at', 'desc');
    }
}

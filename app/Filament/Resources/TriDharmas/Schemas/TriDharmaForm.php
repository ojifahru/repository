<?php

namespace App\Filament\Resources\TriDharmas\Schemas;

use App\Models\StudyProgram;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\RichEditor;

class TriDharmaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Tabs::make('Tri Dharma')
                ->columnSpanFull()
                ->tabs([

                    /* =========================
                     * TAB 1 — INFORMASI
                     * ========================= */
                    Tab::make('Informasi Dokumen')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(255),

                            RichEditor::make('abstract')
                                ->label('Abstrak')
                                ->maxLength(1000)
                                ->columnSpanFull(),
                        ]),

                    /* =========================
                     * TAB 2 — KLASIFIKASI
                     * ========================= */
                    Tab::make('Klasifikasi')
                        ->icon('heroicon-o-tag')
                        ->schema([
                            Select::make('category_id')
                                ->label('Kategori Tri Dharma')
                                ->relationship(
                                    'category',
                                    'name',
                                    fn($query) => $query->whereNull('deleted_at')
                                )
                                ->searchable()
                                ->preload()
                                ->required(),


                            Select::make('document_type_id')
                                ->label('Jenis Dokumen')
                                ->relationship('documentType', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                    /* =========================
                     * TAB 3 — STRUKTUR & AUTHOR
                     * ========================= */
                    Tab::make('Struktur & Author')
                        ->icon('heroicon-o-users')
                        ->schema([
                            Select::make('faculty_id')
                                ->label('Fakultas')
                                ->relationship(
                                    'faculty',
                                    'name',
                                    fn($query) => $query->whereNull('deleted_at')
                                )
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn(callable $set) => $set('study_program_id', null)),

                            Select::make('study_program_id')
                                ->label('Program Studi')
                                ->options(
                                    fn(callable $get) =>
                                    $get('faculty_id')
                                        ? StudyProgram::where('faculty_id', $get('faculty_id'))
                                        ->whereNull('deleted_at')
                                        ->pluck('name', 'id')
                                        : []
                                )
                                ->required()
                                ->disabled(fn(callable $get) => ! $get('faculty_id')),

                            Select::make('authors')
                                ->label('Author')
                                ->multiple()
                                ->relationship(
                                    'authors',
                                    'name',
                                    fn($query) => $query->whereNull('deleted_at')
                                )
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),

                    /* =========================
                     * TAB 4 — PUBLIKASI & FILE
                     * ========================= */
                    Tab::make('Publikasi & File')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->schema([
                            TextInput::make('publish_year')
                                ->label('Tahun Publikasi')
                                ->numeric()
                                ->minValue(1900)
                                ->maxValue(now()->year)
                                ->required(),

                            Select::make('status')
                                ->label('Status Publikasi')
                                ->options([
                                    'draft' => 'Draft',
                                    'published' => 'Published',
                                ])
                                ->default('draft')
                                ->required(),

                            FileUpload::make('file_path')
                                ->label('File Dokumen (PDF)')
                                ->disk('public')
                                ->directory('tri_dharmas')
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(10240)
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
    }
}

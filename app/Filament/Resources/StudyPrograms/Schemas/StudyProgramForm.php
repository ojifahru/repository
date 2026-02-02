<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

use App\Models\Degree;
use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class StudyProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Informasi Program Studi')
                    ->schema([

                        // === BARIS 1 ===
                        TextInput::make('name')
                            ->label('Nama Program Studi')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                $set(
                                    'slug',
                                    self::generateSlug(
                                        (string) $state,
                                        self::resolveDegreeSuffix($get('degree_id'))
                                    )
                                )
                            ),

                        TextInput::make('kode')
                            ->label('Kode Program Studi')
                            ->required()
                            ->maxLength(50)
                            ->columnSpan(1)
                            ->unique(ignoreRecord: true),

                        // === BARIS 2 ===
                        Select::make('degree_id')
                            ->label('Jenjang')
                            ->relationship('degree', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1)
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                $set(
                                    'slug',
                                    self::generateSlug(
                                        (string) $get('name'),
                                        self::resolveDegreeSuffix($state)
                                    )
                                )
                            ),

                        Select::make('program_type_id')
                            ->label('Tipe Program')
                            ->relationship('programType', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(100),

                                TextInput::make('code')
                                    ->label('Kode')
                                    ->maxLength(30)
                                    ->unique(ignoreRecord: true),
                            ]),

                        // === BARIS 3 ===
                        Select::make('faculty_id')
                            ->label('Fakultas')
                            ->relationship('faculty', 'name')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->columnSpan(2),

                        // === BARIS 4 ===
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->columnSpan(2)
                            ->helperText('Digunakan untuk URL program studi'),

                    ])
                    ->columns(2)
                    ->columnSpan('full'),
            ]);
    }

    private static function resolveDegreeSuffix(mixed $degreeId): string
    {
        if (! is_numeric($degreeId)) {
            return '';
        }

        return (string) (Degree::query()->whereKey((int) $degreeId)->value('slug_suffix') ?? '');
    }

    private static function generateSlug(string $name, string $degreeSuffix): string
    {
        $slug = Str::slug(trim($name . ' ' . $degreeSuffix));

        return Str::limit($slug, 150, '');
    }
}

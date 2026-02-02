<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

use App\Models\Degree;
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

                        TextInput::make('name')
                            ->label('Nama Program Studi')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) => $set('slug', self::generateSlug((string) $state, self::resolveDegreeSuffix($get('degree_id'))))
                            ),

                        Select::make('degree_id')
                            ->label('Jenjang')
                            ->required()
                            ->relationship('degree', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) => $set('slug', self::generateSlug((string) $get('name'), self::resolveDegreeSuffix($state)))
                            ),

                        Select::make('program_type_id')
                            ->label('Tipe Program')
                            ->relationship('programType', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false)
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

                        Select::make('faculty_id')
                            ->label('Fakultas')
                            ->relationship('faculty', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->helperText('Digunakan untuk URL program studi'),
                    ]),
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

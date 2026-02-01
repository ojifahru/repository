<?php

namespace App\Filament\Resources\StudyPrograms\Schemas;

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
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        Select::make('degree')
                            ->label('Jenjang')
                            ->required()
                            ->options([
                                'Bachelor' => 'Sarjana (S1)',
                                'Master' => 'Magister (S2)',
                                'Doctorate' => 'Doktor (S3)',
                            ])
                            ->native(false),

                        Select::make('faculty_id')
                            ->label('Fakultas')
                            ->relationship('faculty', 'name')
                            ->searchable()
                            ->required(),

                        TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(150)
                            ->unique(ignoreRecord: true)
                            ->helperText('Digunakan untuk URL program studi'),
                    ]),
            ]);
    }
}

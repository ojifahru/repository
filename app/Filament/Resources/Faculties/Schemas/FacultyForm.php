<?php

namespace App\Filament\Resources\Faculties\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class FacultyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Section::make('Informasi Fakultas')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama Fakultas')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn($state, callable $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->maxLength(10),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true)
                            ->helperText('Otomatis dari nama, digunakan untuk URL'),
                    ]),
            ]);
    }
}

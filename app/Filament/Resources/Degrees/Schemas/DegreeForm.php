<?php

namespace App\Filament\Resources\Degrees\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DegreeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Jenjang')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(30)
                            ->unique(ignoreRecord: true)
                            ->helperText('Contoh: Bachelor, Master, Doctorate'),

                        TextInput::make('slug_suffix')
                            ->label('Suffix Slug')
                            ->maxLength(10)
                            ->helperText('Contoh: s1, s2, s3 (dipakai untuk slug prodi)'),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ProgramTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProgramTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Informasi Tipe Program')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        TextInput::make('code')
                            ->label('Kode')
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),
                    ]),
            ]);
    }
}

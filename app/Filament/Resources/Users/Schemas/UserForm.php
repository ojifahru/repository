<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                // ================= USER INFO =================
                Section::make('Informasi User')
                    ->schema([

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->autocomplete('name'),

                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),

                // ================= ROLE =================
                Section::make('Role & Akses')
                    ->schema([

                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),

                // ================= PASSWORD =================
                Section::make('Keamanan')
                    ->columns(2)
                    ->schema([

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->maxLength(255)
                            ->required(fn($context) => $context === 'create')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => bcrypt($state))
                            ->confirmed()
                            ->autocomplete('new-password')
                            ->revealable()
                            ->suffixAction(
                                Action::make('generatePassword')
                                    ->icon('heroicon-o-key')
                                    ->tooltip('Generate password otomatis')
                                    ->action(function (callable $set) {
                                        $password = Str::password(12); // Laravel helper
                                        $set('password', $password);
                                    })
                            ),


                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->autocomplete('new-password')
                            ->revealable(),
                    ]),
            ]);
    }
}

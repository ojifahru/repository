<?php

namespace App\Filament\Resources\ProgramTypes;

use App\Filament\Resources\ProgramTypes\Pages\CreateProgramType;
use App\Filament\Resources\ProgramTypes\Pages\EditProgramType;
use App\Filament\Resources\ProgramTypes\Pages\ListProgramTypes;
use App\Filament\Resources\ProgramTypes\Schemas\ProgramTypeForm;
use App\Filament\Resources\ProgramTypes\Tables\ProgramTypesTable;
use App\Models\ProgramType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProgramTypeResource extends Resource
{
    protected static ?string $model = ProgramType::class;

    protected static string|UnitEnum|null $navigationGroup = 'Struktur Akademik';

    protected static ?string $navigationLabel = 'Tipe Program';

    protected static ?string $modelLabel = 'Tipe Program';

    protected static ?string $pluralModelLabel = 'Tipe Program';

    protected static ?int $navigationSort = 22;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    public static function form(Schema $schema): Schema
    {
        return ProgramTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProgramTypes::route('/'),
            'create' => CreateProgramType::route('/create'),
            'edit' => EditProgramType::route('/{record}/edit'),
        ];
    }
}

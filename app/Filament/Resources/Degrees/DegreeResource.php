<?php

namespace App\Filament\Resources\Degrees;

use App\Filament\Resources\Degrees\Pages\CreateDegree;
use App\Filament\Resources\Degrees\Pages\EditDegree;
use App\Filament\Resources\Degrees\Pages\ListDegrees;
use App\Filament\Resources\Degrees\Schemas\DegreeForm;
use App\Filament\Resources\Degrees\Tables\DegreesTable;
use App\Models\Degree;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DegreeResource extends Resource
{
    protected static ?string $model = Degree::class;

    protected static string|UnitEnum|null $navigationGroup = 'Struktur Akademik';

    protected static ?string $navigationLabel = 'Jenjang';

    protected static ?string $modelLabel = 'Jenjang';

    protected static ?string $pluralModelLabel = 'Jenjang';

    protected static ?int $navigationSort = 21;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AcademicCap;

    public static function form(Schema $schema): Schema
    {
        return DegreeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DegreesTable::configure($table);
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
            'index' => ListDegrees::route('/'),
            'create' => CreateDegree::route('/create'),
            'edit' => EditDegree::route('/{record}/edit'),
        ];
    }
}

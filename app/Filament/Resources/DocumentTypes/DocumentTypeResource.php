<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Filament\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypeActivities;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Filament\Resources\DocumentTypes\Schemas\DocumentTypeForm;
use App\Filament\Resources\DocumentTypes\Tables\DocumentTypesTable;
use App\Models\DocumentType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static string|UnitEnum|null $navigationGroup = 'Metadata';

    protected static ?string $navigationLabel = 'Tipe Dokumen';

    protected static ?int $navigationSort = 30;

    protected static ?string $modelLabel = 'Tipe Dokumen';

    protected static ?string $pluralModelLabel = 'Tipe Dokumen';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentDuplicate;

    public static function form(Schema $schema): Schema
    {
        return DocumentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentTypesTable::configure($table);
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
            'index' => ListDocumentTypes::route('/'),
            'create' => CreateDocumentType::route('/create'),
            'edit' => EditDocumentType::route('/{record}/edit'),
            'activities' => ListDocumentTypeActivities::route('/{record}/activities'),
        ];
    }

    public static function canViewAny(): bool
    {
        return parent::canViewAny();
    }
}

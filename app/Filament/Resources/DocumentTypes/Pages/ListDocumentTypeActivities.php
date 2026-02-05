<?php

namespace App\Filament\Resources\DocumentTypes\Pages;

use App\Filament\Resources\DocumentTypes\DocumentTypeResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListDocumentTypeActivities extends ListActivities
{
    protected static string $resource = DocumentTypeResource::class;
}

<?php

namespace App\Filament\Resources\Degrees\Pages;

use App\Filament\Resources\Degrees\DegreeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDegrees extends ListRecords
{
    protected static string $resource = DegreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Jenjang'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProgramTypes\Pages;

use App\Filament\Resources\ProgramTypes\ProgramTypeResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListProgramTypeActivities extends ListActivities
{
    protected static string $resource = ProgramTypeResource::class;
}

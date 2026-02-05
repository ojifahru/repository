<?php

namespace App\Filament\Resources\Authors\Pages;

use App\Filament\Resources\Authors\AuthorResource;
use pxlrbt\FilamentActivityLog\Pages\ListActivities;

class ListAuthorActivities extends ListActivities
{
    protected static string $resource = AuthorResource::class;
}

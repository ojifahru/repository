<?php

namespace App\Filament\Resources\Degrees\Pages;

use App\Filament\Resources\Degrees\DegreeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDegree extends CreateRecord
{
    protected static string $resource = DegreeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Jenjang berhasil ditambahkan.');
    }
}

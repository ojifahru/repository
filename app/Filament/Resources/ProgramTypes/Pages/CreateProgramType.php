<?php

namespace App\Filament\Resources\ProgramTypes\Pages;

use App\Filament\Resources\ProgramTypes\ProgramTypeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramType extends CreateRecord
{
    protected static string $resource = ProgramTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Tipe program berhasil ditambahkan.');
    }
}

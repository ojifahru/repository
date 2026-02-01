<?php

namespace App\Filament\Resources\TriDharmas\Pages;

use App\Filament\Resources\TriDharmas\TriDharmaResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTriDharma extends CreateRecord
{
    protected static string $resource = TriDharmaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Tri Dharma berhasil ditambahkan.');
    }
}

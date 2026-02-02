<?php

namespace App\Filament\Resources\Degrees\Pages;

use App\Filament\Resources\Degrees\DegreeResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDegree extends EditRecord
{
    protected static string $resource = DegreeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Jenjang')
                ->visible(fn() => auth()->user()->can('Delete:Degree')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Jenjang berhasil diperbarui.');
    }
}

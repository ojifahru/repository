<?php

namespace App\Filament\Resources\TriDharmas\Pages;

use App\Filament\Resources\TriDharmas\TriDharmaResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTriDharma extends EditRecord
{
    protected static string $resource = TriDharmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Tri Dharma')
                ->visible(fn() => auth()->user()->can('Delete:TriDharma')),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Tri Dharma berhasil diperbarui.');
    }
}

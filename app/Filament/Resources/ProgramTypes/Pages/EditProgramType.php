<?php

namespace App\Filament\Resources\ProgramTypes\Pages;

use App\Filament\Resources\ProgramTypes\ProgramTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProgramType extends EditRecord
{
    protected static string $resource = ProgramTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Tipe Program')
                ->visible(fn() => auth()->user()->can('Delete:ProgramType')),
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
            ->body('Tipe program berhasil diperbarui.');
    }
}

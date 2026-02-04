<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function canView(): bool
    {
        $user = Auth::user();

        if (! $user?->can('Update:User')) {
            return false;
        }

        if ($user->hasRole('admin') && ! $user->hasRole('super_admin')) {
            $record = $this->getRecord();

            if (! $record) {
                return false;
            }

            if ($record->getKey() === $user->getKey()) {
                return true;
            }

            return $record->hasRole('editor') && ! $record->hasAnyRole(['admin', 'super_admin']);
        }

        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label('Hapus Pengguna')
                ->visible(fn () => Auth::user()?->can('Delete:User') ?? false),
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
            ->body('Pengguna berhasil diperbarui.');
    }
}

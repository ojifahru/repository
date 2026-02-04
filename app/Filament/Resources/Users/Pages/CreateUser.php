<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if ($user?->hasRole('admin') && ! $user->hasRole('super_admin')) {
            $editorRoleId = Role::query()->where('name', 'editor')->value('id');

            if (! $editorRoleId) {
                throw ValidationException::withMessages([
                    'roles' => 'Role editor belum tersedia. Silakan buat role editor terlebih dahulu.',
                ]);
            }

            $data['roles'] = [$editorRoleId];
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Pengguna berhasil ditambahkan.');
    }
}

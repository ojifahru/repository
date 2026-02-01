<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TriDharma;
use Illuminate\Auth\Access\HandlesAuthorization;

class TriDharmaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TriDharma');
    }

    public function view(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('View:TriDharma');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TriDharma');
    }

    public function update(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('Update:TriDharma');
    }

    public function delete(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('Delete:TriDharma');
    }

    public function restore(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('Restore:TriDharma');
    }

    public function forceDelete(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('ForceDelete:TriDharma');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TriDharma');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TriDharma');
    }

    public function replicate(AuthUser $authUser, TriDharma $triDharma): bool
    {
        return $authUser->can('Replicate:TriDharma');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TriDharma');
    }

}
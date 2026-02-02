<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Degree;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DegreePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Degree');
    }

    public function view(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('View:Degree');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Degree');
    }

    public function update(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('Update:Degree');
    }

    public function delete(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('Delete:Degree');
    }

    public function restore(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('Restore:Degree');
    }

    public function forceDelete(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('ForceDelete:Degree');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Degree');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Degree');
    }

    public function replicate(AuthUser $authUser, Degree $degree): bool
    {
        return $authUser->can('Replicate:Degree');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Degree');
    }
}

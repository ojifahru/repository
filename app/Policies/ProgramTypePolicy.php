<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProgramType;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProgramTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProgramType');
    }

    public function view(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('View:ProgramType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProgramType');
    }

    public function update(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('Update:ProgramType');
    }

    public function delete(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('Delete:ProgramType');
    }

    public function restore(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('Restore:ProgramType');
    }

    public function forceDelete(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('ForceDelete:ProgramType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProgramType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProgramType');
    }

    public function replicate(AuthUser $authUser, ProgramType $programType): bool
    {
        return $authUser->can('Replicate:ProgramType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProgramType');
    }
}

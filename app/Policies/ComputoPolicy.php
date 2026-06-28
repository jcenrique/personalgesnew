<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Computo;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ComputoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Computo');
    }

    public function view(AuthUser $authUser, Computo $computo): bool
    {
        return $authUser->can('View:Computo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Computo');
    }

    public function update(AuthUser $authUser, Computo $computo): bool
    {
        return $authUser->can('Update:Computo');
    }

    public function delete(AuthUser $authUser, Computo $computo): bool
    {
        return $authUser->can('Delete:Computo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Computo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Computo');
    }
}

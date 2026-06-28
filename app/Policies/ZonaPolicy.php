<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Zona;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ZonaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Zona');
    }

    public function view(AuthUser $authUser, Zona $zona): bool
    {
        return $authUser->can('View:Zona');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Zona');
    }

    public function update(AuthUser $authUser, Zona $zona): bool
    {
        return $authUser->can('Update:Zona');
    }

    public function delete(AuthUser $authUser, Zona $zona): bool
    {
        return $authUser->can('Delete:Zona');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Zona');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Zona');
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Rechazo;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RechazoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Rechazo');
    }

    public function view(AuthUser $authUser, Rechazo $rechazo): bool
    {
        return $authUser->can('View:Rechazo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Rechazo');
    }

    public function update(AuthUser $authUser, Rechazo $rechazo): bool
    {
        return $authUser->can('Update:Rechazo');
    }

    public function delete(AuthUser $authUser, Rechazo $rechazo): bool
    {
        return $authUser->can('Delete:Rechazo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Rechazo');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Rechazo');
    }
}

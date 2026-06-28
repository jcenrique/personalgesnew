<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Estacion;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class EstacionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Estacion');
    }

    public function view(AuthUser $authUser, Estacion $estacion): bool
    {
        return $authUser->can('View:Estacion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Estacion');
    }

    public function update(AuthUser $authUser, Estacion $estacion): bool
    {
        return $authUser->can('Update:Estacion');
    }

    public function delete(AuthUser $authUser, Estacion $estacion): bool
    {
        return $authUser->can('Delete:Estacion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Estacion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Estacion');
    }
}

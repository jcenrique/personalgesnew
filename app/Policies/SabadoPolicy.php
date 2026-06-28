<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sabado;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SabadoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sabado');
    }

    public function view(AuthUser $authUser, Sabado $sabado): bool
    {
        return $authUser->can('View:Sabado');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sabado');
    }

    public function update(AuthUser $authUser, Sabado $sabado): bool
    {
        return $authUser->can('Update:Sabado');
    }

    public function delete(AuthUser $authUser, Sabado $sabado): bool
    {
        return $authUser->can('Delete:Sabado');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sabado');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Sabado');
    }
}

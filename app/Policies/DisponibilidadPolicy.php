<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Disponibilidad;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DisponibilidadPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Disponibilidad');
    }

    public function view(AuthUser $authUser, Disponibilidad $disponibilidad): bool
    {
        return $authUser->can('View:Disponibilidad');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Disponibilidad');
    }

    public function update(AuthUser $authUser, Disponibilidad $disponibilidad): bool
    {
        return $authUser->can('Update:Disponibilidad');
    }

    public function delete(AuthUser $authUser, Disponibilidad $disponibilidad): bool
    {
        return $authUser->can('Delete:Disponibilidad');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Disponibilidad');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Disponibilidad');
    }
}

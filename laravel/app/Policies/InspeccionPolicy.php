<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Inspeccion;
use Illuminate\Auth\Access\HandlesAuthorization;

class InspeccionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Inspeccion');
    }

    public function view(AuthUser $authUser, Inspeccion $inspeccion): bool
    {
        return $authUser->can('View:Inspeccion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Inspeccion');
    }

    public function update(AuthUser $authUser, Inspeccion $inspeccion): bool
    {
        return $authUser->can('Update:Inspeccion');
    }

    public function delete(AuthUser $authUser, Inspeccion $inspeccion): bool
    {
        return $authUser->can('Delete:Inspeccion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Inspeccion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Inspeccion');
    }

}
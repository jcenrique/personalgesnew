<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Elementoinspeccion;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ElementoinspeccionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Elementoinspeccion');
    }

    public function view(AuthUser $authUser, Elementoinspeccion $elementoinspeccion): bool
    {
        return $authUser->can('View:Elementoinspeccion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Elementoinspeccion');
    }

    public function update(AuthUser $authUser, Elementoinspeccion $elementoinspeccion): bool
    {
        return $authUser->can('Update:Elementoinspeccion');
    }

    public function delete(AuthUser $authUser, Elementoinspeccion $elementoinspeccion): bool
    {
        return $authUser->can('Delete:Elementoinspeccion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Elementoinspeccion');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Elementoinspeccion');
    }
}

<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Reconocimiento;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ReconocimientoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Reconocimiento');
    }

    public function view(AuthUser $authUser, Reconocimiento $reconocimiento): bool
    {
        return $authUser->can('View:Reconocimiento');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Reconocimiento');
    }

    public function update(AuthUser $authUser, Reconocimiento $reconocimiento): bool
    {
        return $authUser->can('Update:Reconocimiento');
    }

    public function delete(AuthUser $authUser, Reconocimiento $reconocimiento): bool
    {
        return $authUser->can('Delete:Reconocimiento');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Reconocimiento');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Reconocimiento');
    }
}

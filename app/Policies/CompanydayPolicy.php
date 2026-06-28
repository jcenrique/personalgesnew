<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Companyday;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CompanydayPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Companyday');
    }

    public function view(AuthUser $authUser, Companyday $companyday): bool
    {
        return $authUser->can('View:Companyday');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Companyday');
    }

    public function update(AuthUser $authUser, Companyday $companyday): bool
    {
        return $authUser->can('Update:Companyday');
    }

    public function delete(AuthUser $authUser, Companyday $companyday): bool
    {
        return $authUser->can('Delete:Companyday');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Companyday');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Companyday');
    }
}

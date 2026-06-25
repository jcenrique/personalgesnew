<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Additionalday;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdditionaldayPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Additionalday');
    }

    public function view(AuthUser $authUser, Additionalday $additionalday): bool
    {
        return $authUser->can('View:Additionalday');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Additionalday');
    }

    public function update(AuthUser $authUser, Additionalday $additionalday): bool
    {
        return $authUser->can('Update:Additionalday');
    }

    public function delete(AuthUser $authUser, Additionalday $additionalday): bool
    {
        return $authUser->can('Delete:Additionalday');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Additionalday');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Additionalday');
    }

}
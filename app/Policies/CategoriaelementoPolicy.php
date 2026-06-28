<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Categoriaelemento;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CategoriaelementoPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Categoriaelemento');
    }

    public function view(AuthUser $authUser, Categoriaelemento $categoriaelemento): bool
    {
        return $authUser->can('View:Categoriaelemento');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Categoriaelemento');
    }

    public function update(AuthUser $authUser, Categoriaelemento $categoriaelemento): bool
    {
        return $authUser->can('Update:Categoriaelemento');
    }

    public function delete(AuthUser $authUser, Categoriaelemento $categoriaelemento): bool
    {
        return $authUser->can('Delete:Categoriaelemento');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Categoriaelemento');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Categoriaelemento');
    }
}

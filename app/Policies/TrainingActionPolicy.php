<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TrainingAction;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrainingActionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TrainingAction');
    }

    public function view(AuthUser $authUser, TrainingAction $trainingAction): bool
    {
        return $authUser->can('View:TrainingAction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TrainingAction');
    }

    public function update(AuthUser $authUser, TrainingAction $trainingAction): bool
    {
        return $authUser->can('Update:TrainingAction');
    }

    public function delete(AuthUser $authUser, TrainingAction $trainingAction): bool
    {
        return $authUser->can('Delete:TrainingAction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TrainingAction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TrainingAction');
    }

}
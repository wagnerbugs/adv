<?php

namespace App\Policies;

use App\Models\Prospection;
use App\Models\User;

class ProspectionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_prospections');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Prospection $prospection): bool
    {
        return $user->hasPermissionTo('view_prospections');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_prospections');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Prospection $prospection): bool
    {
        return $user->hasPermissionTo('update_prospections');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Prospection $prospection): bool
    {
        return $user->hasPermissionTo('delete_prospections');
    }
}

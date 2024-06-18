<?php

namespace App\Policies;

use App\Models\StateCourt;
use App\Models\User;

class StateCourtPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_state_courts');
    }

    public function view(User $user, StateCourt $stateCourt): bool
    {
        return $user->hasPermissionTo('view_state_courts');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_state_courts');
    }

    public function update(User $user, StateCourt $stateCourt): bool
    {
        return $user->hasPermissionTo('update_state_courts');
    }

    public function delete(User $user, StateCourt $stateCourt): bool
    {
        return $user->hasPermissionTo('delete_state_courts');
    }
}

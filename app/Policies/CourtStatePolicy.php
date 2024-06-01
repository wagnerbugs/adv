<?php

namespace App\Policies;

use App\Models\CourtState;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CourtStatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_court_states');
    }

    public function view(User $user, CourtState $courtState): bool
    {
        return $user->hasPermissionTo('view_court_states');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_court_states');
    }

    public function update(User $user, CourtState $courtState): bool
    {
        return $user->hasPermissionTo('update_court_states');
    }

    public function delete(User $user, CourtState $courtState): bool
    {
        return $user->hasPermissionTo('delete_court_states');
    }
}

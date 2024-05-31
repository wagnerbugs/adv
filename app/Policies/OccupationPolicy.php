<?php

namespace App\Policies;

use App\Models\Occupation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OccupationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_occupations');
    }

    public function view(User $user, Occupation $occupation): bool
    {
        return $user->hasPermissionTo('view_occupations');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_occupations');
    }

    public function update(User $user, Occupation $occupation): bool
    {
        return $user->hasPermissionTo('update_occupations');
    }

    public function delete(User $user, Occupation $occupation): bool
    {
        return $user->hasPermissionTo('delete_occupations');
    }
}

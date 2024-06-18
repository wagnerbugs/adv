<?php

namespace App\Policies;

use App\Models\Court;
use App\Models\User;

class CourtPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_courts');
    }

    public function view(User $user, Court $court): bool
    {
        return $user->hasPermissionTo('view_courts');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_courts');
    }

    public function update(User $user, Court $court): bool
    {
        return $user->hasPermissionTo('update_courts');
    }

    public function delete(User $user, Court $court): bool
    {
        return $user->hasPermissionTo('delete_courts');
    }
}

<?php

namespace App\Policies;

use App\Models\OccupationFamily;
use App\Models\User;

class OccupationFamilyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_occupation_families');
    }

    public function view(User $user, OccupationFamily $occupationFamily): bool
    {
        return $user->hasPermissionTo('view_occupation_families');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_occupation_families');
    }

    public function update(User $user, OccupationFamily $occupationFamily): bool
    {
        return $user->hasPermissionTo('update_occupation_families');
    }

    public function delete(User $user, OccupationFamily $occupationFamily): bool
    {
        return $user->hasPermissionTo('delete_occupation_families');
    }
}

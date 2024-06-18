<?php

namespace App\Policies;

use App\Models\CourtDistrict;
use App\Models\User;

class CourtDistrictPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_court_districts');
    }

    public function view(User $user, CourtDistrict $courtDistrict): bool
    {
        return $user->hasPermissionTo('view_court_districts');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_court_districts');
    }

    public function update(User $user, CourtDistrict $courtDistrict): bool
    {
        return $user->hasPermissionTo('update_court_districts');
    }

    public function delete(User $user, CourtDistrict $courtDistrict): bool
    {
        return $user->hasPermissionTo('delete_court_districts');
    }
}

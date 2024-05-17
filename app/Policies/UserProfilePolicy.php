<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\Response;

class UserProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_user_profiles');
    }

    public function view(User $user, UserProfile $userProfile): bool
    {
        return $user->hasPermissionTo('view_user_profiles');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_user_profiles');
    }

    public function update(User $user, UserProfile $userProfile): bool
    {
        return $user->hasPermissionTo('update_user_profiles');
    }

    public function delete(User $user, UserProfile $userProfile): bool
    {
        return $user->hasPermissionTo('delete_user_profiles');
    }
}

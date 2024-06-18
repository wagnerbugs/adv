<?php

namespace App\Policies;

use App\Models\Process;
use App\Models\User;

class ProcessPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_processes');
    }

    public function view(User $user, Process $process): bool
    {
        return $user->hasPermissionTo('view_processes');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_processes');
    }

    public function update(User $user, Process $process): bool
    {
        return $user->hasPermissionTo('update_processes');
    }

    public function delete(User $user, Process $process): bool
    {
        return $user->hasPermissionTo('delete_processes');
    }
}

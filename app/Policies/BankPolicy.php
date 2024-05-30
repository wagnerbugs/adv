<?php

namespace App\Policies;

use App\Models\Bank;
use App\Models\User;

class BankPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_banks');
    }

    public function view(User $user, Bank $bank): bool
    {
        return $user->hasPermissionTo('view_banks');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_banks');
    }

    public function update(User $user, Bank $bank): bool
    {
        return $user->hasPermissionTo('update_banks');
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $user->hasPermissionTo('delete_banks');
    }
}

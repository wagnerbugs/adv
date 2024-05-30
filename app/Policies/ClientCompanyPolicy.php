<?php

namespace App\Policies;

use App\Models\ClientCompany;
use App\Models\User;

class ClientCompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_client_companies');
    }

    public function view(User $user, ClientCompany $clientCompany): bool
    {
        return $user->hasPermissionTo('view_client_companies');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_client_companies');
    }

    public function update(User $user, ClientCompany $clientCompany): bool
    {
        return $user->hasPermissionTo('update_client_companies');
    }

    public function delete(User $user, ClientCompany $clientCompany): bool
    {
        return $user->hasPermissionTo('delete_client_companies');
    }
}

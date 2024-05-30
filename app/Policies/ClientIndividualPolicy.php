<?php

namespace App\Policies;

use App\Models\ClientIndividual;
use App\Models\User;

class ClientIndividualPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_client_individuals');
    }

    public function view(User $user, ClientIndividual $clientIndividual): bool
    {
        return $user->hasPermissionTo('view_client_individuals');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_client_individuals');
    }

    public function update(User $user, ClientIndividual $clientIndividual): bool
    {
        return $user->hasPermissionTo('update_client_individuals');
    }

    public function delete(User $user, ClientIndividual $clientIndividual): bool
    {
        return $user->hasPermissionTo('delete_client_individuals');
    }
}

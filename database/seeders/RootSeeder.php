<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RootSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'access_admin_panel']);

        //USER
        Permission::create(['name' => 'view_any_users']);
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'create_users']);
        Permission::create(['name' => 'update_users']);
        Permission::create(['name' => 'delete_users']);

        //ROLE
        Permission::create(['name' => 'view_any_roles']);
        Permission::create(['name' => 'view_roles']);
        Permission::create(['name' => 'create_roles']);
        Permission::create(['name' => 'update_roles']);
        Permission::create(['name' => 'delete_roles']);

        //PERMISSION
        Permission::create(['name' => 'view_any_permissions']);
        Permission::create(['name' => 'view_permissions']);
        Permission::create(['name' => 'create_permissions']);
        Permission::create(['name' => 'update_permissions']);
        Permission::create(['name' => 'delete_permissions']);

        //PERMISSION
        Permission::create(['name' => 'view_any_user_profiles']);
        Permission::create(['name' => 'view_user_profiles']);
        Permission::create(['name' => 'create_user_profiles']);
        Permission::create(['name' => 'update_user_profiles']);
        Permission::create(['name' => 'delete_user_profiles']);

        //CLIENT
        Permission::create(['name' => 'view_any_clients']);
        Permission::create(['name' => 'view_clients']);
        Permission::create(['name' => 'create_clients']);
        Permission::create(['name' => 'update_clients']);
        Permission::create(['name' => 'delete_clients']);

        //CLIENT INDIVIDUAL
        Permission::create(['name' => 'view_any_client_individuals']);
        Permission::create(['name' => 'view_client_individuals']);
        Permission::create(['name' => 'create_client_individuals']);
        Permission::create(['name' => 'update_client_individuals']);
        Permission::create(['name' => 'delete_client_individuals']);

        //CLIENT COMPANY
        Permission::create(['name' => 'view_any_client_companies']);
        Permission::create(['name' => 'view_client_companies']);
        Permission::create(['name' => 'create_client_companies']);
        Permission::create(['name' => 'update_client_companies']);
        Permission::create(['name' => 'delete_client_companies']);

        //BANK
        Permission::create(['name' => 'view_any_banks']);
        Permission::create(['name' => 'view_banks']);
        Permission::create(['name' => 'create_banks']);
        Permission::create(['name' => 'update_banks']);
        Permission::create(['name' => 'delete_banks']);

        //OCCUPATION
        Permission::create(['name' => 'view_any_occupations']);
        Permission::create(['name' => 'view_occupations']);
        Permission::create(['name' => 'create_occupations']);
        Permission::create(['name' => 'update_occupations']);
        Permission::create(['name' => 'delete_occupations']);

        //OCCUPATION FAMILY
        Permission::create(['name' => 'view_any_occupation_families']);
        Permission::create(['name' => 'view_occupation_families']);
        Permission::create(['name' => 'create_occupation_families']);
        Permission::create(['name' => 'update_occupation_families']);
        Permission::create(['name' => 'delete_occupation_families']);

        //COURT
        Permission::create(['name' => 'view_any_courts']);
        Permission::create(['name' => 'view_courts']);
        Permission::create(['name' => 'create_courts']);
        Permission::create(['name' => 'update_courts']);
        Permission::create(['name' => 'delete_courts']);

        //COURT STATE
        Permission::create(['name' => 'view_any_court_states']);
        Permission::create(['name' => 'view_court_states']);
        Permission::create(['name' => 'create_court_states']);
        Permission::create(['name' => 'update_court_states']);
        Permission::create(['name' => 'delete_court_states']);

        //COURT DISTRICT
        Permission::create(['name' => 'view_any_court_districts']);
        Permission::create(['name' => 'view_court_districts']);
        Permission::create(['name' => 'create_court_districts']);
        Permission::create(['name' => 'update_court_districts']);
        Permission::create(['name' => 'delete_court_districts']);

        //PROCESS
        Permission::create(['name' => 'view_any_processes']);
        Permission::create(['name' => 'view_processes']);
        Permission::create(['name' => 'create_processes']);
        Permission::create(['name' => 'update_processes']);
        Permission::create(['name' => 'delete_processes']);

        //PROSPECTION
        Permission::create(['name' => 'view_any_prospections']);
        Permission::create(['name' => 'view_prospections']);
        Permission::create(['name' => 'create_prospections']);
        Permission::create(['name' => 'update_prospections']);
        Permission::create(['name' => 'delete_prospections']);

        User::withoutEvents(function () {
            $role = Role::create(['name' => 'Root']);
            $role->givePermissionTo('access_admin_panel');

            $role->givePermissionTo('view_any_users');
            $role->givePermissionTo('view_users');
            $role->givePermissionTo('create_users');
            $role->givePermissionTo('update_users');
            $role->givePermissionTo('delete_users');

            $role->givePermissionTo('view_any_roles');
            $role->givePermissionTo('view_roles');
            $role->givePermissionTo('create_roles');
            $role->givePermissionTo('update_roles');
            $role->givePermissionTo('delete_roles');

            $role->givePermissionTo('view_any_permissions');
            $role->givePermissionTo('view_permissions');
            $role->givePermissionTo('create_permissions');
            $role->givePermissionTo('update_permissions');
            $role->givePermissionTo('delete_permissions');

            $role->givePermissionTo('view_any_user_profiles');
            $role->givePermissionTo('view_user_profiles');
            $role->givePermissionTo('create_user_profiles');
            $role->givePermissionTo('update_user_profiles');
            $role->givePermissionTo('delete_user_profiles');

            $role->givePermissionTo('view_any_clients');
            $role->givePermissionTo('view_clients');
            $role->givePermissionTo('create_clients');
            $role->givePermissionTo('update_clients');
            $role->givePermissionTo('delete_clients');

            $role->givePermissionTo('view_any_client_individuals');
            $role->givePermissionTo('view_client_individuals');
            $role->givePermissionTo('create_client_individuals');
            $role->givePermissionTo('update_client_individuals');
            $role->givePermissionTo('delete_client_individuals');

            $role->givePermissionTo('view_any_client_companies');
            $role->givePermissionTo('view_client_companies');
            $role->givePermissionTo('create_client_companies');
            $role->givePermissionTo('update_client_companies');
            $role->givePermissionTo('delete_client_companies');

            $role->givePermissionTo('view_any_banks');
            $role->givePermissionTo('view_banks');
            $role->givePermissionTo('create_banks');
            $role->givePermissionTo('update_banks');
            $role->givePermissionTo('delete_banks');

            $role->givePermissionTo('view_any_occupations');
            $role->givePermissionTo('view_occupations');
            $role->givePermissionTo('create_occupations');
            $role->givePermissionTo('update_occupations');
            $role->givePermissionTo('delete_occupations');

            $role->givePermissionTo('view_any_occupation_families');
            $role->givePermissionTo('view_occupation_families');
            $role->givePermissionTo('create_occupation_families');
            $role->givePermissionTo('update_occupation_families');
            $role->givePermissionTo('delete_occupation_families');

            $role->givePermissionTo('view_any_courts');
            $role->givePermissionTo('view_courts');
            $role->givePermissionTo('create_courts');
            $role->givePermissionTo('update_courts');
            $role->givePermissionTo('delete_courts');

            $role->givePermissionTo('view_any_court_states');
            $role->givePermissionTo('view_court_states');
            $role->givePermissionTo('create_court_states');
            $role->givePermissionTo('update_court_states');
            $role->givePermissionTo('delete_court_states');

            $role->givePermissionTo('view_any_court_districts');
            $role->givePermissionTo('view_court_districts');
            $role->givePermissionTo('create_court_districts');
            $role->givePermissionTo('update_court_districts');
            $role->givePermissionTo('delete_court_districts');

            $role->givePermissionTo('view_any_processes');
            $role->givePermissionTo('view_processes');
            $role->givePermissionTo('create_processes');
            $role->givePermissionTo('update_processes');
            $role->givePermissionTo('delete_processes');

            $role->givePermissionTo('view_any_prospections');
            $role->givePermissionTo('view_prospections');
            $role->givePermissionTo('create_prospections');
            $role->givePermissionTo('update_prospections');
            $role->givePermissionTo('delete_prospections');

            $user = User::updateOrCreate([
                'name' => 'Wagner Bugs',
                'email' => 'wagnerbugs@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456789'),
            ])->assignRole($role);

            UserProfile::updateOrCreate([
                'user_id' => $user->id,
            ]);

            UserAddress::updateOrCreate([
                'user_id' => $user->id,
            ]);
        });
    }
}

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

            $user = User::create([
                'name' => 'Wagner Bugs',
                'email' => 'wagnerbugs@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456789'),
            ])->assignRole($role);

            UserProfile::create([
                'user_id' => $user->id,
            ]);

            UserAddress::create([
                'user_id' => $user->id,
            ]);
        });
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'professor']);
        Role::firstOrCreate(['name' => 'student']);

        // Optional: create permissions
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'input grades']);
        Permission::firstOrCreate(['name' => 'view grades']);

        // Assign permissions to roles
        Role::findByName('admin')->givePermissionTo(['manage users', 'input grades', 'view grades']);
        Role::findByName('professor')->givePermissionTo(['input grades', 'view grades']);
        Role::findByName('student')->givePermissionTo(['view grades']);
    }
}

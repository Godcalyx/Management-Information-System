<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = ['student', 'professor', 'admin'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }
}

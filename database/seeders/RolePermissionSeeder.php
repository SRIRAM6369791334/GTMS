<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',

            'users.create',
            'users.view',
            'users.edit',
            'users.delete',

            'roles.create',
            'roles.view',
            'roles.edit',
            'roles.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);

        $admin->syncPermissions(Permission::all());

        $staff = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'web'
        ]);

        $staff->syncPermissions([
            'dashboard.view',
            'users.view'
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // All system permissions grouped logically
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Department / Branch
            'branch.view',
            'branch.create',
            'branch.edit',
            'branch.delete',

            // Roles
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Customers
            'customer.view',
            'customer.create',
            'customer.edit',
            'customer.delete',

            // Lease Applications
            'application.view',
            'application.create',
            'application.edit',
            'application.delete',

            // Mining Plan
            'mining.view',
            'mining.create',
            'mining.edit',
            'mining.delete',

            // Environment Clearance
            'environment.view',
            'environment.b1.view',
            'environment.b2.view',
            'environment.b2.create',
            'environment.b2.upload',
            'environment.b2.review',
            'environment.b2.status',

            // EC Certificate
            'ec_certificate.view',
            'ec_certificate.create',
            'ec_certificate.edit',
            'ec_certificate.delete',
            'ec_certificate.manage',

            // EC Compliance (Half-Yearly)
            'ec_compliance.view',
            'ec_compliance.create',
            'ec_compliance.edit',
            'ec_compliance.delete',
            'ec_compliance.manage',

            // PPT Department
            'ppt.view',
            'ppt.create',
            'ppt.edit',
            'ppt.delete',
            'ppt.manage',

            // DGPS Survey
            'dgps.view',
            'dgps.create',
            'dgps.edit',
            'dgps.delete',
            'dgps.manage',

            // Drone Survey
            'drone.view',
            'drone.create',
            'drone.edit',
            'drone.delete',
            'drone.manage',

            // Accounts & Financials
            'account.view',
            'account.create',
            'account.edit',
            'account.delete',

            // Masters (Category, Product, Unit)
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'unit.view',
        ];


        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 1. Admin Role (Has all permissions)
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::all());

        // 2. Staff Role (Basic viewing permissions)
        $staffRole = Role::firstOrCreate([
            'name' => 'Staff',
            'guard_name' => 'web',
        ]);
        $staffRole->syncPermissions([
            'dashboard.view',
            'customer.view',
            'application.view',
            'mining.view',
            'environment.view',
            'environment.b2.view',
            'users.view',
            'ec_certificate.view',
            'ec_compliance.view',
            'ppt.view',
            'dgps.view',
            'drone.view',
            'account.view',
        ]);

        // 3. Officer Role
        $officerRole = Role::firstOrCreate([
            'name' => 'Officer',
            'guard_name' => 'web',
        ]);
        $officerRole->syncPermissions([
            'dashboard.view',
            'customer.view',
            'customer.create',
            'customer.edit',
            'application.view',
            'application.create',
            'application.edit',
            'mining.view',
            'mining.create',
            'mining.edit',
            'environment.view',
            'environment.b2.view',
            'environment.b2.create',
            'environment.b2.upload',
            'environment.b2.review',
            'ec_certificate.view',
            'ec_certificate.create',
            'ec_certificate.edit',
            'ec_certificate.manage',
            'ec_compliance.view',
            'ec_compliance.create',
            'ec_compliance.edit',
            'ec_compliance.manage',
            'ppt.view',
            'ppt.create',
            'ppt.edit',
            'ppt.manage',
            'dgps.view',
            'dgps.create',
            'dgps.edit',
            'dgps.manage',
            'drone.view',
            'drone.create',
            'drone.edit',
            'drone.manage',
            'account.view',
            'account.create',
            'account.edit',
            'account.delete',
        ]);


        // Default Branch if none exists
        $branch = Branch::firstOrCreate(
            ['branch_name' => 'Head Office'],
            [
                'contact_person' => 'Administrator',
                'mobile' => '9876543210',
                'address' => 'District Mining Office',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'pincode' => '600001',
                'status' => 1,
            ]
        );

        // Create or Update Default Super Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@gtms.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'show_password' => 'admin123',
                'mobile_num' => '9876543210',
                'branch_id' => $branch->id,
                'role_id' => $adminRole->id,
                'status' => 1,
                'user_code' => 'LUK_001',
            ]
        );

        $adminUser->role_id = $adminRole->id;
        $adminUser->branch_id = $branch->id;
        $adminUser->status = 1;
        $adminUser->save();

        // Assign Spatie Role
        $adminUser->syncRoles([$adminRole->name]);

        // Create or Update Staff User
        $staffUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'show_password' => 'staff123',
                'mobile_num' => '9876543211',
                'branch_id' => $branch->id,
                'role_id' => $staffRole->id,
                'status' => 1,
                'user_code' => 'LUK_002',
            ]
        );
        $staffUser->password = Hash::make('staff123');
        $staffUser->show_password = 'staff123';
        $staffUser->user_code = 'LUK_002';
        $staffUser->role_id = $staffRole->id;
        $staffUser->branch_id = $branch->id;
        $staffUser->status = 1;
        $staffUser->save();
        $staffUser->syncRoles([$staffRole->name]);

        // Create or Update Officer User
        $officerUser = User::firstOrCreate(
            ['email' => 'officer@gtms.com'],
            [
                'name' => 'Field Officer',
                'password' => Hash::make('officer123'),
                'show_password' => 'officer123',
                'mobile_num' => '9876543212',
                'branch_id' => $branch->id,
                'role_id' => $officerRole->id,
                'status' => 1,
                'user_code' => 'LUK_003',
            ]
        );
        $officerUser->password = Hash::make('officer123');
        $officerUser->show_password = 'officer123';
        $officerUser->user_code = 'LUK_003';
        $officerUser->role_id = $officerRole->id;
        $officerUser->branch_id = $branch->id;
        $officerUser->status = 1;
        $officerUser->save();
        $officerUser->syncRoles([$officerRole->name]);
    }
}



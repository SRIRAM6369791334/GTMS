<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $allPermissions = Permission::orderBy('name')->get();

        // Group permissions by prefix/module (e.g. 'users.create' => 'users')
        $modules = [
            'dashboard' => 'Dashboard',
            'branch' => 'Department / Branch',
            'roles' => 'Roles & Permissions',
            'users' => 'Users Management',
            'application' => 'Lease Applications',
            'mining' => 'Mining Plan',
            'environment' => 'Environment Clearance',
            'ec_certificate' => 'EC Certificate',
            'ppt' => 'PPT Department',
            'dgps' => 'DGPS Survey',
            'drone' => 'Drone Survey',
            'category' => 'Categories',
            'product' => 'Products',
            'unit' => 'Units',
        ];

        $groupedPermissions = [];
        foreach ($allPermissions as $perm) {
            $parts = explode('.', $perm->name);
            $moduleKey = $parts[0] ?? 'other';
            $groupedPermissions[$moduleKey][] = $perm;
        }

        return view('pages.authentication.roles.index', compact('roles', 'groupedPermissions', 'modules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $role->load('permissions');

        return response()->json([
            'status' => 1,
            'message' => 'Role Added Successfully',
            'data' => $role
        ]);
    }

    public function getPermissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'status' => 1,
            'role' => $role,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:roles,id',
            'name' => 'required|unique:roles,name,' . $request->id,
            'permissions' => 'nullable|array',
        ]);

        $role = Role::findOrFail($request->id);
        $role->name = $request->name;
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions ?? []);
        } else {
            $role->syncPermissions([]);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $role->load('permissions');

        return response()->json([
            'status' => 1,
            'message' => 'Role Updated Successfully',
            'data' => $role
        ]);
    }

    public function destroy(Request $request)
    {
        $role = Role::findOrFail($request->id);
        if ($role->name === 'Admin' || $role->name === 'Super Admin') {
            return response()->json([
                'status' => 0,
                'message' => 'Default Administrator role cannot be deleted.',
            ]);
        }

        $role->delete();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'status' => 1,
            'message' => 'Role Deleted Successfully',
            'data' => $role
        ]);
    }
}


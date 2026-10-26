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
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        if ($isSuperAdmin) {
            $roles = Role::with('permissions')->withCount('users')->orderBy('id', 'asc')->get();
            $allPermissions = Permission::orderBy('name')->get();
        } else {
            // Non-superadmin: ONLY see roles created by this user!
            $roles = Role::with('permissions')
                ->withCount('users')
                ->where('created_by', $currentUser ? $currentUser->id : 0)
                ->orderBy('id', 'asc')
                ->get();

            // Non-superadmin: only show permissions possessed by the current user
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $allPermissions = Permission::whereIn('name', $userPerms)->orderBy('name')->get();
        }

        // Group permissions by prefix/module (e.g. 'users.create' => 'users')
        $modules = [
            'dashboard' => 'Dashboard',
            'branch' => 'Department / Branch',
            'roles' => 'Roles & Permissions',
            'users' => 'Users Management',
            'customer' => 'Customer Management',
            'application' => 'Lease Applications',
            'mining' => 'Mining Plan',
            'environment' => 'Environment Clearance',
            'ec_certificate' => 'EC Certificate',
            'ec_compliance' => 'EC Compliance',
            'ppt' => 'PPT Department',
            'dgps' => 'DGPS Survey',
            'drone' => 'Drone Survey',
            'category' => 'Categories',
            'product' => 'Products',
            'unit' => 'Units',
            'account' => 'Accounts & Financials',
            'notification' => 'Notifications',
        ];

        $groupedPermissions = [];
        foreach ($allPermissions as $perm) {
            $parts = explode('.', $perm->name);
            $moduleKey = $parts[0] ?? 'other';
            $groupedPermissions[$moduleKey][] = $perm;
        }

        // Live KPI Metrics
        $totalRoles = $roles->count();
        $totalPermissions = $allPermissions->count();
        $systemRolesCount = $roles->filter(fn($r) => in_array($r->name, ['Admin', 'Super Admin']))->count();
        $customRolesCount = $totalRoles - $systemRolesCount;
        $totalAssignedUsers = $roles->sum('users_count');

        return view('pages.authentication.roles.index', compact(
            'roles', 
            'groupedPermissions', 
            'modules',
            'totalRoles',
            'totalPermissions',
            'systemRolesCount',
            'customRolesCount',
            'totalAssignedUsers',
            'isSuperAdmin'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        if (!$isSuperAdmin) {
            // Reserved names safeguard
            if (in_array(strtolower(trim($request->name)), ['admin', 'super admin', 'superadmin'])) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Reserved role names cannot be created.',
                ], 403);
            }

            // Subsetting safeguard: cannot grant permissions you do not possess
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $requestedPerms = $request->permissions ?? [];
            $unauthorizedPerms = array_diff($requestedPerms, $userPerms);

            if (!empty($unauthorizedPerms)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot grant permissions that you do not possess.',
                ], 403);
            }
        }

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'created_by' => auth()->id(),
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
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $role = Role::with('permissions')->findOrFail($id);

        if (!$isSuperAdmin && in_array($role->name, ['Admin', 'Super Admin'])) {
            return response()->json([
                'status'  => 0,
                'message' => 'Access denied to system administrator role definitions.',
            ], 403);
        }

        if (!$isSuperAdmin && $role->created_by != ($currentUser ? $currentUser->id : 0)) {
            return response()->json([
                'status'  => 0,
                'message' => 'Access denied to roles created by other administrators.',
            ], 403);
        }

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
            'name' => 'required|string|max:255|unique:roles,name,' . $request->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $role = Role::findOrFail($request->id);

        // Safeguard Admin and Super Admin from unauthorized rename or total permission wipe
        if ($role->name === 'Admin' || $role->name === 'Super Admin') {
            if (!$isSuperAdmin) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You do not have permission to modify an Administrator role.',
                ], 403);
            }

            if ($request->name !== $role->name) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Default Administrator role cannot be renamed.',
                ]);
            }

            // Ensure Admin retains full access
            $role->syncPermissions(Permission::all());
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            $role->load('permissions');

            return response()->json([
                'status' => 1,
                'message' => 'Administrator role permissions preserved with full system access.',
                'data' => $role
            ]);
        }

        if (!$isSuperAdmin && $role->created_by != ($currentUser ? $currentUser->id : 0)) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to modify roles created by other administrators.',
            ], 403);
        }

        if (!$isSuperAdmin) {
            if (in_array(strtolower(trim($request->name)), ['admin', 'super admin', 'superadmin']) && $request->name !== $role->name) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'Reserved role names cannot be used.',
                ], 403);
            }

            // Subsetting safeguard: cannot grant permissions you do not possess
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $requestedPerms = $request->permissions ?? [];
            $unauthorizedPerms = array_diff($requestedPerms, $userPerms);

            if (!empty($unauthorizedPerms)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot grant permissions that you do not possess.',
                ], 403);
            }
        }

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
        $request->validate([
            'id' => 'required|exists:roles,id',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $role = Role::findOrFail($request->id);
        if ($role->name === 'Admin' || $role->name === 'Super Admin') {
            return response()->json([
                'status' => 0,
                'message' => 'Default Administrator role cannot be deleted.',
            ]);
        }

        if (!$isSuperAdmin && $role->created_by != ($currentUser ? $currentUser->id : 0)) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to delete roles created by other administrators.',
            ], 403);
        }

        if (!$isSuperAdmin) {
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $rolePerms = $role->permissions->pluck('name')->toArray();
            if (!empty(array_diff($rolePerms, $userPerms))) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You do not have permission to delete a role with higher privileges.',
                ], 403);
            }
        }

        // Check if any users are assigned to this role
        $assignedUsersCount = \App\Models\User::where('role_id', $role->id)
            ->orWhereHas('roles', fn($q) => $q->where('id', $role->id))
            ->count();

        if ($assignedUsersCount > 0) {
            return response()->json([
                'status' => 0,
                'message' => "Cannot delete role '{$role->name}' because {$assignedUsersCount} user(s) are currently assigned to it. Reassign or remove these users first.",
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


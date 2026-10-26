<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        if ($isSuperAdmin) {
            $users = User::with(['role', 'roles', 'branch'])->get();
            $role = Role::with('permissions')->get();
            $branch = Branch::where('status', 1)->get();
        } else {
            // Non-superadmin:
            // 1. Directory: only show users created by this user AND their own profile
            $users = User::with(['role', 'roles', 'branch'])
                ->where(function ($q) use ($currentUser) {
                    $q->where('id', $currentUser ? $currentUser->id : 0)
                      ->orWhere('user_id', $currentUser ? $currentUser->id : 0);
                })
                ->get();

            // 2. Roles: only show roles created by this user that are subsets of their permissions
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $role = Role::with('permissions')
                ->where('created_by', $currentUser ? $currentUser->id : 0)
                ->whereNotIn('name', ['Super Admin', 'Admin'])
                ->get()
                ->filter(function ($r) use ($userPerms) {
                    $rolePerms = $r->permissions->pluck('name')->toArray();
                    return empty(array_diff($rolePerms, $userPerms));
                })
                ->values();

            // 3. Branch: non-superadmin can only assign personnel to their own assigned branch
            $branch = Branch::where('status', 1)
                ->where('id', $currentUser ? $currentUser->branch_id : 0)
                ->get();
        }

        return view('pages.authentication.users.index', compact('users', 'role', 'branch', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'user_code'  => 'nullable|string|max:50|unique:users,user_code',
            'password'   => 'required|min:6',
            'role_id'    => 'required|exists:roles,id',
            'branch_id'  => 'required|exists:branches,id',
            'mobile_num' => 'nullable|digits_between:10,15',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'user_code.unique'          => 'This User ID / Personnel Code is already registered to another user.',
            'branch_id.required'        => 'Please select a branch / department.',
            'mobile_num.digits_between' => 'Phone number must be between 10 and 15 digits.',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $targetRole = Role::with('permissions')->findOrFail($request->role_id);

        // Security Check 1: Non-superadmin cannot assign Super Admin or Admin roles
        if (!$isSuperAdmin) {
            if (in_array($targetRole->name, ['Super Admin', 'Admin'])) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You do not have permission to assign an Administrator role.',
                ], 403);
            }

            // Security Check 2: Non-superadmin can only assign roles created by their own account
            if ($targetRole->created_by != ($currentUser ? $currentUser->id : 0)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You can only assign roles created by your own account.',
                ], 403);
            }

            // Security Check 3: Non-superadmin cannot assign a role with permissions exceeding their own
            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $rolePerms = $targetRole->permissions->pluck('name')->toArray();
            $unauthorizedPerms = array_diff($rolePerms, $userPerms);

            if (!empty($unauthorizedPerms)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot assign a role containing permissions you do not possess.',
                ], 403);
            }

            // Security Check 4: Non-superadmin can only assign personnel to their own branch
            if ($request->branch_id != ($currentUser ? $currentUser->branch_id : 0)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You can only assign personnel to your own branch office.',
                ], 403);
            }
        }

        $user = new User();
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        $user->user_id = auth()->id();
        $user->status = $request->input('status', 1);
        $user->remember_token = Str::random(10);
        $user->mobile_num = trim($request->mobile_num ?? '');
        $user->show_password = $request->password;

        // Upload Image
        if ($request->hasFile('image')) {
            $uploadDir = public_path('uploads/users');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadDir, $imageName);
            $user->image = $imageName;
        }

        // Save first to get auto-increment ID
        $user->save();

        // Assign User ID (manual user_code if provided, else auto-generate LUK_001, ...)
        if ($request->filled('user_code')) {
            $user->user_code = trim($request->user_code);
        } else {
            $user->user_code = 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
        }
        $user->save();

        // Sync Spatie Role
        $role = Role::find($request->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
        }

        $user->load(['role', 'roles', 'branch']);

        return response()->json([
            'status'  => 1,
            'message' => 'User Added Successfully',
            'data'    => $user
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'         => 'required|exists:users,id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $request->id,
            'user_code'  => 'nullable|string|max:50|unique:users,user_code,' . $request->id,
            'password'   => 'nullable|min:6',
            'role_id'    => 'required|exists:roles,id',
            'branch_id'  => 'required|exists:branches,id',
            'mobile_num' => 'nullable|digits_between:10,15',
            'status'     => 'nullable|in:0,1',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'user_code.unique'          => 'This User ID / Personnel Code is already registered to another user.',
            'branch_id.required'        => 'Please select a branch / department.',
            'mobile_num.digits_between' => 'Phone number must be between 10 and 15 digits.',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $user = User::findOrFail($request->id);

        // Security Check 1: Non-superadmin cannot modify a Super Admin or Admin account
        if (!$isSuperAdmin && ($user->hasRole('Super Admin') || $user->hasRole('Admin'))) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to modify an Administrator account.',
            ], 403);
        }

        // Security Check 2: Non-superadmin can only modify users created by themselves or their own account
        if (!$isSuperAdmin && $user->id != ($currentUser ? $currentUser->id : 0) && $user->user_id != ($currentUser ? $currentUser->id : 0)) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to modify users created by other administrators.',
            ], 403);
        }

        // Security Check 3: Self-Escalation Lock (Users cannot modify their own security role)
        if ($currentUser && $currentUser->id == $user->id) {
            if ($request->role_id != $user->role_id) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot modify your own security role. Please contact a Super Administrator.',
                ], 403);
            }
        }

        // Security Check 4: Role assignment validation for non-superadmin
        $targetRole = Role::with('permissions')->findOrFail($request->role_id);
        if (!$isSuperAdmin) {
            if (in_array($targetRole->name, ['Super Admin', 'Admin'])) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You do not have permission to assign an Administrator role.',
                ], 403);
            }

            if ($targetRole->created_by != ($currentUser ? $currentUser->id : 0)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You can only assign roles created by your own account.',
                ], 403);
            }

            $userPerms = $currentUser ? $currentUser->getAllPermissions()->pluck('name')->toArray() : [];
            $rolePerms = $targetRole->permissions->pluck('name')->toArray();
            $unauthorizedPerms = array_diff($rolePerms, $userPerms);

            if (!empty($unauthorizedPerms)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot assign a role containing permissions you do not possess.',
                ], 403);
            }

            // Security Check 5: Non-superadmin cannot transfer users to another branch
            if ($request->branch_id != ($currentUser ? $currentUser->branch_id : 0)) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'You cannot transfer personnel to another branch office.',
                ], 403);
            }
        }

        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if ($request->filled('user_code')) {
            $user->user_code = trim($request->user_code);
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->show_password = $request->password;
        }
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        if ($request->has('status')) {
            $user->status = (int) $request->status;
        }
        $user->mobile_num = trim($request->mobile_num ?? '');

        // Upload Image
        if ($request->hasFile('image')) {
            $uploadDir = public_path('uploads/users');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if ($user->image && file_exists($uploadDir . '/' . $user->image)) {
                unlink($uploadDir . '/' . $user->image);
            }
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move($uploadDir, $imageName);
            $user->image = $imageName;
        }

        $user->save();

        // Sync Spatie Role
        $role = Role::find($request->role_id);
        if ($role) {
            $user->syncRoles([$role->name]);
        }

        $user->load(['role', 'roles', 'branch']);

        return response()->json([
            'status'  => 1,
            'message' => 'User Updated Successfully',
            'data'    => $user
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && ($currentUser->hasRole('Super Admin') || $currentUser->hasRole('Admin'));

        $user = User::findOrFail($request->id);

        if (auth()->id() == $user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'You cannot delete your own account.',
            ]);
        }

        if (($user->hasRole('Admin') && User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->count() <= 1)
            || ($user->hasRole('Super Admin') && User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->count() <= 1)) {
            return response()->json([
                'status' => 0,
                'message' => 'The last Admin account cannot be deleted.',
            ]);
        }

        // Security Check 1: Non-superadmin cannot delete a Super Admin or Admin account
        if (!$isSuperAdmin && ($user->hasRole('Super Admin') || $user->hasRole('Admin'))) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to delete an Administrator account.',
            ]);
        }

        // Security Check 2: Non-superadmin can only delete users created by themselves
        if (!$isSuperAdmin && $user->user_id != ($currentUser ? $currentUser->id : 0)) {
            return response()->json([
                'status'  => 0,
                'message' => 'You do not have permission to delete users created by other administrators.',
            ]);
        }

        // Integrity safeguard: check if user has created records in statutory application tables
        $hasStatutoryRecords = \Illuminate\Support\Facades\DB::table('customers')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('lease_applications')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('mining_applications')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('environment_projects')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('dgps_surveys')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('drone_surveys')->where('created_by', $user->id)->exists()
            || \Illuminate\Support\Facades\DB::table('ec_certificates')->where('created_by', $user->id)->exists();

        if ($hasStatutoryRecords) {
            return response()->json([
                'status' => 0,
                'message' => 'Cannot delete user because they have recorded statutory applications or customer filings. You can deactivate their account instead.',
            ]);
        }

        // Delete image if exists
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }

        $user->delete();

        return response()->json([
            'status'  => 1,
            'message' => 'User Deleted Successfully',
        ]);
    }

}

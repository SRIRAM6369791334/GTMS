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
        $users = User::with(['role', 'roles', 'branch'])->get();
        $role = Role::with('permissions')->get();
        $branch = Branch::where('status', 1)->get();
        return view('pages.authentication.users.index', compact('users', 'role', 'branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6',
            'role_id'    => 'required|exists:roles,id',
            'branch_id'  => 'required|exists:branches,id',
            'mobile_num' => 'nullable|digits_between:10,15',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'branch_id.required'        => 'Please select a branch / department.',
            'mobile_num.digits_between' => 'Phone number must be between 10 and 15 digits.',
        ]);

        $user = new User();
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
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

        // Generate User ID (LUK_001, LUK_002, ...)
        $user->user_code = 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
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
            'password'   => 'nullable|min:6',
            'role_id'    => 'required|exists:roles,id',
            'branch_id'  => 'required|exists:branches,id',
            'mobile_num' => 'nullable|digits_between:10,15',
            'status'     => 'nullable|in:0,1',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'branch_id.required'        => 'Please select a branch / department.',
            'mobile_num.digits_between' => 'Phone number must be between 10 and 15 digits.',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = trim($request->name);
        $user->email = trim($request->email);
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

        $user = User::findOrFail($request->id);

        if (auth()->id() == $user->id) {
            return response()->json([
                'status' => 0,
                'message' => 'You cannot delete your own account.',
            ]);
        }

        if ($user->hasRole('Admin') && User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->count() <= 1) {
            return response()->json([
                'status' => 0,
                'message' => 'The last Admin account cannot be deleted.',
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

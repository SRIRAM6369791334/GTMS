<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(){
       $users = User::with(['role', 'branch'])->get();
        $role = Role::get();
        $branch = Branch::where('status', 1)->get();
        return view('pages.authentication.users.index', compact('users', 'role', 'branch'));
    }

  public function store(Request $request)
{


    $request->validate([
        'name'      => 'required',
        'email'     => 'required|email|unique:users,email',
        'password'  => 'required|min:6',
        'role_id'   => 'required',
        'branch_id' => 'required',
        'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->role_id = $request->role_id;
    $user->branch_id = $request->branch_id;
    $user->status =  1;
    $user->remember_token = Str::random(10);
    $user->mobile_num = $request->mobile_num;
    $user->show_password = $request->password; // Store the plain password for display purposes (not recommended for production)

    // Upload Image
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $user->image = $imageName;
    }

    // Save first to get auto-increment ID
    $user->save();

    // Generate User ID (LUK_001, LUK_002, ...)
    $user->user_code = 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT);

    // Update only the user_id column
    $user->save();

     $user->load(['role','branch']);

    return response()->json([
        'status'  => 1,
        'message' => 'User Added Successfully',
        'data'    => $user
    ]);
}

public function update(Request $request)
{
    $request->validate([
        'id'        => 'required|exists:users,id',
        'name'      => 'required',
        'email'     => 'required|email|unique:users,email,' . $request->id,
        'password'  => 'nullable|min:6',
        'role_id'   => 'required',
        'branch_id' => 'required',
        'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    $user = User::findOrFail($request->id);
    $user->name = $request->name;
    $user->email = $request->email;
    if ($request->filled('password')) {
        $user->password = bcrypt($request->password);
        $user->show_password = $request->password; // Update the plain password for display purposes (not recommended for production)
    }
    $user->role_id = $request->role_id;
    $user->branch_id = $request->branch_id;
    $user->status = 1;
    $user->mobile_num = $request->mobile_num;

    // Upload Image
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($user->image && file_exists(public_path('uploads/users/' . $user->image))) {
            unlink(public_path('uploads/users/' . $user->image));
        }
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('uploads/users'), $imageName);
        $user->image = $imageName;
    }

    // Save the updated user
    $user->save();

     $user->load(['role','branch']);

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RolesController extends Controller
{
    public function index(){
        $roles = Role::get();
        return view('pages.authentication.roles.index', compact('roles'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = new Role();
        $role->name = $request->name;
        $role->guard_name = 'web';
        $role->save();

         return response()->json([
        'status'=>1,
        'message'=>'Role Added Successfully',
        'data'=>$role
    ]);
    }

    public function update(Request $request){
        $request->validate([
            'name' => 'required|unique:roles,name,'.$request->id,
        ]);

        $role = Role::find($request->id);
        $role->name = $request->name;
        $role->save();

         return response()->json([
        'status'=>1,
        'message'=>'Role Updated Successfully',
        'data'=>$role
    ]);
    }

    public function destroy(Request $request){
        $role = Role::find($request->id);
        $role->delete();

         return response()->json([
        'status'=>1,
        'message'=>'Role Deleted Successfully',
        'data'=>$role
    ]);
    }
}

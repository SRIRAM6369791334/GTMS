<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
     public function index(){

     $branches = Branch::get();
     return view('pages.authentication.branch.index', compact('branches'));
     }

     public function store(Request $request){

        $request->validate([
            'branch_name' => 'required',
            'contact_person' => 'required',
            'mobile' => 'required',
            'address' => 'required',
        ]);

        $branch = new Branch();
        $branch->branch_name = $request->branch_name;
        $branch->contact_person = $request->contact_person;
        $branch->mobile = $request->mobile;
        $branch->address = $request->address;
        $branch->city = $request->city;
        $branch->state = $request->state;
        $branch->pincode = $request->pincode;
        $branch->status = 1;
        $branch->save();

         return response()->json([
        'status'=>1,
        'message'=>'Branch Added Successfully',
        'data'=>$branch
    ]);
     }

     public function update(Request $request){

        $request->validate([
            'branch_name' => 'required',
            'contact_person' => 'required',
            'mobile' => 'required',
            'address' => 'required',
        ]);

        $branch = Branch::find($request->id);
        $branch->branch_name = $request->branch_name;
        $branch->contact_person = $request->contact_person;
        $branch->mobile = $request->mobile;
        $branch->address = $request->address;
        $branch->city = $request->city;
        $branch->state = $request->state;
        $branch->pincode = $request->pincode;
        $branch->status = $request->status;
        $branch->save();

         return response()->json([
        'status'=>1,
        'message'=>'Branch Updated Successfully',
        'data'=>$branch
    ]);
     }

     public function destroy(Request $request){

        $branch = Branch::find($request->id);
        $branch->delete();

         return response()->json([
        'status'=>1,
        'message'=>'Branch Deleted Successfully',
        'data'=>$branch
    ]);
     }
}

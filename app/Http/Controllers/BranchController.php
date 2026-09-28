<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('id', 'asc')->get();
        return view('pages.authentication.branch.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_name'    => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile'         => 'required|digits_between:10,15',
            'address'        => 'required|string',
            'city'           => 'nullable|string|max:255',
            'state'          => 'nullable|string|max:255',
            'pincode'        => 'nullable|digits:6',
        ], [
            'mobile.digits_between' => 'Phone number must be between 10 and 15 digits.',
            'pincode.digits'        => 'Pincode must be a valid 6-digit number.',
        ]);

        $branch = new Branch();
        $branch->branch_name    = trim($request->branch_name);
        $branch->contact_person = trim($request->contact_person);
        $branch->mobile         = trim($request->mobile);
        $branch->address        = trim($request->address);
        $branch->city           = trim($request->city ?? '');
        $branch->state          = trim($request->state ?? '');
        $branch->pincode        = trim($request->pincode ?? '');
        $branch->status         = 1;
        $branch->save();

        return response()->json([
            'status'  => 1,
            'message' => 'Branch Added Successfully',
            'data'    => $branch
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'             => 'required|exists:branches,id',
            'branch_name'    => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'mobile'         => 'required|digits_between:10,15',
            'address'        => 'required|string',
            'city'           => 'nullable|string|max:255',
            'state'          => 'nullable|string|max:255',
            'pincode'        => 'nullable|digits:6',
            'status'         => 'nullable|in:0,1',
        ], [
            'mobile.digits_between' => 'Phone number must be between 10 and 15 digits.',
            'pincode.digits'        => 'Pincode must be a valid 6-digit number.',
        ]);

        $branch = Branch::find($request->id);
        if (!$branch) {
            return response()->json([
                'status'  => 0,
                'message' => 'Branch not found.',
            ]);
        }

        $branch->branch_name    = trim($request->branch_name);
        $branch->contact_person = trim($request->contact_person);
        $branch->mobile         = trim($request->mobile);
        $branch->address        = trim($request->address);
        $branch->city           = trim($request->city ?? '');
        $branch->state          = trim($request->state ?? '');
        $branch->pincode        = trim($request->pincode ?? '');
        
        $statusChangedToInactive = false;
        if ($request->has('status')) {
            if ($branch->status == 1 && $request->status == 0) {
                $statusChangedToInactive = true;
            }
            $branch->status = (int) $request->status;
        }
        $branch->save();

        $message = 'Branch Updated Successfully';
        if ($statusChangedToInactive) {
            $userCount = User::where('branch_id', $branch->id)->count();
            if ($userCount > 0) {
                $message = "Department deactivated. Note: {$userCount} existing user(s) remain assigned to it.";
            }
        }

        return response()->json([
            'status'  => 1,
            'message' => $message,
            'data'    => $branch
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::find($request->id);
        if (!$branch) {
            return response()->json([
                'status'  => 0,
                'message' => 'Branch not found.',
            ]);
        }

        // 1. Safeguard: Last remaining branch cannot be deleted
        if (Branch::count() <= 1) {
            return response()->json([
                'status'  => 0,
                'message' => 'The last remaining Department / Branch cannot be deleted.',
            ]);
        }

        // 2. Safeguard: Cannot delete department assigned to current logged-in user
        if (auth()->check() && auth()->user()->branch_id == $branch->id) {
            return response()->json([
                'status'  => 0,
                'message' => 'You cannot delete the department your own account is assigned to.',
            ]);
        }

        // 3. Safeguard: Cannot delete department if it contains the only remaining Admin account
        $branchAdminCount = User::where('branch_id', $branch->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'Admin'))
            ->count();
        $totalAdminCount = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->count();

        if ($branchAdminCount > 0 && ($totalAdminCount - $branchAdminCount) < 1) {
            return response()->json([
                'status'  => 0,
                'message' => 'Cannot delete this department because it contains the only remaining Admin account.',
            ]);
        }

        // 4. Cascade delete: remove all users assigned to this branch and the branch itself atomically
        $deletedUserCount = 0;
        DB::transaction(function () use ($branch, &$deletedUserCount) {
            $deletedUserCount = User::where('branch_id', $branch->id)->count();
            User::where('branch_id', $branch->id)->delete();
            $branch->delete();
        });

        $msg = 'Branch Deleted Successfully';
        if ($deletedUserCount > 0) {
            $msg = "Branch and {$deletedUserCount} associated user account(s) deleted successfully.";
        }

        return response()->json([
            'status'  => 1,
            'message' => $msg,
            'data'    => $branch
        ]);
    }
}

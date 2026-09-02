<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::where('delete_status', 0)->get();
        return view('pages.master.unit.index', compact('units'));
    }
}

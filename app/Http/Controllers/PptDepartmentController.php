<?php

namespace App\Http\Controllers;

class PptDepartmentController extends Controller
{
    public function index()
    {
        return view('pages.ppt_department.index');
    }

    public function wizard(int $step)
    {
        abort_unless($step >= 1 && $step <= 7, 404);
        return view('pages.ppt_department.wizard', compact('step'));
    }
}

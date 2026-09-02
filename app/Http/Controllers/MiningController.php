<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MiningController extends Controller
{
    public function index()
    {
        return view('pages.mining-portal.index');
    }

    public function newApplication()
    {
        return view('pages.mining-portal.newapplication');
    }

    public function projectFolder()
    {
        return view('pages.mining-portal.projectfolder');
    }

    public function Document()
    {
        return view('pages.mining-portal.document');
    }

    public function Process()
    {
        return view('pages.mining-portal.process');
    }
}

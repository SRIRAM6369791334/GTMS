<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnverionsoneController extends Controller
{
    public function index(){
        return view('pages.enviro_b1.index');
    }

    public function index1(){
        return view('pages.enviro_b1.subcat1');
    }
    public function index2(){
        return view('pages.enviro_b1.subcat2');
    }
}

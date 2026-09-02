<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('pages.lease_application.customer');
    }

    public function step1()
    {
        return view('pages.lease_application.createstep1');
    }

    public function step2()
    {
        return view('pages.lease_application.createstep2');
    }

    public function step3()
    {
        return view('pages.lease_application.createstep3');
    }

    public function step4()
    {
        return view('pages.lease_application.createstep4');
    }

    public function step5()
    {
        return view('pages.lease_application.createstep5');
    }

    public function step6()
    {
        return view('pages.lease_application.createstep6');
    }

    public function step7()
    {
        return view('pages.lease_application.createstep7');
    }

    public function viewApplication()
    {
        return view('pages.lease_application.viewapplication');
    }
}

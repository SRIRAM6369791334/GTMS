<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\DroneSurvey;
use Illuminate\Http\Request;

class DroneSurveyController extends Controller
{
    public function index()
    {
        return view('pages.drone_survey.index');
    }

    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('drone_wizard', []);
        $customers = Customer::withTrashed()->orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();

        return view('pages.drone_survey.wizard', compact('step', 'draft', 'customers', 'districts'));
    }
}

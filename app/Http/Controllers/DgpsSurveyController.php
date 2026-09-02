<?php

namespace App\Http\Controllers;

class DgpsSurveyController extends Controller
{
    public function index() { return view('pages.dgps_survey.index'); }

    public function wizard(int $step)
    {
        abort_unless($step >= 1 && $step <= 6, 404);
        return view('pages.dgps_survey.wizard', compact('step'));
    }
}

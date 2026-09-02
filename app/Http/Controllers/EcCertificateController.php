<?php

namespace App\Http\Controllers;

class EcCertificateController extends Controller
{
    public function index()
    {
        return view('pages.ec_certificate.index');
    }

    public function wizard(int $step)
    {
        abort_unless($step >= 1 && $step <= 6, 404);
        return view('pages.ec_certificate.wizard', compact('step'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('pages.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        // Check if user exists by email or user_code
        $user = User::where('email', $credentials['email'])
            ->orWhere('user_code', $credentials['email'])
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email or User ID.',
            ])->onlyInput('email');
        }

        if ($user->status != 1) {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $user->email, 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'password' => 'Incorrect password entered.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminLoginController extends Controller
{
    public function show()
    {
        return view('auth.login-superadmin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // HARD LOCK: only superadmin allowed
            if ($user->role !== 'super_admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Unauthorized access.',
                ]);
            }

            $request->session()->regenerate();

            return redirect('/superadmin/dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }
}

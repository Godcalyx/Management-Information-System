<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;


class CustomLoginController extends Controller
{
    public function showStudentLogin() {
        return view('auth.login-student');
    }
    
    public function login(Request $request)
{
    $request->validate([
        'lrn' => ['required', 'digits:12'],
        'password' => ['required'],
    ]);

    $credentials = $request->only('lrn', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->role !== 'student') {
            Auth::logout();
            return back()->withErrors([
                'lrn' => 'Unauthorized: Not a student account.',
            ])->onlyInput('lrn');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'));
    }

    return back()->withErrors([
        'lrn' => 'Invalid credentials.',
    ])->onlyInput('lrn');
}


    
    
    // Similar functions for professor & admin using email instead of LRN
    public function showProfessorLogin() {
        return view('auth.login-professor');
    }
    public function loginProfessor(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->role !== 'professor') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Unauthorized: Not a professor account.',
                ])->onlyInput('email');
            }
    
            $request->session()->regenerate();
    
            return redirect()->intended(route('professor.dashboard'));
        }
    
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }
    
    
    public function showAdminLogin() {
        return view('auth.login-admin');
    }
    public function loginAdmin(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = \App\Models\User::where('email', $request->email)->first();
    
        if ($user && \Hash::check($request->password, $user->password)) {
            if ($user->role !== 'admin') {
                return back()->withErrors(['email' => 'Invalid credentials.']);
            }
    
            auth()->login($user);
            return redirect()->route('admin.dashboard');
        }
    
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }
    

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

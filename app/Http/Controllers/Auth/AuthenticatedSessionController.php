<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    dd(auth()->user()->id, auth()->user()->role, auth()->check());
}


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user(); // Capture user before logout
        $role = $user?->role; // Use role column directly

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on role after logout
        switch ($role) {
    case 'super_admin':
    case 'admin':
        return redirect('/login/admin'); 
    case 'professor':
        return redirect('/login/professor');
    case 'student':
        return redirect('/login/student');
    default:
        return redirect('/');
}


    }

    /**
     * Determine where to redirect users after login.
     */
    protected function redirectTo()
{
    switch (auth()->user()->role) {
        case 'superadmin':
            return route('super_admin.dashboard'); // ✅ FIXED
        case 'admin':
            return route('admin.dashboard');
        case 'professor':
            return route('professor.dashboard');
        case 'student':
            return route('student.dashboard');
        default:
            return '/';
    }
}



}

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

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
{
    $user = Auth::user(); // Capture user before logout
    $role = $user?->getRoleNames()->first(); // Spatie method

    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect based on role
    switch ($role) {
        case 'admin':
            return redirect('/login/admin');
        case 'professor':
            return redirect('/login/professor');
        case 'student':
            return redirect('/login/student');
        default:
            return redirect('/'); // Fallback
    }
}

    protected function redirectTo()
{
    switch (auth()->user()->role) {
        case 'admin':
            return '/admin/dashboard';
        case 'professor':
            return '/professor/dashboard';
        case 'student':
            return '/student/dashboard';
        default:
            return '/';
    }
}

}

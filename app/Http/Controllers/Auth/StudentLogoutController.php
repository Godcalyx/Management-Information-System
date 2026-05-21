<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentLogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Logout student guard
        Auth::guard('student')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to student login page
        return redirect('/login/student');
    }
}

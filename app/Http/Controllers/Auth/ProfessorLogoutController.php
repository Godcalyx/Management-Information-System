<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessorLogoutController extends Controller
{
    public function logout(Request $request)
    {
        // Logout professor guard
        Auth::guard('professor')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to professor login page
        return redirect('/login/professor');
    }
}

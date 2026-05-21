<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(Request $request)
    {
        // Detect portal from route or URL segment
        $portal = $this->detectPortal($request);

        return view('auth.passwords.email', compact('portal'));
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker($this->detectPortal($request))->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    private function detectPortal(Request $request)
    {
        if ($request->is('admin/*')) return 'admin';
        if ($request->is('professor/*')) return 'faculty';
        if ($request->is('student/*')) return 'student';

        return 'users'; // fallback
    }
}

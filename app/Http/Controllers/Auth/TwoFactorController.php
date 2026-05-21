<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function index()
    {
        return view('auth.2fa');
    }

    public function store(Request $request)
{
    $request->validate([
        'two_factor_code' => ['required', 'digits:6'],
    ]);

    $code = $request->two_factor_code;

    $userId = $request->session()->get('2fa:user:id');
    $user = User::find($userId);

    if (! $user) {
        return redirect()->route('login.student')
            ->withErrors('User not found.');
    }

    if ((string) $user->two_factor_code !== $code) {
        return back()->withErrors([
            'two_factor_code' => 'The provided 2FA code is invalid.',
        ]);
    }

    if (now()->gt($user->two_factor_expires_at)) {
        return back()->withErrors([
            'two_factor_code' => 'The 2FA code has expired.',
        ]);
    }

    // Clear 2FA code
    $user->forceFill([
        'two_factor_code' => null,
        'two_factor_expires_at' => null,
    ])->save();

    // Log in
    Auth::login($user);
    $request->session()->forget('2fa:user:id');

    return match ($user->role) {
        'student' => redirect()->route('student.dashboard'),
        'professor' => redirect()->route('professor.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        default => redirect('/'),
    };
}

public function resend()
{
    $user = auth()->user();

    $user->two_factor_code = rand(100000, 999999);
    $user->two_factor_expires_at = now()->addMinutes(10);
    $user->save();

    Mail::to($user->email)->send(
        new TwoFactorCodeMail($user) // ✅ pass model
    );

    return back()->with('status', 'Two-factor code resent.');
}

}

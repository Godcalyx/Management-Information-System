<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


class CustomLoginController extends Controller
{
    /**
     * Show student login page
     */
    public function showStudentLogin() {
        return view('auth.login-student');
    }

    /**
     * Handle student login with rate limiting
     */
    public function login(Request $request)
    {
        $request->validate([
            'lrn' => ['required', 'digits:12'],
            'password' => ['required'],
        ]);

        $key = $this->throttleKey('student', $request->lrn);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'lrn' => "Too many login attempts. Try again in $seconds seconds.",
            ]);
        }

        $credentials = $request->only('lrn', 'password');

        if (Auth::attempt($credentials)) {
    $user = Auth::user();

    if ($user->role !== 'student') {
        Auth::logout();
        RateLimiter::hit($key);
        return back()->withErrors(['lrn' => 'Unauthorized: Not a student account.'])->onlyInput('lrn');
    }

    RateLimiter::clear($key);

    // Generate 2FA code
    $this->sendTwoFactorCode($user);

    // Store intended route and logout user temporarily
    Auth::logout();
    $request->session()->put('2fa:user:id', $user->id);
    return redirect()->route('2fa.index');
}


        RateLimiter::hit($key);
        return back()->withErrors(['lrn' => 'Invalid credentials.'])->onlyInput('lrn');
    }

    /**
     * Show professor login page
     */
    public function showProfessorLogin() {
        return view('auth.login-professor');
    }

    /**
     * Handle professor login with rate limiting
     */
    public function loginProfessor(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = $this->throttleKey('professor', $request->email);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in $seconds seconds.",
            ]);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
    $user = Auth::user();

    if ($user->role !== 'professor') {
        Auth::logout();
        RateLimiter::hit($key);
        return back()->withErrors(['email' => 'Unauthorized: Not a professor account.'])->onlyInput('email');
    }

    RateLimiter::clear($key);

    // Generate 2FA code
    $this->sendTwoFactorCode($user);

    // Store intended route and logout user temporarily
    Auth::logout();
    $request->session()->put('2fa:user:id', $user->id);
    return redirect()->route('2fa.index');
}


        RateLimiter::hit($key);
        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /**
     * Show admin login page
     */
    public function showAdminLogin() {
        return view('auth.login-admin');
    }

    /**
     * Handle admin login with rate limiting
     */
    public function loginAdmin(Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $key = $this->throttleKey('admin', $request->email);

    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Try again in $seconds seconds.",
        ]);
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Ensure only admins can log in here
        if ($user->role === 'admin' && $user->status === 'inactive') {
            Auth::logout();
            RateLimiter::hit($key);
            return back()->withErrors(['email' => 'This admin account is inactive. Please contact the superadmin.']);
        }

        if (!in_array($user->role, ['admin', 'superadmin'])) {
            Auth::logout();
            RateLimiter::hit($key);
            return back()->withErrors(['email' => 'Unauthorized: Not an admin account.']);
        }

        RateLimiter::clear($key);

        // Generate 2FA code
        $this->sendTwoFactorCode($user);

        // Logout temporarily and store user ID for 2FA verification
        Auth::logout();
        $request->session()->put('2fa:user:id', $user->id);

        // Redirect to 2FA verification page
        return redirect()->route('2fa.index');
    }

    RateLimiter::hit($key);
    return back()->withErrors(['email' => 'Invalid credentials.']);
}




    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Generate a unique rate limiter key per user type and identifier
     */
    protected function throttleKey($type, $identifier)
    {
        return Str::lower($type.'|'.$identifier.'|'.request()->ip());
    }

    protected function sendTwoFactorCode($user)
{
    $user->two_factor_code = rand(100000, 999999);
    $user->two_factor_expires_at = Carbon::now()->addMinutes(10); // code valid 10 mins
    $user->save();

    // Send code via email (example)
    Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($user));
}
}

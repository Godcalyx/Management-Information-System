<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class StudentForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('student.auth.passwords.email', [
            'unreadAnnouncementCount' => 0,
        ]);
    }


    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $student = User::where('email', $request->email)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return back()->withErrors(['email' => 'We could not find a student account with that email address.']);
        }

        $status = Password::broker('users')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('student.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                if ($user->role !== 'student') {
                    throw ValidationException::withMessages([
                        'email' => 'This reset link does not belong to a student account.',
                    ]);
                }

                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login.student')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\PasswordPolicy;


class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('admin.change-password');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'confirmed', PasswordPolicy::rule()],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()
                ->route('password.request')
                ->with('info', 'Current password is incorrect. You can reset your password here.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password successfully changed!');
    }
}

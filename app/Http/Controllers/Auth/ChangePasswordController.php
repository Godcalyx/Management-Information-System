<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false, // Flag to skip forced change next time
        ]);

        // Redirect based on role if needed
        if ($user->hasRole('professor')) {
            return redirect()->route('professor.dashboard')->with('success', 'Password changed successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }
}

<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;



class ChangePasswordController extends Controller
{
    public function showChangeForm()
    {
        return view('professor.change-password');
    }

    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => ['required', 'confirmed', 'min:8'],
    ]);

    $user = auth()->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user->update([
        'password' => Hash::make($request->new_password),
    ]);

    return back()->with('success', 'Password updated successfully');
}

}

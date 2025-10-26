<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ChangePasswordController extends Controller
{
    public function showChangeForm()
    {
        return view('admin.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('status', 'Password successfully updated.');
    }
}

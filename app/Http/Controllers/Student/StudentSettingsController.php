<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class StudentSettingsController extends Controller
{
    public function index()
    {
        return view('student.settings');
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $filename = time() . '_' . $request->file('profile_picture')->getClientOriginalName();
            $path = $request->file('profile_picture')->storeAs('profile_pictures', $filename, 'public');

            $user->profile_picture = $path;
            $user->save();
        }

        return redirect()->route('student.settings')->with('success', 'Profile picture updated successfully!');
    }


    public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    if (!Hash::check($request->current_password, auth()->user()->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user = auth()->user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password changed successfully!');
}

public function setTheme(Request $request)
{
    $theme = $request->input('theme');
    session(['theme' => $theme]);
    return back();
}

public function saveTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:theme-yellow, theme-blue, theme-green,theme-dark', // add your supported themes here
        ]);

        $user = Auth::user();
        $user->theme = $request->theme;
        $user->save();

        return back()->with('success', 'Theme updated successfully.');
    }



}

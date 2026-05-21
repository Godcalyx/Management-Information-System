<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Announcement;
use App\Helpers\PasswordPolicy;

class ChangePasswordController extends Controller
{
    // Show the change password form
    public function showChangeForm()
    {
        $user = Auth::user();

        // Count unread announcements for the professor
        $unreadAnnouncementCount = Announcement::where(function ($query) use ($user) {
                $query->whereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades'); // Professors usually see 'All'
            })
            ->whereDoesntHave('users', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        return view('professor.change-password', compact('unreadAnnouncementCount'));
    }

    // Handle password update
    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => ['required', 'confirmed', PasswordPolicy::rule()],
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user->update([
        'password' => Hash::make($request->new_password),
    ]);

    // Count unread announcements again after update
    $unreadAnnouncementCount = Announcement::where(function ($query) use ($user) {
            $query->whereJsonContains('target_grades', 'All')
                  ->orWhereNull('target_grades');
        })
        ->whereDoesntHave('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('announcement_user.is_read', true);
        })
        ->count();

    return back()->with([
        'success' => 'Password updated successfully',
        'unreadAnnouncementCount' => $unreadAnnouncementCount
    ]);
}

}

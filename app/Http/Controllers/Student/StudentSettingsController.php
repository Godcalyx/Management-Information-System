<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Announcement;
use App\Helpers\PasswordPolicy;


class StudentSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $gradeNumber = $user->grade_level;
        $gradeText = "Grade {$gradeNumber}";

        // Count unread announcements for this student
        $unreadAnnouncementCount = Announcement::where(function($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeNumber)
                      ->orWhereJsonContains('target_grades', $gradeText)
                      ->orWhereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        return view('student.settings', compact('unreadAnnouncementCount'));
    }

    public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'new_password' => ['required', 'confirmed', PasswordPolicy::rule()],
    ]);

    if (!Hash::check($request->current_password, auth()->user()->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    $user = auth()->user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return back()->with('success', 'Password changed successfully!');
}


    // Profile picture update method can stay commented or included as needed
}

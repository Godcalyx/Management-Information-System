<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class StudentAnnouncementController extends Controller
{
    // Show all announcements
    public function index(Request $request)
{
    $student = Auth::user();
    $sort = $request->input('sort', 'desc'); // default: desc

    $gradeNumber = $student->grade_level;
    $gradeText = "Grade {$gradeNumber}";

    $announcements = Announcement::where(function ($query) use ($gradeNumber, $gradeText) {
        $query->whereJsonContains('target_grades', $gradeText)
              ->orWhereJsonContains('target_grades', $gradeNumber)
              ->orWhereJsonContains('target_grades', 'All')
              ->orWhereNull('target_grades');
    })
    ->with('user')
    ->orderBy('created_at', $sort) // ✅ now uses asc or desc from the request
    ->get();

    $announcements->each(function ($announcement) {
    if ($announcement->attachment) {
        $announcement->attachment_url = asset('storage/' . $announcement->attachment);
    } else {
        $announcement->attachment_url = null;
    }
});


    // Count unread
    $unreadAnnouncementCount = Announcement::where(function ($query) use ($gradeNumber, $gradeText) {
            $query->whereJsonContains('target_grades', $gradeText)
                  ->orWhereJsonContains('target_grades', $gradeNumber)
                  ->orWhereJsonContains('target_grades', 'All')
                  ->orWhereNull('target_grades');
        })
        ->whereDoesntHave('users', function ($q) use ($student) {
            $q->where('user_id', $student->id)
              ->where('announcement_user.is_read', true);
        })
        ->count();

    return view('student.announcements', compact('announcements', 'sort', 'unreadAnnouncementCount'));
}


    // Show a single announcement
    public function show(Announcement $announcement)
    {
        $student = Auth::user();
$gradeNumber = $student->grade_level ?? 0; // default if somehow 0
$gradeText = "Grade {$gradeNumber}";


        $targets = json_decode($announcement->target_grades);

        if (
            !$targets ||
            in_array($gradeText, $targets) ||
            in_array($gradeNumber, $targets) ||
            in_array('All', $targets)
        ) {
            $announcement->users()->syncWithoutDetaching([
                $student->id => ['is_read' => true]
            ]);
        }

        // Count unread for sidebar
        $unreadAnnouncementCount = Announcement::where(function ($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeText)
                      ->orWhereJsonContains('target_grades', $gradeNumber)
                      ->orWhereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function ($q) use ($student) {
                $q->where('user_id', $student->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        return view('student.announcement-detail', compact('announcement', 'unreadAnnouncementCount'));
    }
    public function markAsRead(Announcement $announcement)
{
    $student = Auth::user();
    $announcement->users()->syncWithoutDetaching([
        $student->id => ['is_read' => true]
    ]);
    return response()->json(['status' => 'ok']);
}

}

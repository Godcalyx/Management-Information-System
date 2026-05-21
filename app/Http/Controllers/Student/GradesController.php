<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Subject;

class GradesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // ✅ Get latest enrollment for this student
        $latestEnrollment = Enrollment::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$latestEnrollment) {
            return view('student.grades', [
                'subjects' => [],
                'unreadAnnouncementCount' => 0,
                'schoolYear' => now()->year . ' - ' . (now()->year + 1)
            ]);
        }

        $gradeLevelId = $latestEnrollment->grade_level_id;
        $schoolYear = $latestEnrollment->school_year;

        // ✅ Get all subjects tied to this student's grade level
        $subjectList = Subject::where('grade_level_id', $gradeLevelId)
            ->orderBy('name')
            ->get();

        // Initialize subjects with empty quarters
        $subjects = [];
        foreach ($subjectList as $subject) {
            $subjects[$subject->name] = [
                'q1' => '—',
                'q2' => '—',
                'q3' => '—',
                'q4' => '—'
            ];
        }

        // ✅ Fetch actual grades for this student and current school year
        $grades = Grade::where('user_id', $userId)
            ->where('school_year', $schoolYear)
            ->whereIn('subject_id', $subjectList->pluck('id'))
            ->get();

        // Fill in actual grades
        foreach ($grades as $entry) {
            $quarterKey = 'q' . $entry->quarter;
            $subjectName = $entry->subject->name ?? null;

            if ($subjectName && isset($subjects[$subjectName])) {
                $subjects[$subjectName][$quarterKey] = $entry->grade;
            }
        }

        // ✅ Count unread announcements for this student
        $gradeNumber = $latestEnrollment->grade_level->name ?? null;
        $gradeText = "Grade {$gradeNumber}";

        $unreadAnnouncementCount = Announcement::where(function ($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeNumber)
                      ->orWhereJsonContains('target_grades', $gradeText)
                      ->orWhereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        return view('student.grades', compact('subjects', 'unreadAnnouncementCount', 'schoolYear'));
    }
}       

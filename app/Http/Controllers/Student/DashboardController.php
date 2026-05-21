<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Get the latest enrollment for this student
        $enrollment = Enrollment::where('user_id', $student->id)
            ->orderBy('id', 'desc')
            ->first();

        $gradeLevel = $enrollment ? $enrollment->gradeLevel : null;

        // If no enrollment found, return empty data
        if (!$gradeLevel) {
            return view('student.dashboard', [
                'announcements' => [],
                'subjectBreakdown' => [],
                'quarters' => [],
                'quarterlyAverages' => [],
                'honor' => null,
                'percentile' => null,
            ]);
        }

        $gradeNumber = $gradeLevel->name; // or $gradeLevel->level_number
        $gradeText = $gradeLevel->name;

        // Latest 5 announcements for this grade level
        $announcements = Announcement::where(function($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeNumber)
                      ->orWhereJsonContains('target_grades', $gradeText)
                      ->orWhereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades');
            })
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // Unread announcement count
        $unreadAnnouncementCount = Announcement::where(function($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeNumber)
                      ->orWhereJsonContains('target_grades', $gradeText)
                      ->orWhereJsonContains('target_grades', 'All')
                      ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function($q) use ($student) {
                $q->where('user_id', $student->id)
                  ->where('announcement_user.is_read', true);
            })
            ->count();

        // Fetch all subjects for this grade level
        $subjects = Subject::where('grade_level_id', $gradeLevel->id)->get();

        // Fetch all grades for this student
        $grades = Grade::where('user_id', $student->id)->get();

        $quarters = [1,2,3,4]; // actual quarter numbers in DB

$subjectBreakdown = [];
foreach ($subjects as $subject) {
    $subjectGrades = $grades->where('subject_id', $subject->id);

    $row = [];
    $row['name'] = $subject->name;
    $total = 0;
    $count = 0;

    // Assign grades for Q1-Q4
    foreach ($quarters as $q) {
        $grade = $subjectGrades->where('quarter', $q)->first();
        $row['q'.$q] = $grade ? $grade->grade : null; // q1, q2, q3, q4 for Blade

        if ($grade) {
            $total += $grade->grade;
            $count++;
        }
    }

    // Final grade
    // Final grade: calculate average of Q1-Q4
$finalGrades = [];
foreach ($quarters as $q) {
    $g = $subjectGrades->where('quarter', $q)->first();
    if ($g) $finalGrades[] = $g->grade;
}

$row['final'] = count($finalGrades) ? round(array_sum($finalGrades)/count($finalGrades), 2) : null;


    // Average (include final if exists)
    $avgTotal = $total;
    $avgCount = $count;
    if ($row['final'] !== null) {
        $avgTotal += $row['final'];
        $avgCount++;
    }
    $row['average'] = $avgCount ? round($avgTotal / $avgCount, 2) : null;

    // Trend: Q1 vs Q4
    $first = $subjectGrades->where('quarter', 1)->first();
    $last = $subjectGrades->where('quarter', 4)->first();
    if ($first && $last) {
        $row['trend'] = $last->grade > $first->grade ? '↑' : ($last->grade < $first->grade ? '↓' : '→');
    } else {
        $row['trend'] = '→';
    }

    $row['is_excellent'] = $row['average'] >= 85;

    $subjectBreakdown[] = $row;
}

        // Compute percentile and honor
        $finalAverages = $grades->where('quarter', 'final')->pluck('grade');
        $finalAverage = $finalAverages->count() ? $finalAverages->avg() : null;

        $allFinalAverages = Grade::where('quarter', 'final')
            ->selectRaw('user_id, AVG(grade) as avg_final')
            ->groupBy('user_id')
            ->pluck('avg_final')
            ->sortDesc()
            ->values();

        $percentile = $honor = null;
        if ($finalAverage !== null && $allFinalAverages->count()) {
            $position = $allFinalAverages->search($finalAverage) + 1;
            $percentile = ($position / $allFinalAverages->count()) * 100;

            if ($percentile <= 10) {
                $honor = "With Highest Honors";
            } elseif ($percentile <= 20) {
                $honor = "With High Honors";
            } elseif ($percentile <= 50) {
                $honor = "With Honors";
            }
        }

        return view('student.dashboard', [
            'announcements' => $announcements,
            'unreadAnnouncementCount' => $unreadAnnouncementCount,
            'subjectBreakdown' => $subjectBreakdown,
            'quarters' => $quarters,
            'quarterlyAverages' => $grades->groupBy('quarter')->map(fn($g) => round($g->avg('grade'),2))->values()->toArray(),
            'honor' => $honor,
            'percentile' => $percentile,
        ]);
    }
}

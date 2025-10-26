<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Grade;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradeConsolidatedController extends Controller
{
    public function index(Request $request)
{
    $gradeLevels = Subject::distinct()->pluck('grade_level');
    $selectedGrade = $request->input('grade_level');
    $quarter = $request->input('quarter');

    $subjects = collect();
    $students = collect();

    if ($selectedGrade && $quarter) {
        $subjects = Subject::where('grade_level', $selectedGrade)->get();

        // Fetch students from enrollments
        $students = Enrollment::with('user') // Load related User (student)
            ->where('status', 'approved') // ✅ Only approved students
            ->where('grade_level', $selectedGrade)
            ->orderBy('last_name')
            ->get();

        foreach ($students as $student) {
            $userId = $student->user_id;

            $grades = Grade::where('user_id', $userId)
                ->where('quarter', $quarter)
                ->where('school_year', now()->year)
                ->get();

            $student->grades = $grades;
            $total = 0;
            $count = 0;

            foreach ($subjects as $subject) {
                $grade = $grades->firstWhere('subject_id', $subject->id);
                if ($grade && $grade->grade !== null) {
                    $total += $grade->grade;
                    $count++;
                }
            }

            $average = $count > 0 ? round($total / $count, 2) : null;
            $student->average = $average;

            if ($average !== null) {
                if ($average >= 98) {
                    $student->remarks = '🥇 With Highest Honors';
                } elseif ($average >= 95) {
                    $student->remarks = '🥈 With High Honors';
                } elseif ($average >= 90) {
                    $student->remarks = '🏅 With Honors';
                } elseif ($average >= 75) {
                    $student->remarks = 'PASSED';
                } else {
                    $student->remarks = 'FAILED';
                }
            } else {
                $student->remarks = 'No Grades';
            }
        }
    }

    return view('professor.grades.consolidated', compact('gradeLevels', 'subjects', 'students', 'selectedGrade', 'quarter'));
}


}

<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use Illuminate\Http\Request;

class GradeConsolidatedController extends Controller
{
    public function index(Request $request)
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $selectedGradeId = $request->input('grade_level_id');
        $quarter = $request->input('quarter');

        $subjects = collect();
        $students = collect();

        if ($selectedGradeId && $quarter) {
            $subjects = Subject::where('grade_level_id', $selectedGradeId)->get();

            // Get latest enrollment per student
            $latestEnrollmentIds = Enrollment::selectRaw('MAX(id) as id')
                ->groupBy('user_id')
                ->pluck('id');

            // Get students whose latest enrollment matches the selected grade
            $students = Enrollment::with('user', 'gradeLevel')
                ->whereIn('id', $latestEnrollmentIds)
                ->where('grade_level_id', $selectedGradeId)
                ->where('status', 'approved')
                ->get()
                ->sortBy(fn($s) => $s->user->last_name);

            foreach ($students as $student) {
                // Use the enrollment's school_year
                $grades = Grade::where('user_id', $student->user_id)
                    ->where('quarter', $quarter)
                    ->where('school_year', $student->school_year) // enrollment's school year
                    ->get();

                $student->grades = $grades;

                // Calculate average
                $total = 0;
                $count = 0;
                foreach ($subjects as $subject) {
                    $grade = $grades->firstWhere('subject_id', $subject->id);
                    if ($grade && $grade->grade !== null) {
                        $total += $grade->grade;
                        $count++;
                    }
                }

                $student->average = $count > 0 ? round($total / $count, 2) : null;

                // Assign remarks
                $avg = $student->average;
                if ($avg !== null) {
                    $student->remarks =
                        $avg >= 98 ? '🥇 With Highest Honors' :
                        ($avg >= 95 ? '🥈 With High Honors' :
                        ($avg >= 90 ? '🏅 With Honors' :
                        ($avg >= 75 ? 'PASSED' : 'FAILED')));
                } else {
                    $student->remarks = 'No Grades';
                }
            }
        }

        return view('professor.grades.consolidated', compact(
            'gradeLevels', 'subjects', 'students', 'selectedGradeId', 'quarter'
        ));
    }
}

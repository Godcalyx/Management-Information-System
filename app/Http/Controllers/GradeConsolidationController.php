<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Grade;
use App\Models\User;
use App\Models\GradeLevelProfessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Enrollment;

class GradeConsolidationController extends Controller
{
    public function index(Request $request)
{
    $professorId = Auth::id();

    // Get distinct grade levels assigned to this professor
    $gradeLevels = DB::table('professor_subject_grade_levels')
        ->where('user_id', $professorId)
        ->distinct()
        ->pluck('grade_level');

    $selectedGrade = $request->input('grade_level');
    $quarter = $request->input('quarter');

    $subjects = collect();
    $students = collect();

    if ($selectedGrade && $quarter) {
        // Fetch only assigned subjects for this professor and grade level
        $subjectIds = DB::table('professor_subject_grade_levels')
            ->where('user_id', $professorId)
            ->where('grade_level', $selectedGrade)
            ->pluck('subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $students = Enrollment::where('grade_level', $selectedGrade)
            ->where('status', 'approved') // ✅ Only approved students
            ->orderBy('last_name')
            ->get();

        foreach ($students as $student) {
            $grades = Grade::where('user_id', $student->id)
                ->where('quarter', $quarter)
                ->where('school_year', now()->year)
                ->get();

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

            // Compute remarks
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

    return view('professor.grades.consolidation', compact('gradeLevels', 'subjects', 'students'));
}


    public function assignSubjectsToProfessor(Request $request)
    {
        $professorId = $request->input('user_id');
        $gradeLevel = $request->input('grade_level');

        $subjectIds = Subject::where('grade_level', $gradeLevel)->pluck('id');

        $insertData = $subjectIds->map(function ($subjectId) use ($professorId, $gradeLevel) {
            return [
                'user_id' => $professorId,
                'grade_level' => $gradeLevel,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('grade_level_professor')->insertOrIgnore($insertData);

        return back()->with('success', 'All subjects for Grade ' . $gradeLevel . ' assigned to professor.');
    }
}

<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Grade;
use App\Models\ProfessorSubjectGradeLevel;

class DashboardController extends Controller
{
    public function index()
    {
        $professorId = Auth::id();
        $schoolYear = now()->year;

        // ✅ Subjects handled by this professor
        $subjectIds = ProfessorSubjectGradeLevel::where('user_id', $professorId)
            ->pluck('subject_id');

        $totalSubjects = $subjectIds->count();

        // ✅ Total unique approved students taught by this professor
        $totalStudents = DB::table('enrollments')
            ->whereIn('grade_level', function($query) use ($professorId) {
                $query->select('grade_level')
                      ->from('professor_subject_grade_levels')
                      ->where('user_id', $professorId);
            })
            ->where('status', 'approved')
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        // ✅ Total grades submitted by this professor
        $submittedGradesCount = Grade::whereIn('subject_id', $subjectIds)
            ->where('school_year', $schoolYear)
            ->count();

        // ✅ Recent grades
        $recentGrades = Grade::with(['user', 'subject'])
            ->whereIn('subject_id', $subjectIds)
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function ($grade) {
                return (object)[
                    'student_name' => $grade->user->last_name . ', ' . $grade->user->first_name,
                    'subject_name' => $grade->subject->name,
                    'grade' => $grade->grade,
                    'quarter' => $grade->quarter
                ];
            });

        // ✅ Subjects handled by this professor
        $subjectsHandled = ProfessorSubjectGradeLevel::with('subject')
            ->where('user_id', $professorId)
            ->get()
            ->map(function ($record) {
                return (object)[
                    'subject_name' => $record->subject->name ?? 'Unknown Subject',
                    'grade_level'  => $record->grade_level ?? 'N/A',
                ];
            });

        // ✅ Students grouped per grade level (approved only)
        $studentsPerGrade = DB::table('enrollments')
            ->join('users', 'enrollments.user_id', '=', 'users.id')
            ->whereIn('enrollments.grade_level', function($query) use ($professorId) {
                $query->select('grade_level')
                      ->from('professor_subject_grade_levels')
                      ->where('user_id', $professorId);
            })
            ->where('enrollments.status', 'approved')
            ->whereNotNull('enrollments.user_id')
            ->select('users.name', 'enrollments.grade_level')
            ->distinct()
            ->orderBy('enrollments.grade_level')
            ->get()
            ->groupBy('grade_level');

        // ✅ Honor students across ALL subjects of their grade level
$honorStudents = DB::table('enrollments')
    ->where('enrollments.status', 'approved')
    ->whereNotNull('enrollments.user_id')
    ->join('users', 'enrollments.user_id', '=', 'users.id')
    ->join('subjects', 'subjects.grade_level', '=', 'enrollments.grade_level')
    ->leftJoin('grades', function($join) use ($schoolYear) {
        $join->on('grades.user_id', '=', 'enrollments.user_id')
             ->on('grades.subject_id', '=', 'subjects.id')
             ->where('grades.school_year', '=', $schoolYear);
    })
    ->select(
        'enrollments.user_id as id',
        'users.name',
        'enrollments.grade_level',
        DB::raw('AVG(grades.grade) as average')
    )
    ->groupBy('enrollments.user_id', 'users.name', 'enrollments.grade_level')
    ->havingRaw('AVG(grades.grade) >= 90')
    ->orderByDesc('average')
    ->get()
    ->map(function ($g) {
        $average = round($g->average, 2);

        $honor = match (true) {
            $average >= 98 => 'With Highest Honors',
            $average >= 95 => 'With High Honors',
            $average >= 90 => 'With Honors',
            default => null,
        };

        return (object)[
            'id' => $g->id,
            'name' => $g->name,
            'average' => $average,
            'grade_level' => $g->grade_level ?? 'N/A',
            'honor' => $honor,
        ];
    });


        return view('professor.dashboard', compact(
            'totalStudents',
            'totalSubjects',
            'submittedGradesCount',
            'recentGrades',
            'honorStudents',
            'subjectsHandled',
            'studentsPerGrade'
        ));
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StandingController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $student = Auth::user();
        $gradeLevel = $student->grade_level;
        $schoolYear = '2024-2025'; // You can make this dynamic later

        // ------------------------
        // 1. Fetch average grades of all students in the same grade level and school year
        // ------------------------
        $averages = DB::table('grades')
            ->join('users', 'grades.user_id', '=', 'users.id')
            ->where('users.role', 'student')
            ->where('users.grade_level', $gradeLevel)
            // ->where('grades.school_year', $schoolYear)
            ->select('grades.user_id', DB::raw('AVG(grades.grade) as avg_grade'))
            ->groupBy('grades.user_id')
            ->orderByDesc('avg_grade')
            ->get();

        // ------------------------
        // 2. Calculate the logged-in student's average and rank
        // ------------------------
        $studentAverage = null;
        $studentRank = null;
        foreach ($averages as $index => $row) {
            if ($row->user_id == $userId) {
                $studentAverage = $row->avg_grade;
                $studentRank = $index + 1;
                break;
            }
        }

        // ------------------------
        // 3. Determine honor level
        // ------------------------
        $honor = null;
        if ($studentAverage !== null) {
            if ($studentAverage >= 95) {
                $honor = 'With Highest Honors';
            } elseif ($studentAverage >= 92) {
                $honor = 'With High Honors';
            } elseif ($studentAverage >= 90) {
                $honor = 'With Honors';
            }
        }

        // ------------------------
        // 4. Compute percentile rank
        // ------------------------
        $percentile = null;
        if ($studentAverage !== null && $averages->count() > 0) {
            $totalStudents = $averages->count();
            $studentsBelow = $averages->filter(fn($a) => $a->avg_grade < $studentAverage)->count();
            $percentile = round(($studentsBelow / $totalStudents) * 100);
        }

        // ------------------------
        // 5. Fetch pivoted grades per subject
        // ------------------------
        $grades = DB::table('grades')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->where('grades.user_id', $userId)
            // ->where('grades.school_year', $schoolYear)
            ->select(
                'subjects.name as subject_name',
                DB::raw("MAX(CASE WHEN grades.quarter='1' THEN grades.grade END) as q1"),
                DB::raw("MAX(CASE WHEN grades.quarter='2' THEN grades.grade END) as q2"),
                DB::raw("MAX(CASE WHEN grades.quarter='3' THEN grades.grade END) as q3"),
                DB::raw("MAX(CASE WHEN grades.quarter='4' THEN grades.grade END) as q4"),
                DB::raw("MAX(CASE WHEN grades.quarter='final' THEN grades.grade END) as final")
            )
            ->groupBy('grades.subject_id', 'subjects.name')
            ->get();

        return view('student.academic-standing', compact(
            'studentAverage',
            'studentRank',
            'honor',
            'percentile',
            'grades'
        ));
    }
}

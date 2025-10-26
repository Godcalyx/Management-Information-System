<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Grade;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GradeSummaryController extends Controller
{
    public function index(Request $request)
    {   
        $gradeLevel = $request->input('grade_level');

        // For the filter dropdown (show only grade levels that have approved students)
        $gradeLevels = DB::table('enrollments')
            ->where('status', 'approved')
            ->pluck('grade_level')
            ->unique()
            ->sort()
            ->values();

        $subjects = collect();
        $enrollments = collect();
        $gradesData = [];

        if ($gradeLevel) {
            // ✅ Fetch ONLY approved students for the selected grade level
            $enrollments = DB::table('enrollments')
                ->where('grade_level', $gradeLevel)
                ->where('status', 'approved')
                ->get();

            // Fetch subjects for that grade level
            $subjects = DB::table('subjects')
                ->where('grade_level', $gradeLevel)
                ->get();

            // Get corresponding users for matching grades
            $lrns = $enrollments->pluck('lrn')->filter();

            // Map LRN to user_id (from users table)
            $userMap = DB::table('users')
                ->whereIn('lrn', $lrns)
                ->pluck('id', 'lrn'); // [lrn => id]

            // Fetch grades for those users
            $grades = DB::table('grades')
                ->whereIn('user_id', $userMap->values())
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->get();

            // Structure grades: $gradesData[user_id][subject_id][quarter] = grade
            foreach ($grades as $grade) {
                $gradesData[$grade->user_id][$grade->subject_id][$grade->quarter] = $grade->grade;
            }

            // Attach user_id and compute age for each student
            $enrollments->transform(function ($student) use ($userMap) {
                $student->user_id = $userMap[$student->lrn] ?? null;

                if (!empty($student->birthdate)) {
                    $student->age = Carbon::parse($student->birthdate)->age;
                } else {
                    $student->age = null;
                }

                return $student;
            });
        }

        return view('Admin.grades.summary', [
            'gradeLevels' => $gradeLevels,
            'subjects' => $subjects,
            'enrollments' => $enrollments,
            'gradesData' => $gradesData,
        ]);
    }
}

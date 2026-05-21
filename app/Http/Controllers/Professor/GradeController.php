<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Student;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    public function consolidation(Request $request)
    {
        $gradeLevel = $request->input('grade_level');

        $advisoryStudents = collect();
        $students = collect();
        $subjects = collect();
        $existingGrades = collect();

        if ($gradeLevel) {
            $subjects = auth()->user()->subjects()
                ->where('grade_level', $gradeLevel)
                ->get();

            $advisoryStudents = Student::with('user')
                ->where('adviser_id', auth()->id())
                ->where('grade_level', $gradeLevel)
                ->get()
                ->map(fn($student) => $student->user)
                ->filter()
                ->values();

            $advisoryIds = $advisoryStudents->pluck('id')->toArray();

            $students = Enrollment::with('user')
                ->where('grade_level', $gradeLevel)
                ->whereNotIn('user_id', $advisoryIds)
                ->get()
                ->map(fn($enrollment) => $enrollment->user)
                ->filter()
                ->values();

            $allStudentIds = $students->pluck('id')->merge($advisoryStudents->pluck('id'));
            $existingGrades = Grade::whereIn('user_id', $allStudentIds)
                ->whereIn('subject_id', $subjects->pluck('id'))
                ->get()
                ->groupBy('user_id')
                ->map(function ($userGrades) {
                    return $userGrades->groupBy('subject_id')
                        ->map(function ($subjectGrades) {
                            $grades = [];
                            foreach ($subjectGrades as $grade) {
                                $grades[$grade->quarter] = $grade->grade;
                            }
                            return $grades;
                        });
                });
        }

        $gradeLevels = range(7, 12);

        return view('professor.grades.consolidation', compact(
            'gradeLevels',
            'gradeLevel',
            'subjects',
            'students',
            'advisoryStudents',
            'existingGrades'
        ));
    }

    public function summary()
    {
        return view('professor.grades.summary');
    }

   public function store(Request $request)
{
    $gradesInput = $request->input('grades', []);

    foreach ($gradesInput as $enrollmentId => $subjectGrades) {

        $enrollment = Enrollment::find($enrollmentId);

        if (!$enrollment) {
            \Log::warning("Enrollment {$enrollmentId} not found. Skipping.");
            continue;
        }

        foreach ($subjectGrades as $subjectId => $quarterGrades) {
            foreach ($quarterGrades as $quarter => $gradeValue) {

                if ($gradeValue === null || $gradeValue === '') {
                    continue;
                }

                Grade::updateOrCreate(
                    [
                        'user_id' => $enrollment->user_id,
                        'subject_id' => $subjectId,
                        'school_year' => $enrollment->school_year,
                        'quarter' => $quarter,
                    ],
                    [
                        'grade' => $gradeValue,
                        'status' => 'submitted',
                        'grade_level' => $enrollment->grade_level, // ALWAYS SAVE
                        'is_notified' => false,
                    ]
                );
            }
        }
    }

    return back()->with('success', 'Grades saved successfully.');
}


public function submitGrades(Request $request)
{
    $gradeLevel = $request->input('grade_level');
    $gradesInput = $request->input('grades', []);

    if (empty($gradesInput)) {
        return back()->with('error', 'No grades received from the form.');
    }

    DB::transaction(function () use ($gradesInput, $gradeLevel) {

        foreach ($gradesInput as $userId => $subjectGrades) {

            // Fetch latest enrollment for this student & grade level
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('grade_level', $gradeLevel)
                ->latest()
                ->first();

            if (!$enrollment) {
                \Log::warning("No enrollment found for user {$userId} at grade {$gradeLevel}");
                continue;
            }

            foreach ($subjectGrades as $subjectId => $quarterGrades) {
                foreach ($quarterGrades as $quarter => $gradeValue) {

                    if ($gradeValue === null || $gradeValue === '') continue;

                    Grade::updateOrCreate(
                        [
                            'user_id' => $userId,
                            'subject_id' => $subjectId,
                            'school_year' => $enrollment->school_year,
                            'quarter' => $quarter,
                        ],
                        [
                            'grade' => $gradeValue,
                            'status' => 'submitted',
                            'grade_level' => $gradeLevel, // ALWAYS SAVE
                            'is_notified' => false,
                        ]
                    );
                }
            }
        }
    });

    return back()->with('success', 'Grades submitted successfully.');
}


}

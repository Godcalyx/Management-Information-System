<?php

namespace App\Http\Controllers\Professor;

use Illuminate\Http\Request;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GradeController extends Controller
{
    public function consolidation()
    {
        return view('professor.grades.consolidation');
    }

    public function summary()
    {
        return view('professor.grades.summary');
    }

    public function store(Request $request)
{
    $quarter = $request->input('quarter');
    $schoolYear = $request->input('school_year') ?? now()->year;
    $gradesInput = $request->input('grades');

    foreach ($gradesInput as $enrollmentId => $subjectGrades) {
        $enrollment = \App\Models\Enrollment::find($enrollmentId);

        // Skip if the enrollment is missing or doesn't link to a user
        if (!$enrollment || !$enrollment->user_id) {
            continue;
        }

        $userId = $enrollment->user_id;

        foreach ($subjectGrades as $subjectId => $gradeValue) {
            if (!is_null($gradeValue) && $gradeValue !== '') {
                \App\Models\Grade::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'subject_id' => $subjectId,
                        'school_year' => $schoolYear,
                        'quarter' => $quarter,
                    ],
                    [
                        'grade' => $gradeValue,
                        'status' => 'draft',
                    ]
                );
            }
        }
    }

    return redirect()->back()->with('success', 'Grades have been successfully saved using enrollments.');
}

}

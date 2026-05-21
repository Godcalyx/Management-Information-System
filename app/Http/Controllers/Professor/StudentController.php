<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessorSubjectGradeLevel;


class StudentController extends Controller
{
    public function index(Request $request)
{
    $query = Enrollment::query();

    if (Auth::user()->role === 'professor') {
        // Get all grade levels handled by this professor
        $handledGrades = ProfessorSubjectGradeLevel::where('user_id', Auth::id())
            ->pluck('grade_level')
            ->unique();

        // Filter students by those grade levels
        $query->whereIn('grade_level', $handledGrades)
              ->where('status', 'approved');
    }

    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('first_name', 'like', "%{$request->search}%")
              ->orWhere('last_name', 'like', "%{$request->search}%")
              ->orWhere('lrn', 'like', "%{$request->search}%");
        });
    }

    $students = $query->select('*')->get();


    return view('professor.students.index', compact('students'));
}
public function updatePromotionStatus(Request $request, $user_id)
{
    $request->validate(['promotion_status' => 'required|string']);

    $student = Enrollment::where('user_id', $user_id)->first(); // make sure it's Enrollment

    if (!$student) {
        return back()->with('error', 'Student not found.');
    }

    $student->promotion_status = $request->promotion_status;
    $student->save();

    return back()->with('success', 'Promotion status updated successfully.');
}



public function show($id)
{
    $student = Enrollment::findOrFail($id);

    // Optional: verify professor is assigned to this student
    if (! $this->isAssignedToProfessor($student)) {
        abort(403, 'Unauthorized');
    }

    // Load grades, subjects, etc. for the review page
    $grades = $student->grades; // or however you store grades
    $subjects = $student->subjects; // optional

    return view('professor.students.review', compact('student', 'grades', 'subjects'));
}

private function isAssignedToProfessor($student)
{
    return ProfessorSubjectGradeLevel::where('user_id', auth()->id())
        ->where('grade_level', $student->grade_level)
        ->exists();
}
public function fetchDetails($user_id)
{
    // Fetch student enrollment
    $student = \DB::table('enrollments')->where('user_id', $user_id)->first();

    if (!$student) {
        return response()->json([
            'success' => false, 
            'message' => 'Student not found.'
        ]);
    }

    // Safely compute age
    $age = null;
    if (!empty($student->birthdate)) {
        try {
            $age = \Carbon\Carbon::parse($student->birthdate)->age;
        } catch (\Exception $e) {
            $age = null;
        }
    }

    // Fetch grades grouped by subject
    $gradesRaw = \DB::table('grades')
        ->where('user_id', $user_id)
        ->get(['subject_id', 'quarter', 'grade']);

    // Transform grades to have one row per subject with Q1-Q4
    $grades = [];
    foreach ($gradesRaw as $g) {
        $subjectId = $g->subject_id;
        if (!isset($grades[$subjectId])) {
            $grades[$subjectId] = [
                'subject_id' => $subjectId,
                'q1' => null,
                'q2' => null,
                'q3' => null,
                'q4' => null,
            ];
        }

        switch ($g->quarter) {
            case '1':
                $grades[$subjectId]['q1'] = $g->grade;
                break;
            case '2':
                $grades[$subjectId]['q2'] = $g->grade;
                break;
            case '3':
                $grades[$subjectId]['q3'] = $g->grade;
                break;
            case '4':
                $grades[$subjectId]['q4'] = $g->grade;
                break;
        }
    }

    // Optionally, fetch subject names if you have a subjects table
    foreach ($grades as &$g) {
        $subject = \DB::table('subjects')->where('id', $g['subject_id'])->first();
        $g['subject'] = $subject ? $subject->name : 'Unknown';
    }
    unset($g);

    // Full student name
    $fullName = trim("{$student->last_name}, {$student->first_name} {$student->middle_name} {$student->extension_name}");

    return response()->json([
        'success' => true,
        'student' => [
            'name' => $fullName,
            'lrn' => $student->lrn,
            'age' => $age,
            'sex' => $student->sex,
            'email' => $student->email,
            'grade_level' => $student->grade_level,
        ],
        'grades' => array_values($grades) // reset numeric keys for JS
    ]);
}


}

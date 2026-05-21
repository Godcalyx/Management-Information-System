<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\User;
use App\Models\Enrollment;
use Carbon\Carbon;

class GradeApprovalController extends Controller
{
    /**
     * Show students with submitted grades and their latest enrollment.
     */
    public function index(Request $request)
    {
        // Fetch all grade levels
        $gradeLevels = \App\Models\GradeLevel::orderBy('id')->get();

        // Selected grade level filter
        $selectedLevel = $request->grade_level;

        // Fetch enrollments that have submitted grades
        $students = Enrollment::with([
                'student', // make sure Enrollment has a student() relationship
                'grades' => function ($q) {
                    $q->where('status', 'submitted')
                      ->with('subject'); // eager load subjects
                },
                'gradeLevel'
            ])
            ->whereExists(function ($q) {
                $q->selectRaw(1)
                  ->from('grades')
                  ->whereColumn('grades.user_id', 'enrollments.user_id')
                  ->whereColumn('grades.school_year', 'enrollments.school_year')
                  ->whereColumn('grades.grade_level', 'enrollments.grade_level_id')
                  ->where('grades.status', 'submitted');
            })
            ->when($selectedLevel, function ($q) use ($selectedLevel) {
                $q->where('grade_level_id', $selectedLevel);
            })
            ->orderBy('grade_level_id', 'asc')
            ->orderBy('school_year', 'asc')
            ->get();

        return view('admin.grade_approvals.index', compact(
            'students',
            'gradeLevels',
            'selectedLevel'
        ));
    }

    /**
     * Approve all submitted grades for a specific student.
     */
    public function approveStudent(User $student)
    {
        Grade::where('user_id', $student->id)
            ->where('status', 'submitted')
            ->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Grades approved for ' . $student->name);
    }

    /**
     * Approve all submitted grades for all students.
     */
    public function approveAllPending()
    {
        $grades = Grade::where('status', 'submitted')->get();
        $totalApproved = $grades->count();

        if ($totalApproved > 0) {
            $grades->each->update(['status' => 'approved']);
            return back()->with('success', "All pending grades ($totalApproved) approved successfully.");
        }

        return back()->with('info', 'No submitted grades were found to approve.');
    }

   /**
 * Return selected submitted grades for a student with remarks.
 */
public function returnGrades(Request $request, $studentId)
{
    $request->validate([
        'remarks' => 'required|string|max:500',
        'grades' => 'required|array|min:1',
        'grades.*.subject' => 'required|string',
        'grades.*.quarter' => 'required|integer|min:1|max:4',
    ]);

    $student = User::findOrFail($studentId);

    foreach ($request->grades as $g) {
        $grade = Grade::where('user_id', $studentId)
            ->whereHas('subject', function ($q) use ($g) {
                $q->where('name', $g['subject']);
            })
            ->where('quarter', $g['quarter'])
            ->where('status', 'submitted')
            ->first();

        if ($grade) {
            $grade->update([
                'status' => 'returned',
                'remarks' => $request->remarks
            ]);
        }
    }

    return redirect()
        ->route('admin.grade-approvals.index')
        ->with('success', 'Selected grades successfully returned with remarks.');
}


    /**
     * Fetch submitted grades for a student (AJAX).
     */
    public function getStudentGrades($studentId)
    {
        $student = User::with(['grades' => function ($q) {
            $q->where('status', 'submitted')->with('subject');
        }])->findOrFail($studentId);

        $grades = $student->grades->map(function ($grade) {
            return [
                'subject' => $grade->subject->name,
                'quarter' => $grade->quarter,
                'grade' => $grade->grade,
            ];
        });

        return response()->json([
            'student_name' => $student->name,
            'grades' => $grades,
        ]);
    }
}

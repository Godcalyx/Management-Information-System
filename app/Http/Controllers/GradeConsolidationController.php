<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ProfessorSubject;
use App\Models\GradeLevel;
use Illuminate\Support\Facades\DB;

class GradeConsolidationController extends Controller
{
    /**
     * Display students and grades for consolidation
     */
    public function index(Request $request)
    {
        $professorId = auth()->id();

        // Get grade levels assigned to this professor
        $gradeLevels = ProfessorSubject::where('user_id', $professorId)
            ->join('subjects', 'professor_subjects.subject_id', '=', 'subjects.id')
            ->join('grade_levels', 'subjects.grade_level_id', '=', 'grade_levels.id')
            ->pluck('grade_levels.name', 'grade_levels.id')
            ->unique()
            ->sort()
            ->toArray();

        $gradeLevelId = $request->input('grade_level', array_key_first($gradeLevels));

        if (!$gradeLevelId) {
            return redirect()->back()->with('error', 'No grade levels assigned.');
        }

        // Subjects handled by professor for this grade level
        $subjectIds = ProfessorSubject::where('user_id', $professorId)
            ->join('subjects', 'professor_subjects.subject_id', '=', 'subjects.id')
            ->where('subjects.grade_level_id', $gradeLevelId)
            ->pluck('professor_subjects.subject_id');

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        // Latest approved enrollment per student for this grade level
        $latestEnrollmentIds = DB::table('enrollments')
            ->selectRaw('MAX(id) as id')
            ->where('status', 'approved')
            ->where('grade_level_id', $gradeLevelId)
            ->groupBy('user_id')
            ->pluck('id');

        $students = Enrollment::with('user')
            ->whereIn('id', $latestEnrollmentIds)
            ->orderBy('user_id', 'asc')
            ->get();

        // Separate advisory students and others
        $advisoryStudents = $students->filter(fn($enrollment) => $enrollment->user->adviser_id == $professorId)->values();
        $otherStudents = $students->whereNotIn('user_id', $advisoryStudents->pluck('user_id'))->values();

        // Existing grades for all students
        $existingGrades = [];
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                $grades = Grade::where('user_id', $student->user_id)
                    ->where('subject_id', $subject->id)
                    ->pluck('grade', 'quarter')
                    ->toArray();

                $existingGrades[$student->user_id][$subject->id] = $grades;
            }
        }
        $students = $advisoryStudents->merge($otherStudents);


        return view('professor.grades.consolidation', compact(
            'advisoryStudents', 'otherStudents', 'subjects', 'gradeLevelId', 'gradeLevels', 'existingGrades','students'
        ));
    }

    /**
     * Store submitted grades
     * Only sets status=submitted if grade is new or changed
     */
    public function store(Request $request)
{
    $request->validate([
        'grades.*.*.*' => 'nullable|integer|min:70|max:99',
        'grade_level_id' => 'required|integer|exists:grade_levels,id'
    ]);

    $gradesInput = $request->input('grades', []);
    $gradeLevelId = $request->grade_level_id;
    
    foreach ($gradesInput as $studentId => $subjects) {
        $enrollment = Enrollment::where('user_id', $studentId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('status', 'approved')
            ->latest('id')
            ->first();

        if (!$enrollment) {
            continue; // Skip this student if no valid enrollment
        }

        $currentYear = $enrollment->school_year;

        foreach ($subjects as $subjectId => $quarters) {
            foreach ($quarters as $quarter => $gradeValue) {
                if ($gradeValue === null || $gradeValue === '') continue;

                // Check existing grade
                $existing = Grade::where('user_id', $studentId)
                    ->where('subject_id', $subjectId)
                    ->where('quarter', $quarter)
                    ->where('school_year', $currentYear)
                    ->first();

                    if (!$existing) {
                        // Create new
                        Grade::create([
                            'user_id'     => $studentId,
                            'subject_id'  => $subjectId,
                            'quarter'     => $quarter,
                            'school_year' => $currentYear,
                            'grade_level' => $gradeLevelId,
                            'grade'       => $gradeValue,
                            'status'      => 'submitted',
                        ]);
                        continue;
                    }

                    // Update only if changed
                    if ($existing->grade != $gradeValue) {
                        $existing->update([
                            'grade'       => $gradeValue,
                            'grade_level' => $gradeLevelId,
                            'status'      => 'submitted',
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'Grades submitted successfully!');
    }

    /**
     * Assign all subjects of a grade level to a professor
     */
    public function assignSubjectsToProfessor(Request $request)
    {
        $professorId = $request->user_id;
        $gradeLevelId = $request->grade_level;

        $subjectIds = Subject::where('grade_level_id', $gradeLevelId)->pluck('id');

        $insertData = $subjectIds->map(function ($subjectId) use ($professorId) {
            return [
                'user_id'    => $professorId,
                'subject_id' => $subjectId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        DB::table('professor_subjects')->insertOrIgnore($insertData);

        return back()->with('success', 'All subjects assigned to professor.');
    }
}

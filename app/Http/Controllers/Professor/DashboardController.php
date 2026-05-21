<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\ProfessorSubject;
use App\Models\Advisory;
use App\Models\User;
use App\Models\Subject;
use App\Models\GradeLevel;
use App\Models\Enrollment;

class DashboardController extends Controller
{
    public function index()
    {
        $professorId = Auth::id();
        $schoolYear = now()->year . '-' . (now()->year + 1);
        $gradeLevels = GradeLevel::pluck('name', 'id')->toArray();

        // --- Professor Grades ---
        $professorGrades = ProfessorSubject::where('user_id', $professorId)
            ->join('subjects', 'professor_subjects.subject_id', '=', 'subjects.id')
            ->pluck('subjects.grade_level_id')
            ->unique();

        // --- Subjects Handled ---
        $subjectIds = ProfessorSubject::where('user_id', $professorId)->pluck('subject_id');
        $totalSubjects = $subjectIds->count();

        $subjectsHandled = ProfessorSubject::with('subject.gradeLevel')
            ->where('user_id', $professorId)
            ->get()
            ->map(function ($record) {
                $subject = $record->subject;

                $students = Enrollment::where('grade_level_id', $subject->grade_level_id)
                    ->where('status', 'approved')
                    ->whereIn('id', function ($q) {
                        $q->selectRaw('MAX(id)')->from('enrollments')->groupBy('user_id');
                    })
                    ->with('user')
                    ->get();

                return (object)[
                    'subject_name'  => $subject->name ?? 'Unknown Subject',
                    'grade_level'   => $subject->gradeLevel->name ?? 'N/A',
                    'student_count' => $students->count(),
                    'students'      => $students->map(fn($s) => $s->user->name),
                ];
            });

        // --- Students (latest enrollments) ---
        $students = Enrollment::whereIn('grade_level_id', $professorGrades)
    ->where('status', 'approved')
    ->where(function ($q) {
        $q->whereNull('completion_status')
          ->orWhere('completion_status', 'active');
    })
    ->whereIn('id', function ($query) {
        $query->selectRaw('MAX(id)')
              ->from('enrollments')
              ->groupBy('user_id');
    })
    ->with('user')
    ->orderBy('grade_level_id')
    ->get();


        $totalStudents = $students->count();
        $studentsPerGrade = $students->groupBy('grade_level_id'); // For students modal

        // --- Grades ---
        $submittedGradesCount = Grade::whereIn('subject_id', $subjectIds)
            ->where('school_year', $schoolYear)
            ->count();

        $recentGrades = Grade::with(['user', 'subject'])
            ->whereIn('subject_id', $subjectIds)
            ->where('school_year', $schoolYear)
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->map(function ($grade) {
                return (object)[
                    'student_name' => $grade->user->last_name . ', ' . $grade->user->first_name,
                    'subject_name' => $grade->subject->name,
                    'grade'        => $grade->grade,
                    'quarter'      => $grade->quarter,
                ];
            });

        // --- Returned Grades ---
        $returnedGrades = Grade::whereIn('subject_id', $subjectIds)
            ->where('status', 'returned')
            ->with(['user', 'subject'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $returnedGradesCount = $returnedGrades->count();

        // --- Advisory ---
        $advisory = Advisory::where('user_id', $professorId)->first();
        $isAdviser = $advisory ? true : false;
        $advisoryGradeLevel = $advisory->grade_level_id ?? null;
        $advisoryStudents = collect();

        if ($advisoryGradeLevel) {
            $advisoryStudents = User::where('role', 'student')
                ->whereHas('enrollments', function ($q) use ($advisoryGradeLevel) {
                    $q->where('grade_level_id', $advisoryGradeLevel)
                      ->where('status', 'approved');
                })
                ->orderBy('name')
                ->get();
        }

        // --- Honor Students ---
        $selectedQuarters = request()->quarters ?? [1,2,3,4];
        $honorStudents = User::where('role', 'student')
            ->whereHas('enrollments', function ($q) use ($professorGrades) {
                $q->whereIn('grade_level_id', $professorGrades)
                  ->where('status', 'approved');
            })
            ->with(['grades' => function ($q) use ($schoolYear, $selectedQuarters) {
                $q->where('school_year', $schoolYear)
                  ->whereIn('quarter', $selectedQuarters);
            }, 'enrollments.gradeLevel'])
            ->get()
            ->map(function ($student) {
                $gradesData = [];
                foreach ($student->grades->groupBy('subject_id') as $subjectId => $grades) {
                    $gradesData[$subjectId] = $grades->pluck('grade')->filter(fn($g) => $g !== null)->all();
                }

                $subjectAverages = [];
                foreach ($gradesData as $subGrades) {
                    $subjectAverages[] = count($subGrades) ? array_sum($subGrades)/count($subGrades) : null;
                }

                $validAverages = array_filter($subjectAverages, fn($avg) => $avg !== null);
                $overallAverage = count($validAverages) ? array_sum($validAverages)/count($validAverages) : 0;

                $allGradesFlat = array_merge(...array_values($gradesData));
                $minGrade = count($allGradesFlat) ? min($allGradesFlat) : 0;

                if ($minGrade < 87 || $overallAverage < 75) return null;

                return (object)[
                    'id' => $student->id,
                    'name' => $student->name,
                    'overall_average' => round($overallAverage),
                    'grade_level' => $student->enrollments()->latest()->first()?->gradeLevel->name ?? 'N/A',
                    'honor' => 'With Honors',
                ];
            })
            ->filter()
            ->values();

        // --- AI Insights ---
        $studentIds = $students->pluck('user_id');

        $studentAverages = Grade::selectRaw('user_id, AVG(grade) as avg_grade')
            ->whereIn('user_id', $studentIds)
            ->groupBy('user_id')
            ->pluck('avg_grade', 'user_id');

        $atRiskCount = $studentAverages->filter(fn($avg) => $avg < 85)->count();
        $honorForecastCount = $studentAverages->filter(fn($avg) => $avg >= 90)->count();

        $decliningCount = Grade::whereIn('user_id', $studentIds)
            ->orderBy('quarter')
            ->get()
            ->groupBy('user_id')
            ->filter(function ($grades) {
                $grades = $grades->pluck('grade')->values();
                return count($grades) >= 2 && $grades->last() < $grades->first();
            })
            ->count();

        $lowestSubjectData = Grade::whereIn('subject_id', $subjectIds)
            ->selectRaw('subject_id, AVG(grade) as avg_grade')
            ->groupBy('subject_id')
            ->orderBy('avg_grade')
            ->first();

        $lowestSubject = optional($lowestSubjectData?->subject)->name ?? 'N/A';
        $lowestAvg = round($lowestSubjectData->avg_grade ?? 0);

        $aiInsights = [
            'atRiskCount' => $atRiskCount,
            'decliningCount' => $decliningCount,
            'honorForecastCount' => $honorForecastCount,
            'lowestSubject' => $lowestSubject,
            'lowestAvg' => $lowestAvg,
        ];
        
         $studentsList = User::whereIn('id', $studentIds)->pluck('name', 'id');
         $subjectsList = Subject::whereIn('id', $subjectIds)->pluck('name', 'id');

        return view('professor.dashboard', compact(
            'totalSubjects',
            'totalStudents',
            'submittedGradesCount',
            'recentGrades',
            'subjectsHandled',
            'returnedGrades',
            'isAdviser',
            'advisoryGradeLevel',
            'advisoryStudents',
            'studentsPerGrade',
            'honorStudents',
            'returnedGradesCount',
            'aiInsights',
            'studentsList',
            'subjectsList',
            'honorStudents'

        ));
    }

    public function advisory()
    {
        $teacherId = auth()->id();
        $advisory = Advisory::where('user_id', $teacherId)->first();

        if (!$advisory) abort(403, 'You are not an adviser.');

        $gradeLevel = $advisory->grade_level_id;
        $gradeLevelName = GradeLevel::where('id', $gradeLevel)->value('name');

        $students = User::where('role', 'student')
            ->whereHas('enrollments', fn($q) => $q->where('grade_level_id', $gradeLevel)->where('status', 'approved'))
            ->orderBy('name')
            ->get();

        return view('professor.advisory.index', compact('students', 'gradeLevel', 'gradeLevelName'));
    }

    public function advisoryView($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();

        $grades = Grade::where('user_id', $id)
            ->with('subject.gradeLevel')
            ->get()
            ->groupBy('subject_id');

        return view('professor.advisory.student', compact('student', 'grades'));
    }

    public function filterHonor(Request $request)
    {
        $quarter = $request->query('quarter');
        $schoolYear = now()->year . '-' . (now()->year + 1);

        $students = User::where('role', 'student')
            ->with(['grades' => function($q) use ($schoolYear, $quarter) {
                $q->where('school_year', $schoolYear);
                if ($quarter !== 'all') $q->where('quarter', $quarter);
            }])
            ->get();

        $honorStudents = [];

        foreach ($students as $student) {
            $grades = $student->grades;
            if ($grades->count() == 0) continue;

            $gradesData = [];
            foreach ($grades->groupBy('subject_id') as $subjectId => $subjectGrades) {
                $gradesData[$subjectId] = $subjectGrades->pluck('grade')->filter(fn($g) => $g !== null)->all();
            }
            if (count($gradesData) == 0) continue;

            $subjectAverages = [];
            foreach ($gradesData as $subGrades) {
                $subjectAverages[] = count($subGrades) ? array_sum($subGrades)/count($subGrades) : null;
            }

            $validAverages = array_filter($subjectAverages, fn($avg) => $avg !== null);
            $overallAverage = count($validAverages) ? array_sum($validAverages)/count($validAverages) : 0;

            $allGradesFlat = array_merge(...array_values($gradesData));
            $minGrade = count($allGradesFlat) ? min($allGradesFlat) : 0;

            if ($minGrade < 87 || $overallAverage < 90) continue;

            $honorStudents[] = [
                'id' => $student->id,
                'name' => $student->name,
                'grade_level' => $student->enrollments()->latest()->first()?->gradeLevel->name ?? 'N/A',
                'honor' => 'With Honors',
                'average' => round($overallAverage),
            ];
        }

        return response()->json($honorStudents);
    }

    public function markResolved($gradeId)
    {
        $grade = Grade::findOrFail($gradeId);

        $professorId = auth()->id();
        if (!ProfessorSubject::where('user_id', $professorId)->where('subject_id', $grade->subject_id)->exists()) {
            abort(403, 'You are not authorized to resolve this grade.');
        }

        $grade->status = 'submitted';
        $grade->is_notified = false;
        $grade->save();

        return back()->with('success', 'Returned grade marked as resolved.');
    }
}

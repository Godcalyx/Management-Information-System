<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\PromotionHistory;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Grade;

class StudentController extends Controller
{
    /* ============================================================
       STUDENTS LIST PAGE
    ============================================================ */
    public function index(Request $request)
    {
        $query = Enrollment::with(['gradeLevel', 'user']) // eager load gradeLevel and user
            ->where('status', 'approved')
            ->where(function($q){
                $q->whereNull('completion_status')
                  ->orWhere('completion_status', '!=', 'Graduated');
            });

        if ($request->filled('promotion_status')) {
            $query->where('promotion_status', $request->promotion_status);
        }

        if ($request->filled('grade_level_id')) {
            $query->where('grade_level_id', $request->grade_level_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%");
            });
        }

        $students = Enrollment::with(['user', 'gradeLevel'])
                ->when($request->grade_level_id, fn($q) => $q->where('grade_level_id', $request->grade_level_id))
                ->paginate(10);


        // Latest enrollment per user
        $query->whereIn('id', function ($q) {
            $q->selectRaw('MAX(id)')->from('enrollments')->groupBy('user_id');
        });

        $students = $query->orderBy('grade_level_id')->paginate(10);

        if ($request->ajax()) {
            return view('admin.students.partials.students_table', compact('students'))->render();
        }

        $gradeLevels = GradeLevel::orderBy('id')->get();

        return view('admin.students.index', compact('students','gradeLevels'));
    }

    /* ============================================================
       BULK PROMOTION
    ============================================================ */
    public function promoteBulk(Request $request)
    {
        $request->validate([
            'grade_level_id' => 'required|integer',
        ]);

        $students = Enrollment::where('grade_level_id', $request->grade_level_id)
            ->where('status', 'approved')
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')->from('enrollments')->groupBy('user_id');
            })
            ->get();

        $failed = [];

        foreach ($students as $student) {
            $promotionCheck = $this->canPromote($student);

            if (!$promotionCheck['can_promote']) {
                $failed[] = [
                    'enrollment_id' => $student->id,
                    'user_id' => $student->user_id,
                    'name' => "{$student->first_name} {$student->last_name}",
                    'reason' => $promotionCheck['reason']
                ];
                continue;
            }

            $calc = $this->calculatePromotionMetrics($student);
            if ($calc['gpa'] >= 83 && $calc['msr_avg'] >= 85 && !$calc['has_failing']) {
                $this->performPromotion($student);
            } else {
                $failed[] = [
                    'enrollment_id' => $student->id,
                    'user_id' => $student->user_id,
                    'name' => "{$student->first_name} {$student->last_name}",
                    'gpa' => $calc['gpa'],
                    'msr_avg' => $calc['msr_avg'],
                    'reason' => $calc['has_failing'] ? 'Has failing grade' : 'Did not meet criteria'
                ];
            }
        }

        return count($failed) > 0
            ? response()->json(['failed' => $failed])
            : response()->json(['message' => 'All students promoted successfully.']);
    }

    /* ============================================================
       SINGLE PROMOTION
    ============================================================ */
    public function promoteApproved($id)
    {
        $student = Enrollment::findOrFail($id);

        $promotionCheck = $this->canPromote($student);
        if (!$promotionCheck['can_promote']) {
            return response()->json([
                'error' => $student->first_name . ' cannot be promoted: ' . $promotionCheck['reason']
            ]);
        }

        $this->performPromotion($student);

        return response()->json(['success' => $student->first_name . ' has been promoted.']);
    }

    /* ============================================================
       HELPER: CHECK IF STUDENT HAS COMPLETE GRADES
    ============================================================ */
    private function canPromote($student)
    {
        $grades = Grade::where('user_id', $student->user_id)
            ->where('school_year', $student->school_year) // use school_year instead of grade_level_id
            ->with('subject')
            ->get();

        $subjects = $grades->groupBy('subject_id');

        if ($subjects->isEmpty() ||
            $subjects->some(fn($qs) => $qs->pluck('quarter')->unique()->count() < 4)) {
            return ['can_promote' => false, 'reason' => 'Incomplete grades'];
        }

        return ['can_promote' => true, 'reason' => ''];
    }

    /* ============================================================
       HELPER: GPA + MSR CALCULATIONS
    ============================================================ */
    private function calculatePromotionMetrics($student)
    {
        $grades = Grade::where('user_id', $student->user_id)
            ->where('school_year', $student->school_year) // school_year instead of grade_level_id
            ->with('subject')
            ->get();

        $subjects = $grades->groupBy('subject_id');

        $total = 0;
        $count = 0;
        $msr_total = 0;
        $msr_count = 0;
        $hasFailing = false;

        foreach ($subjects as $qs) {
            $final = ceil($qs->avg('grade'));
            $total += $final;
            $count++;

            $name = strtolower($qs->first()->subject->name ?? '');

            if (str_contains($name, 'math') ||
                str_contains($name, 'science') ||
                str_contains($name, 'research')) {
                $msr_total += $final;
                $msr_count++;
            }

            if ($qs->contains(fn($q) => $q->grade < 75)) {
                $hasFailing = true;
            }
        }

        return [
            'gpa' => $count ? ceil($total / $count) : 0,
            'msr_avg' => $msr_count ? ceil($msr_total / $msr_count) : 0,
            'has_failing' => $hasFailing
        ];
    }

    /* ============================================================
       HELPER: PERFORM PROMOTION
    ============================================================ */
    private function performPromotion($student)
    {
        $current = GradeLevel::find($student->grade_level_id);

        $next = GradeLevel::where('id', '>', $current->id)
            ->orderBy('id')
            ->first();

        if (!$next) {
            $student->promotion_status = 'completed';
            $student->completion_status = 'Graduated';
            $student->save();
            return;
        }

        $nextGradeId = $next->id;
        $nextYear = $this->getNextSchoolYear($student->school_year);

        $exists = Enrollment::where('lrn', $student->lrn)
            ->where('school_year', $nextYear)
            ->exists();

        if ($exists) return;

        DB::transaction(function () use ($student, $nextGradeId, $nextYear) {
            $new = Enrollment::create([
                'user_id' => $student->user_id,
                'lrn' => $student->lrn,
                'email' => $student->email,
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'last_name' => $student->last_name,
                'birthdate' => $student->birthdate,
                'birthplace' => $student->birthplace,
                'sex' => $student->sex,
                'school_year' => $nextYear,
                'grade_level_id' => $nextGradeId,
                'status' => 'approved',
                'promotion_status' => 'pending',
            ]);

            PromotionHistory::create([
                'enrollment_id' => $new->id,
                'admin_id' => Auth::id(),
                'from_grade' => $student->grade_level_id,
                'to_grade' => $nextGradeId,
                'school_year' => $nextYear,
            ]);
        });
    }

    /* ============================================================
       COMPUTE NEXT SCHOOL YEAR
    ============================================================ */
    private function getNextSchoolYear($year)
    {
        if (preg_match('/(\d{4})-(\d{4})/', $year, $m)) {
            return ($m[1] + 1) . '-' . ($m[2] + 1);
        }
        return $year;
    }

    /* ============================================================
       VIEW GRADES (MODAL)
    ============================================================ */
    public function viewGrades(Request $request, $enrollment_id)
    {
        try {
            $enrollment = Enrollment::with('user')->find($enrollment_id);

            if (!$enrollment) {
                return response()->json([
                    'student_name' => 'N/A',
                    'school_year' => 'N/A',
                    'grades' => []
                ]);
            }

            $grades = Grade::where('user_id', $enrollment->user_id)
                ->where('school_year', $enrollment->school_year)
                ->with('subject')
                ->get();

            $data = $grades->map(function ($g) {
                return [
                    'subject' => $g->subject->name ?? 'N/A',
                    'quarter' => $g->quarter,
                    'grade' => $g->grade
                ];
            });

            return response()->json([
                'student_name' => $enrollment->user->name ?? 'N/A',
                'school_year' => $enrollment->school_year,
                'grades' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('viewGrades error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load grades'], 500);
        }
    }

    /* ============================================================
       EDIT + UPDATE STUDENT
    ============================================================ */
    public function edit($userId)
{
    $enrollment = \App\Models\Enrollment::where('user_id', $userId)->firstOrFail();
    $gradeLevels = \App\Models\GradeLevel::all();

    return response()->json([
        'first_name' => $enrollment->first_name,
        'middle_name' => $enrollment->middle_name,
        'last_name' => $enrollment->last_name,
        'email' => $enrollment->email,
        'lrn' => $enrollment->lrn,
        'grade_level_id' => $enrollment->grade_level_id,
        'all_grade_levels' => $gradeLevels
    ]);
}

   public function update(Request $request, $userId)
{
    $request->validate([
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $userId,
        'lrn' => 'required|string|unique:users,lrn,' . $userId,
        'grade_level_id' => 'required|exists:grade_levels,id',
    ]);

    // Update the latest enrollment (names + grade level)
    $enrollment = Enrollment::where('user_id', $userId)->latest()->first();
    if ($enrollment) {
        $enrollment->first_name = $request->first_name;
        $enrollment->middle_name = $request->middle_name;
        $enrollment->last_name = $request->last_name;
        $enrollment->grade_level_id = $request->grade_level_id;
        $enrollment->save();
    }

    // Update users table (email + LRN)
    $user = User::findOrFail($userId);
    $user->email = $request->email;
    $user->lrn = $request->lrn;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Student updated successfully',
        'enrollment' => [
            'first_name' => $enrollment->first_name ?? '',
            'middle_name' => $enrollment->middle_name ?? '',
            'last_name' => $enrollment->last_name ?? '',
            'grade_level_name' => $enrollment->gradeLevel?->name ?? '',
        ]
    ]);
}



}

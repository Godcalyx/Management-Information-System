<?php

namespace App\Http\Controllers\Professor;

use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\GradeLevel;

class AttendanceController extends Controller
{
    /**
     * Show attendance page
     */
    public function index()
    {
        // Load all grade levels ordered numerically
        $gradeLevels = GradeLevel::orderByRaw('CAST(name AS UNSIGNED) ASC')->get();

        return view('professor.attendance.index', compact('gradeLevels'));
    }

    /**
     * Fetch students dynamically (AJAX)
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'grade_level_id' => 'required|exists:grade_levels,id',
            'month'          => 'required|string',
            'school_year'    => 'required|string',
            'days_of_school' => 'required|integer|min:1',
        ]);

        $gradeLevelId = $request->grade_level_id;
        $schoolYear   = $request->school_year;
        $daysOfSchool = $request->days_of_school;

        // Step 1: Get latest approved and current enrollment IDs per student
        $latestEnrollmentIds = Enrollment::where('status', 'approved')
            ->where('is_current', 1) // only current students
            ->where('grade_level_id', $gradeLevelId)
            ->where('school_year', $schoolYear)
            ->pluck('id');

        // Step 2: Fetch students with these latest enrollments
        $students = User::where('role', 'student')
            ->whereIn('id', function ($query) use ($latestEnrollmentIds) {
                $query->select('user_id')
                      ->from('enrollments')
                      ->whereIn('id', $latestEnrollmentIds);
            })
            ->with(['enrollments' => function ($q) use ($latestEnrollmentIds) {
                $q->whereIn('id', $latestEnrollmentIds);
            }])
            ->orderBy('name')
            ->get();

        // Step 3: Fetch attendance records for these enrollments
        $records = Attendance::where('school_year', $schoolYear)
            ->where('month', $request->month)
            ->whereIn('enrollment_id', $latestEnrollmentIds)
            ->get()
            ->keyBy('enrollment_id');

        // Step 4: Render the partial table
        $html = view(
            'professor.attendance.partials.students_table',
            compact('students', 'records', 'request', 'daysOfSchool')
        )->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Store attendance records (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_year'            => 'required|string',
            'month'                  => 'required|string',
            'days_of_school'         => 'required|integer|min:1',
            'records'                => 'required|array',
            'records.*.days_present' => 'required|numeric|min:0',
            'records.*.times_tardy' => 'required|numeric|min:0',
        ]);

        $daysOfSchool = $request->days_of_school;

        foreach ($request->records as $userId => $data) {

            // Fetch latest approved and current enrollment
            $enrollment = Enrollment::where('user_id', $userId)
                ->where('status', 'approved')
                ->where('is_current', 1)
                ->orderByDesc('id')
                ->first();

            if (!$enrollment) continue;

            Attendance::updateOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'school_year'   => $request->school_year,
                    'month'         => $request->month,
                ],
                [
                    'days_of_school' => $daysOfSchool,
                    'days_present'   => $data['days_present'],
                    'days_absent'    => max(0, $daysOfSchool - $data['days_present']),
                    'times_tardy'    => $data['times_tardy'],
                    'encoded_by'     => Auth::id(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully.'
        ]);
    }
}

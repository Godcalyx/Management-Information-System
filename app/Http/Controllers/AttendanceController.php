<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Show attendance form by grade & month
    public function index(Request $request)
{
    $grade = $request->grade;
    $month = $request->month;
    $school_year = $request->school_year;

    $students = collect();
    $records = [];

    if ($grade && $month && $school_year) {
        $students = \App\Models\User::where('role', 'student')
            ->whereHas('enrollment', function ($q) use ($grade, $school_year) {
                $q->where('grade_level', $grade)
                  ->where('school_year', $school_year);
            })
            ->orderBy('name')
            ->get();

        $records = \App\Models\Attendance::where('school_year', $school_year)
            ->where('month', $month)
            ->get()
            ->keyBy('user_id');
    }

    return view('admin.attendance.index', compact('students', 'records', 'grade', 'month', 'school_year'));
}


    // Save attendance records
    public function store(Request $request)
{
    // Validate input
    $request->validate([
        'school_year' => 'required|string',
        'month'       => 'required|string',
        'records'     => 'required|array',
        'records.*.days_of_school' => 'required|numeric',
        'records.*.days_present'   => 'required|numeric',
        'records.*.days_absent'    => 'required|numeric',
        'records.*.times_tardy'    => 'required|numeric',
    ]);

    foreach ($request->records as $userId => $data) {
    $enrollment = Enrollment::where('user_id', $userId)
        ->where('status', 'approved')
        ->first();

    if (!$enrollment) {
        \Log::warning("Attendance skipped: User ID {$userId} has no approved enrollment.");
        continue;
    }

//         \Log::info('Saving attendance', [
//     'enrollment_id' => $enrollmentId,
//     'school_year' => $request->school_year,
//     'month' => $request->month,
//     'existing' => Attendance::where('enrollment_id', $enrollmentId)
//                               ->where('school_year', $request->school_year)
//                               ->where('month', $request->month)->first()
// ]);

        // Insert or update attendance
        Attendance::updateOrCreate(
        [
            'enrollment_id' => $enrollment->id,
            'school_year'   => $request->school_year,
            'month'         => $request->month
        ],
        [
            'days_of_school' => $data['days_of_school'],
            'days_present'   => $data['days_present'],
            'days_absent'    => $data['days_absent'],
            'times_tardy'    => $data['times_tardy'],
            'encoded_by'     => auth()->id(),
        ]
    );
}

    return back()->with('success', 'Attendance records saved successfully.');
}

}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GradesController extends Controller
{
    public function index()
{
    $userId = Auth::id();

    // Fetch all subjects where this student is enrolled (via grades table)
    $subjectList = DB::table('grades')
        ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
        ->where('grades.user_id', $userId)
        ->select('subjects.id', 'subjects.name')
        ->distinct()
        ->orderBy('subjects.name')
        ->get();

    // Initialize subjects with empty quarters
    $subjects = [];
    foreach ($subjectList as $subject) {
        $subjects[$subject->name] = [
            'q1' => '—',
            'q2' => '—',
            'q3' => '—',
            'q4' => '—'
        ];
    }

    // Fetch actual grades
    $grades = DB::table('grades')
        ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
        ->where('grades.user_id', $userId)
        ->select('subjects.name as subject', 'grades.quarter', 'grades.grade')
        ->get();

    // Fill in the actual grades
    foreach ($grades as $entry) {
        $quarterKey = 'q' . $entry->quarter; // e.g. q1, q2
        if (isset($subjects[$entry->subject])) {
            $subjects[$entry->subject][$quarterKey] = $entry->grade;
        }
    }

    return view('student.grades', compact('subjects'));
}

}

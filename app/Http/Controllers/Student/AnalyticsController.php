<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Add this if not already there


class AnalyticsController extends Controller
{
    public function index()
{
    $grades = DB::table('grades')
        ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
        ->where('grades.user_id', auth()->id())
        ->select('subjects.name as subject', DB::raw('AVG(CAST(grades.grade AS DECIMAL(5,2))) as average'))
        ->groupBy('subjects.name')
        ->orderBy('subjects.name')
        ->get();

    $labels = $grades->pluck('subject')->toArray(); // Convert to plain array
    $data = $grades->pluck('average')->map(fn($grade) => (float) $grade)->toArray();

    \Log::info('Grade Analytics Data', ['labels' => $labels, 'data' => $data]);

    return view('student.grade-analytics', compact('labels', 'data'));
}

}

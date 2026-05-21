<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Enrollment;

class AdminGradeController extends Controller
{
    /**
     * Display a list of grade levels or sections with submitted grades.
     */
    public function index(Request $request)
    {
        // Fetch unique grade levels & school years that have submitted grades
        $gradeLevels = Grade::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        return view('admin.grades.index', compact('gradeLevels'));
    }

    /**
     * Show submitted grades per grade level or section.
     */
    public function show($schoolYear)
    {
        // Fetch all submitted grades for review
        $grades = Grade::where('school_year', $schoolYear)
            ->where('status', 'submitted')
            ->with('user') // assuming you have a relation in Grade model
            ->get();

        return view('admin.grades.show', compact('grades', 'schoolYear'));
    }

    /**
     * Approve all submitted grades for a particular school year or level.
     */
    public function approveGrades(Request $request)
    {
        $schoolYear = $request->school_year;

        // Update status to approved
        $updated = Grade::where('school_year', $schoolYear)
            ->where('status', 'submitted')
            ->update(['status' => 'approved']);

        if ($updated > 0) {
            return back()->with('success', 'All submitted grades for ' . $schoolYear . ' have been approved.');
        }

        return back()->with('info', 'No submitted grades found for this school year.');
    }
}

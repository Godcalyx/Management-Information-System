<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessorSubjectGradeLevel;
use App\Models\Subject;
use App\Models\User;

class ClassesController extends Controller
{
    public function index()
{
    $assignedSubjects = ProfessorSubjectGradeLevel::with('subject')
        ->where('user_id', Auth::id())
        ->get()
        ->groupBy('grade_level');

    return view('professor.classes', compact('assignedSubjects'));
}
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Subject;
use App\Models\ProfessorSubjectGradeLevel;
use App\Mail\NewProfessorCredentialsMail;

class ProfessorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = User::role('professor');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $professors = $query->latest()->paginate(10);

        // Grouped by professor for display
        $assignments = ProfessorSubjectGradeLevel::with('subject')->get()->groupBy('user_id');

        // Group subjects by grade level (for select optgroup)
        $subjectsGrouped = Subject::all()->groupBy('grade_level');

        return view('admin.professors.index', compact('professors', 'subjectsGrouped', 'assignments'));
    }

    public function create()
    {
        return view('admin.professors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $temporaryPassword = Str::random(10);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($temporaryPassword),
            'role' => 'professor',
        ]);

        $user->assignRole('professor');

        Mail::to($user->email)->send(new NewProfessorCredentialsMail($user, $temporaryPassword));

        return redirect()->route('admin.professors.index')->with('success', 'Professor added and credentials sent via email.');
    }

    public function assignSubject(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'grade_level' => 'required|integer|min:7|max:10',
        ]);

        $exists = ProfessorSubjectGradeLevel::where([
            'user_id' => $request->user_id,
            'subject_id' => $request->subject_id,
            'grade_level' => $request->grade_level,
        ])->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This assignment already exists.');
        }

        ProfessorSubjectGradeLevel::create($request->only('user_id', 'subject_id', 'grade_level'));

        return redirect()->back()->with('success', 'Subject and grade level assigned successfully.');
    }

    // This is no longer used in the updated Blade (but you can keep if needed elsewhere)
    public function getSubjectsByGrade($grade)
    {
        $subjects = Subject::where('grade_level', $grade)->get(['id', 'name']);
        return response()->json($subjects);
    }
    public function ajaxUpdate(Request $request, User $professor)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $professor->id,
    ]);

    $professor->update($validated);

    return response()->json(['success' => true]);
}

}

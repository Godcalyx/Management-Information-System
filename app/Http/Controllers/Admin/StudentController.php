<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::where('status', 'approved');


        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->input('grade_level'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('middle_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('lrn', 'like', "%$search%");
            });
        }

        $students = $query->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'lrn' => 'required|string|size:12|unique:enrollments,lrn',
            'grade_level' => 'required|integer|min:7|max:10',
        ]);

        Enrollment::create($request->only([
            'first_name',
            'middle_name',
            'last_name',
            'lrn',
            'grade_level',
        ]));

        return redirect()->route('admin.students.index')->with('success', 'Student added successfully.');
    }

    public function edit($id)
    {
        $student = Enrollment::findOrFail($id);

        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Enrollment::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'lrn' => 'required|string|size:12|unique:enrollments,lrn,' . $student->id,
            'grade_level' => 'required|integer|min:7|max:10',
        ]);

        $student->update($request->only([
            'first_name',
            'middle_name',
            'last_name',
            'lrn',
            'grade_level',
        ]));

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy($id)
    {
        $student = Enrollment::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
   public function index()
{
    // Fetch subjects with their grade level
    $subjects = Subject::with('gradeLevel')->orderBy('name')->get();

    // Fetch all grade levels for the select dropdown in the form
    $grade_levels = GradeLevel::orderBy('order')->get();

    return view('admin.subjects.index', compact('subjects', 'grade_levels'));
}



    public function create()
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        return view('admin.subjects.create', compact('gradeLevels'));
    }

  public function store(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'version' => 'required|string|max:255',
        'grade_levels' => 'required|array|min:1',
        'grade_levels.*' => 'exists:grade_levels,id',
    ]);

    // Use the first grade level as the main grade_level_id
    $mainGradeLevel = $validated['grade_levels'][0];

    // Create subject with main grade level
    $subject = Subject::create([
        'name' => $validated['name'],
        'version' => $validated['version'],
        'grade_level_id' => $mainGradeLevel,
    ]);

    // Sync all assigned grade levels in pivot table
    $subject->gradeLevels()->sync($validated['grade_levels']);

    return redirect()->route('admin.subjects.index')
                     ->with('success', 'Subject added successfully.');
}



    public function edit(Subject $subject)
    {
        $gradeLevels = GradeLevel::orderBy('order')->get();
        $assignedLevels = $subject->gradeLevels()->pluck('grade_levels.id')->toArray();
        return view('admin.subjects.edit', compact('subject','gradeLevels','assignedLevels'));
    }

    public function update(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:20',
            'grade_levels' => 'required|array|min:1',
            'grade_levels.*' => 'exists:grade_levels,id',
        ]);

        $subject = Subject::findOrFail($id);

        // Update basic fields and main grade level
        $subject->name = $request->name;
        $subject->version = $request->version;
        $subject->grade_level_id = $request->grade_levels[0]; // keep main grade level required
        $subject->save();

        // Sync many-to-many grade levels
        $subject->gradeLevels()->sync($request->grade_levels);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        // Prevent deletion if subject is used in grade records
        if ($subject->gradeLevels()->exists()) {
            return back()->with('error','Cannot delete: subject is assigned to grade levels.');
        }

        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success','Subject deleted.');
    }

    public function reorder(Request $request)
    {
        $ids = $request->input('order', []);
        if (!is_array($ids)) return response()->json(['message'=>'Invalid'],422);

        DB::transaction(function() use ($ids) {
            foreach($ids as $index => $id){
                Subject::where('id',$id)->update(['order'=>$index+1]);
            }
        });

        return response()->json(['message'=>'Order updated']);
    }
}

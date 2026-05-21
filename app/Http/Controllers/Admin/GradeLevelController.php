<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeLevelController extends Controller
{
    public function index()
    {
        // Fetch all grade levels ordered by 'order'
        $grade_levels = GradeLevel::orderBy('order', 'asc')->get();

        // Pass the variable to the view
        return view('admin.grade_levels.index', compact('grade_levels'));
    }

    public function create()
    {
        return view('admin.grade_levels.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:grade_levels,name',
            'description' => 'nullable|string|max:255',
            'order' => 'nullable|integer|unique:grade_levels,order',
        ]);

        // If no order provided, append to end
        if (empty($data['order'])) {
            $last = GradeLevel::max('order') ?? 0;
            $data['order'] = $last + 1;
        }

        GradeLevel::create($data);

        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'Grade level created.');
    }

    public function edit(GradeLevel $gradeLevel)
    {
        return view('admin.grade-levels.edit', compact('gradeLevel'));
    }

    public function update(Request $request, GradeLevel $gradeLevel)
{
    $data = $request->validate([
        'name' => 'required|string|max:100|unique:grade_levels,name,' . $gradeLevel->id,
        'description' => 'nullable|string|max:255',
        'order' => 'required|integer|unique:grade_levels,order,' . $gradeLevel->id,
    ]);

    $gradeLevel->update($data);

    if(request()->ajax()){
        return response()->json(['success' => true]);
    }

    return redirect()->route('admin.grade-levels.index')->with('success', 'Grade level updated.');
}


    public function destroy(GradeLevel $gradeLevel)
    {
        if ($gradeLevel->enrollments()->exists()) {
            return redirect()->route('admin.grade-levels.index')
                ->with('error', 'Cannot delete: grade level has enrollments.');
        }

        $gradeLevel->delete();

        return redirect()->route('admin.grade-levels.index')
            ->with('success', 'Grade level deleted.');
    }

    /**
     * Reorder grade levels via AJAX.
     * Expects: { order: [id1, id2, id3, ...] }
     */
    public function reorder(Request $request)
    {
        $ids = $request->input('order', []);
        if (!is_array($ids)) {
            return response()->json(['message' => 'Invalid payload'], 422);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                GradeLevel::where('id', $id)->update(['order' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Order updated']);
    }
}

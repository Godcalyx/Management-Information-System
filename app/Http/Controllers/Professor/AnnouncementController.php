<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
{
    // Fetch announcements for the logged-in professor
    $announcements = Announcement::where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    // 1️⃣ Get all grade levels from subjects the professor teaches
    $subjectGrades = \DB::table('subjects')
        ->join('professor_subjects', 'subjects.id', '=', 'professor_subjects.subject_id')
        ->where('professor_subjects.user_id', auth()->id())
        ->pluck('subjects.grade_level_id') // updated for grade_level_id
        ->toArray();

    // 2️⃣ Get advisory grade level if exists
    $adviserGrade = auth()->user()->advisory->grade_level_id ?? null;

    // 3️⃣ Merge and remove duplicates
    $allGradeIds = collect($subjectGrades);
    if ($adviserGrade) {
        $allGradeIds->push($adviserGrade);
    }
    $allGradeIds = $allGradeIds->unique()->toArray();

    // 4️⃣ Fetch grade names for display
    $professorGrades = \DB::table('grade_levels')
        ->whereIn('id', $allGradeIds)
        ->pluck('name', 'id'); // returns [id => name]

    return view('professor.announcements.index', compact('announcements', 'professorGrades'));
}


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_grades' => 'nullable|array',
            'target_grades.*' => 'string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048',
        ]);

        // Fetch professor's allowed grade levels
        $allowedGrades = DB::table('subjects')
            ->join('professor_subjects', 'subjects.id', '=', 'professor_subjects.subject_id')
            ->join('grade_levels', 'subjects.grade_level_id', '=', 'grade_levels.id')
            ->where('professor_subjects.user_id', auth()->id())
            ->pluck('grade_levels.name')
            ->unique()
            ->toArray();

        // Validate target_grades against allowed grades
        if ($request->target_grades) {
            foreach ($request->target_grades as $grade) {
                if (!in_array($grade, $allowedGrades)) {
                    return back()->withErrors(['target_grades' => 'Invalid grade selected.']);
                }
            }
        }

        // Handle file upload
        $attachmentPath = $request->hasFile('attachment')
            ? $request->file('attachment')->store('announcements', 'public')
            : null;

        // Prepend "Grade " to each target grade
        $formattedGrades = $request->target_grades
            ? json_encode(array_map(fn($grade) => 'Grade ' . $grade, $request->target_grades))
            : null;

        // Create the announcement
        Announcement::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'target_grades' => $formattedGrades,
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('professor.announcements')
            ->with('success', 'Announcement posted successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('professor.announcements')
            ->with('success', 'Announcement deleted successfully.');
    }
}

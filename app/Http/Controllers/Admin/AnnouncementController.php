<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('admin.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
            'target_grades' => 'nullable|array',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('announcements', 'public');
        }

        // Create announcement
        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'attachment' => $attachmentPath,
            'user_id' => auth()->id(),
            'target_grades' => $request->target_grades ? json_encode($request->target_grades) : null,
        ]);

        // Notify relevant students
        $students = User::where('role', 'student');
        if ($request->target_grades) {
            $students = $students->whereIn('grade_level', $request->target_grades);
        }
        $students = $students->get();

        foreach ($students as $student) {
            $student->notify(new NewAnnouncementNotification($announcement));
        }

        return redirect()->back()->with('success', 'Announcement posted and notifications sent!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_grades' => 'nullable|array',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'target_grades' => $request->target_grades ? json_encode($request->target_grades) : null,
        ];

        if ($request->hasFile('attachment')) {
            if ($announcement->attachment && Storage::disk('public')->exists($announcement->attachment)) {
                Storage::disk('public')->delete($announcement->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement->update($data);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        if ($announcement->attachment && Storage::disk('public')->exists($announcement->attachment)) {
            Storage::disk('public')->delete($announcement->attachment);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('success', 'Announcement deleted successfully.');
    }
}

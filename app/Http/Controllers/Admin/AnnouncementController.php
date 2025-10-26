<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;

class AnnouncementController extends Controller
{
    public function create()
    {
        return view('admin.announcements.create');
    }

    public function index()
{
    $announcements = \App\Models\Announcement::latest()->get();
    return view('admin.announcements.index', compact('announcements'));
}


    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'attachment' => 'nullable|file|max:2048',
    ]);

    $attachmentPath = null;
    if ($request->hasFile('attachment')) {
        $attachmentPath = $request->file('attachment')->store('attachments', 'public');
    }

    // ✅ First, create the announcement
    $announcement = Announcement::create([
        'title' => $request->title,
        'content' => $request->content,
        'attachment' => $attachmentPath,
        'user_id' => auth()->id(),
    ]);

    // ✅ Then, notify all students
    $students = User::where('role', 'student')->get();

    foreach ($students as $student) {
        $student->notify(new NewAnnouncementNotification($announcement));
    }

    return redirect()->back()->with('success', 'Announcement posted and emails sent to students!');
}
public function destroy($id)
{
    $announcement = Announcement::findOrFail($id);
    $announcement->delete();

    return redirect()->route('admin.announcements.index')
        ->with('success', 'Announcement deleted successfully.');
}



    
}

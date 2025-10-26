<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement; // Assuming you have an Announcement model

class StudentAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'desc'); // default to 'desc'
        
        $announcements = Announcement::orderBy('created_at', $sort)->get();
        $announcements = Announcement::whereJsonContains('target_grade_levels', auth()->user()->grade_level)
        ->latest()
        ->get();


        return view('student.announcements', compact('announcements', 'sort'));
    }
}

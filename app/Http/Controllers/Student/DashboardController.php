<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->take(5)->get(); // Fetch latest 5 announcements
        return view('student.dashboard', compact('announcements'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $total = Enrollment::count();
        $approved = Enrollment::where('status', 'approved')->count();
        $pending = Enrollment::where('status', 'pending')->count();
        $rejected = Enrollment::where('status', 'rejected')->count();
        // $rejected1 = Enrollment::where('status', 'rejected')->with('user')->get();

        $totalStudents = User::where('role', 'student')->count();
        $totalProfessors = User::where('role', 'professor')->count();
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total',
            'approved',
            'pending',
            'rejected',
            // 'rejected1',
            'totalStudents',
            'totalProfessors',
            'announcements'
        ));
    }
    public function markResolved($gradeId)
{
    $grade = \App\Models\Grade::findOrFail($gradeId);

    $grade->status = 'submitted'; // or whatever status you want after resolving
    $grade->is_notified = true;   // mark as notified
    $grade->save();

    return redirect()->back()->with('success', 'Grade marked as resolved.');
}

}

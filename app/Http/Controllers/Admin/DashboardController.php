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

        return view('admin.dashboard', compact(
            'total',
            'approved',
            'pending',
            'rejected',
            // 'rejected1',
            'totalStudents',
            'totalProfessors'
        ));
    }
}

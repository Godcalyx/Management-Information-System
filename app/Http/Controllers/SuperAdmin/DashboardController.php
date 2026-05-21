<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
public function index()
{
    // Basic counts
    $totalEnrollments = \App\Models\Enrollment::count();
    $totalProfessors = \App\Models\User::where('role', 'professor')->count();
    $activeAdmin = \App\Models\User::where('role', 'admin')
                               ->where('status', 'active')
                               ->first();


    $totalStudents = User::where('role', 'student')->count();
    $lastBackup = '2026-01-10 18:00'; // Fetch from backups table or log
    $nextBackup = '2026-01-12 02:00'; // Scheduled
    // $auditLogs = \App\Models\AuditLog::latest()->take(5)->get();


    // SYSTEM HEALTH CHECKS
    $systemStatus = 'ok';
    $statusMessage = 'All systems operational';
    
    try {
        \DB::connection()->getPdo();
    } catch (\Exception $e) {
        $systemStatus = 'error';
        $statusMessage = 'Database connection failed';
    }

    return view('superadmin.dashboard', compact(
        'totalEnrollments', 
        'totalProfessors', 
        'totalStudents',
        'systemStatus',
        'statusMessage',
        'activeAdmin',
        'lastBackup',
        'nextBackup',
        // 'auditLogs'
    ));
}


}

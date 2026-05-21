<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminEnrollmentController extends Controller
{

    public function index(Request $request)
{
    $query = Enrollment::with('gradeLevel') // eager load gradeLevel
        ->where('status', 'pending'); // only pending

    if ($request->filled('search')) {
        $search = $request->input('search');

        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('lrn', 'like', "%{$search}%");
        });
    }

    $enrollments = $query->paginate(10);

    return view('admin.enrollments.index', compact('enrollments'));
}


public function exportPdf($id)
{
    $enrollment = Enrollment::with('gradeLevel')->findOrFail($id); // eager load gradeLevel

    File::ensureDirectoryExists(storage_path('fonts'));

    $pdf = Pdf::loadView('admin.enrollments._modal', compact('enrollment'))
        ->setPaper('a4', 'portrait');

    return $pdf->download("Enrollment_{$enrollment->last_name}.pdf");
}



    public function pending()
    {
        $pendingEnrollments = Enrollment::where('status', 'pending')->get();
        return view('admin.enrollments.pending', compact('pendingEnrollments'));
    }

    public function approve($id)
{
    $enrollment = Enrollment::findOrFail($id);

    // Avoid re-approving
    if ($enrollment->status !== 'pending') {
        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment already processed.');
    }

    // Check for duplicate email
    if (User::where('email', $enrollment->email)->exists()) {
        return redirect()->back()->withErrors(['email' => 'A user with this email already exists.']);
    }

    // Generate random temp password
    $tempPassword = Str::random(10);

    // Create student user
    $user = User::create([
        'name' => $enrollment->first_name . ' ' . $enrollment->last_name,
        'email' => $enrollment->email,
        'password' => Hash::make($tempPassword),
        'lrn' => $enrollment->lrn ?? Str::random(12),
        'role' => 'student',
        'grade_level' => $enrollment->grade_level,
    ]);

    // ✅ Link the enrollment to the new user and mark as approved
    $enrollment->user_id = $user->id;
    $enrollment->status = 'approved';
    $enrollment->save();

    // Send credentials via email
    try {
        Mail::to($user->email)->send(new \App\Mail\StudentCredentialsMail($user, $tempPassword));
    } catch (\Exception $e) {
        // You can log or ignore mail failures if needed
        \Log::error("Mail send failed: " . $e->getMessage());
    }

    return redirect()->route('admin.enrollments.index')
        ->with('success', 'Enrollment approved and credentials sent.');
}

    public function reject(Enrollment $enrollment)
    {
        $enrollment->update(['status' => 'rejected']);
        return back()->with('success', 'Enrollment rejected.');
    }
}

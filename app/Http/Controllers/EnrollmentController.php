<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use PDF; // at top


class EnrollmentController extends Controller
{
    public function showForm()
{
    return view('enroll');
}

public function submit(Request $request)
{
    $validatedData = $request->validate([
        'lrn' => 'nullable|digits:12|unique:enrollments,lrn',
        'email' => 'required|string|max:255',
        'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        'grade_level' => 'required|string',
        'last_name' => 'required|string|max:255',
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'extension_name' => 'nullable|string|max:255',
        'birthdate' => 'required|date',
        'birthplace' => 'required|string|max:255',
        'sex' => 'required|in:Male,Female',
        'mother_tongue' => 'nullable|string|max:255',
        'ip_community' => 'nullable|string|max:255',
        'ip_specify' => 'nullable|string|max:255',
        'is_4ps' => 'nullable|in:Yes,No',
        'household_id' => 'nullable|string|max:255',
        'current_house' => 'nullable|string|max:255',
        'current_street' => 'nullable|string|max:255',
        'current_barangay' => 'nullable|string|max:255',
        'current_city' => 'nullable|string|max:255',
        'current_province' => 'nullable|string|max:255',
        'current_country' => 'nullable|string|max:255',
        'current_zip' => 'nullable|string|max:4',
        'permanent_house' => 'nullable|string|max:255',
        'permanent_street' => 'nullable|string|max:255',
        'permanent_barangay' => 'nullable|string|max:255',
        'permanent_city' => 'nullable|string|max:255',
        'permanent_province' => 'nullable|string|max:255',
        'permanent_country' => 'nullable|string|max:255',
        'permanent_zip' => 'nullable|string|max:4',
        'father_last' => 'nullable|string|max:255',
        'father_first' => 'nullable|string|max:255',
        'father_middle' => 'nullable|string|max:255',
        'father_contact' => 'nullable|string|max:20',
        'mother_last' => 'nullable|string|max:255',
        'mother_first' => 'nullable|string|max:255',
        'mother_middle' => 'nullable|string|max:255',
        'mother_contact' => 'nullable|string|max:20',
        'guardian_last' => 'nullable|string|max:255',
        'guardian_first' => 'nullable|string|max:255',
        'guardian_middle' => 'nullable|string|max:255',
        'guardian_contact' => 'nullable|string|max:20',
        'modality' => 'nullable|array',
        'documents' => 'nullable|array',
    ]);

    // Set default enrollment status
    $validatedData['status'] = 'pending';

    // ✅ Assign user_id if a user exists with the given email
    $user = \App\Models\User::firstWhere('email', $validatedData['email']);
    if ($user) {
        $validatedData['user_id'] = $user->id;
    }

    Enrollment::create($validatedData);

    return redirect()->route('enroll.form')
                     ->with('success', 'Enrollment submitted. Please wait for approval.');
}


public function export($id)
{
    $enrollment = Enrollment::findOrFail($id);

    // Prepare data for PDF
    $pdf = PDF::loadView('admin.enrollments.export', compact('enrollment'));

    // Download PDF named by student LRN or name
    return $pdf->download('Enrollment_'.$enrollment->lrn.'.pdf');
}
public function archive()
{
    $approved = Enrollment::where('status', 'approved')->latest()->paginate(10);
    $rejected = Enrollment::where('status', 'rejected')->latest()->paginate(10);

    return view('admin.enrollments.archive', compact('approved', 'rejected'));
}





}
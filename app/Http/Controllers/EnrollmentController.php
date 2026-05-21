<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use PDF; // at top
use App\Models\GradeLevel;
use Illuminate\Support\Facades\File;


class EnrollmentController extends Controller
{
    public function showForm()
{
     $gradeLevels = GradeLevel::all(); // fetch all grade levels

    return view('enroll', compact('gradeLevels'));
}

public function submit(Request $request)
{
    $validatedData = $request->validate([
        'lrn' => 'nullable|digits:12|unique:enrollments,lrn',
        'email' => 'required|string|max:255',
        'school_year' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/'],
        // 'grade_level' => 'required|string',
        'grade_level_id' => 'required|exists:grade_levels,id',
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
        // Remove 'documents' from validation here—handle files separately below
    ]);

    // Handle file uploads and build the documents array
    $documents = [];
    $docFields = ['report_card', 'good_moral', 'birth_cert', 'id_picture'];
    
    foreach ($docFields as $field) {
        if ($request->hasFile("documents.{$field}")) {
            $file = $request->file("documents.{$field}");
            // Validate file type and size (additional check beyond form)
            $request->validate([
                "documents.{$field}" => 'file|mimes:pdf,jpg,png|max:5120',  // 5MB max, adjust types as needed
            ]);
            // Store file in storage/app/public/uploads/ and get the path
            $path = $file->store('uploads', 'public');
            $documents[$field] = [$path];  // Store as array for consistency
        } else {
            $documents[$field] = [];  // Empty array if no file
        }
    }

    // Set default enrollment status and add documents
    $validatedData['status'] = 'pending';
    $validatedData['documents'] = json_encode($documents);  // Store as JSON string

    Enrollment::create($validatedData);

    return redirect()->route('enroll.form')
                     ->with('success', 'Enrollment submitted. Please wait for approval.');
}



public function export($id)
{
    $enrollment = Enrollment::with('gradeLevel')->findOrFail($id);

    // DomPDF expects its font cache directory to exist before rendering.
    File::ensureDirectoryExists(storage_path('fonts'));

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

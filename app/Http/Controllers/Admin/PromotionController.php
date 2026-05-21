<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Grade;
use Carbon\Carbon;

class PromotionController extends Controller
{
    /**
     * Show the promotion management page for a given school year.
     */
    public function viewEvaluation($schoolYear = null)
    {
        $schoolYear ??= Enrollment::latest('school_year')->value('school_year');

        $students = Enrollment::where('school_year', $schoolYear)->get();
        $totalStudents = $students->count();

        // Students with all grades approved
        $evaluatedCount = $students->filter(fn($s) => $s->grades()->where('status','approved')->count() > 0)->count();

        // Students who still have grades pending approval
        $pendingCount = $totalStudents - $evaluatedCount;

        // Students flagged for manual review (optional: failing grades)
        $studentsForReview = $students->where('promotion_status', 'for_review');

        return view('admin.promotion.manage', compact(
            'schoolYear',
            'totalStudents',
            'evaluatedCount',
            'pendingCount',
            'studentsForReview'
        ));
    }

    /**
     * Run promotion evaluation for all students in the current school year.
     */
    public function evaluate(Request $request, $schoolYear = null)
    {
        $schoolYear ??= Enrollment::latest('school_year')->value('school_year');

        $students = Enrollment::where('school_year', $schoolYear)->get();

        foreach ($students as $student) {
            // Only consider students with all approved grades
            $grades = $student->grades()->where('status','approved')->get();

            if ($grades->count() === 0) {
                // Skip students with no approved grades
                continue;
            }

            // Optional: flag failing students for manual review
            $hasFailing = $grades->contains(fn($g) => $g->grade < 75);
            $student->promotion_status = $hasFailing ? 'for_review' : 'promoted';

            $student->promotion_evaluated_at = now();
            $student->save();

            // Auto-promote if eligible
            if ($student->promotion_status === 'promoted') {
                $this->createNextEnrollment($student);
            }
        }

        // Return students requiring manual review
        $studentsForReview = Enrollment::where('school_year', $schoolYear)
            ->where('promotion_status', 'for_review')
            ->get();

        return response()->json([
            'success' => true,
            'students_for_review' => $studentsForReview
        ]);
    }

    /**
     * Bulk update promotion statuses (manual review).
     */
    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'statuses' => 'required|array',
            'statuses.*.id' => 'required|exists:enrollments,id',
            'statuses.*.promotion_status' => 'required|in:promoted,retained',
        ]);

        foreach ($data['statuses'] as $item) {
            $enrollment = Enrollment::find($item['id']);
            $enrollment->promotion_status = $item['promotion_status'];
            $enrollment->promotion_evaluated_at = now();
            $enrollment->save();

            if ($item['promotion_status'] === 'promoted') {
                $this->createNextEnrollment($enrollment);
            }
        }

        return redirect()->back()->with('success', 'Promotion statuses updated successfully.');
    }

    /**
     * Create next year enrollment for promoted students.
     */
   private function createNextEnrollment($currentEnrollment)
{
    // mark old enrollment as not current
    $currentEnrollment->is_current = 0;
    $currentEnrollment->save();

    [$start, $end] = explode('-', $currentEnrollment->school_year);
    $nextSY = ($start + 1) . '-' . ($end + 1);
    $nextGrade = $currentEnrollment->grade_level + 1;

    $exists = Enrollment::where('user_id', $currentEnrollment->user_id)
                        ->where('school_year', $nextSY)
                        ->exists();

    if (!$exists && $currentEnrollment->grade_level < 10) {
        Enrollment::create([
            'user_id' => $currentEnrollment->user_id,
            'lrn' => $currentEnrollment->lrn,
            'email' => $currentEnrollment->email,
            'school_year' => $nextSY,
            'grade_level' => $nextGrade,
            'last_name' => $currentEnrollment->last_name,
            'first_name' => $currentEnrollment->first_name,
            'middle_name' => $currentEnrollment->middle_name,
            'extension_name' => $currentEnrollment->extension_name,
            'birthdate' => $currentEnrollment->birthdate,
            'birthplace' => $currentEnrollment->birthplace,
            'sex' => $currentEnrollment->sex,
            'modality' => $currentEnrollment->modality,
            'status' => 'approved',
            'promotion_status' => 'pending',
            'completion_status' => null,
            'gpa' => null,
            'weighted_avg_msr' => null,
            'has_failing_grade' => 0,
            'is_current' => 1, // new enrollment is now current
        ]);
    }
}

}

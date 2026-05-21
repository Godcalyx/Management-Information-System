<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\GradeLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GradeSummaryController extends Controller
{
    public function index(Request $request)
    {
        // Inputs
        $gradeLevelId = $request->input('grade_level_id');
        $subjectId = $request->input('subject_id');

        // =========================
        // QUARTER FILTER (UPDATED)
        // =========================
        $quartersInput = $request->input('quarters');

        if (is_string($quartersInput)) {
            $quartersInput = array_filter(explode(',', $quartersInput));
        }

        if (!is_array($quartersInput) || empty($quartersInput)) {
            // NONE selected = "All"
            $selectedQuarters = [];
        } else {
            $selectedQuarters = array_values(array_unique(
                array_filter(array_map('intval', $quartersInput), function ($q) {
                    return in_array($q, [1,2,3,4]);
                })
            ));
        }

        // Used for averages logic
        $selectedQuarter = count($selectedQuarters) === 1
            ? $selectedQuarters[0]
            : 'All';

        // Quarters actually used for computations
        $quartersForQuery = empty($selectedQuarters)
            ? [1,2,3,4]
            : $selectedQuarters;

        // Fetch grade levels
        $gradeLevels = GradeLevel::all();
        $gradeLevelNames = $gradeLevels->pluck('name', 'id')->toArray();

        // =========================
        // NO GRADE LEVEL SELECTED
        // =========================
        if (!$gradeLevelId) {
            return view('Admin.grades.summary', [
                'gradeLevels' => $gradeLevels,
                'selectedGradeLevel' => null,
                'enrollments' => collect(),
                'subjects' => collect(),
                'gradesData' => [],
                'gradeLevelNames' => $gradeLevelNames,
                'selectedQuarters' => $selectedQuarters,
                'selectedSubjectId' => $subjectId,
                'subjectAverages' => [],
                'gradeLevelAverage' => null,
            ]);
        }

        // =========================
        // LATEST ENROLLMENTS
        // =========================
        $latestEnrollmentsQuery = DB::table('enrollments as e1')
            ->select('e1.*')
            ->where('e1.status', 'approved')
            ->where('e1.grade_level_id', $gradeLevelId)
            ->where(function ($q) {
    $q->whereNull('e1.completion_status')
      ->orWhere('e1.completion_status', '!=', 'graduated');
})

            ->whereRaw('e1.id = (
                SELECT MAX(e2.id)
                FROM enrollments e2
                WHERE e2.user_id = e1.user_id
            )');

        $enrollments = $latestEnrollmentsQuery->get();

        // Map LRN → user_id
        $lrns = $enrollments->pluck('lrn')->filter();
        $userMap = DB::table('users')
            ->whereIn('lrn', $lrns)
            ->pluck('id', 'lrn');

        // Add user_id + age
        $enrollments = $enrollments->map(function ($s) use ($userMap) {
            $s->user_id = $userMap[$s->lrn] ?? null;
            $s->age = $s->birthdate ? Carbon::parse($s->birthdate)->age : null;
            return $s;
        })->filter(fn($s) => !empty($s->user_id))->values();

        // =========================
        // SUBJECTS
        // =========================
        $subjectsQuery = Subject::where('grade_level_id', $gradeLevelId)
            ->orderBy('order');

        if ($subjectId) {
            $subjectsQuery->where('id', $subjectId);
        }

        $subjects = $subjectsQuery->get();

        if ($subjects->isEmpty() || $enrollments->isEmpty()) {
            return view('Admin.grades.summary', [
                'gradeLevels' => $gradeLevels,
                'selectedGradeLevel' => $gradeLevelId,
                'enrollments' => $enrollments,
                'subjects' => $subjects,
                'gradesData' => [],
                'gradeLevelNames' => $gradeLevelNames,
                'selectedQuarters' => $selectedQuarters,
                'selectedSubjectId' => $subjectId,
                'subjectAverages' => [],
                'gradeLevelAverage' => null,
            ]);
        }

        // =========================
        // SCHOOL YEAR MAP
        // =========================
        $userSchoolYearMap = [];
        foreach ($enrollments as $e) {
            $userSchoolYearMap[$e->user_id] = $e->school_year;
        }

        // =========================
        // GRADES QUERY (UPDATED)
        // =========================
        $gradesQuery = Grade::whereIn('user_id', $enrollments->pluck('user_id'))
            ->whereIn('subject_id', $subjects->pluck('id'));

        if (!empty($selectedQuarters)) {
            $gradesQuery->whereIn('quarter', $selectedQuarters);
        }

        $grades = $gradesQuery->get();

        // =========================
        // BUILD gradesData
        // =========================
        $gradesData = [];

        foreach ($grades as $g) {

            if ($userSchoolYearMap[$g->user_id] !== $g->school_year) {
                continue;
            }

            $quarterInt = (int) $g->quarter;

            if (!empty($selectedQuarters) &&
                !in_array($quarterInt, $selectedQuarters)) {
                continue;
            }

            $gradesData[$g->user_id][$g->subject_id][$quarterInt] = $g->grade;
        }

        // =========================
        // SUBJECT + GRADE LEVEL AVERAGES
        // =========================
        $subjectAverages = [];
        $overallAverageTotal = 0;
        $overallAverageCount = 0;

        foreach ($subjects as $subject) {
            $sum = 0;
            $count = 0;

            foreach ($enrollments as $student) {
                $userGrades = $gradesData[$student->user_id][$subject->id] ?? [];

                foreach ($quartersForQuery as $q) {
                    if (isset($userGrades[$q])) {
                        $sum += $userGrades[$q];
                        $count++;
                    }
                }
            }

            $subjectAverages[$subject->id] =
                $count ? ceil($sum / $count) : null;

            if ($count) {
                $overallAverageTotal += $subjectAverages[$subject->id];
                $overallAverageCount++;
            }
        }

        $gradeLevelAverage = $overallAverageCount
            ? ceil($overallAverageTotal / $overallAverageCount)
            : null;

        // =========================
        // RETURN VIEW
        // =========================
        return view('Admin.grades.summary', [
            'gradeLevels' => $gradeLevels,
            'selectedGradeLevel' => $gradeLevelId,
            'enrollments' => $enrollments,
            'subjects' => $subjects,
            'gradesData' => $gradesData,
            'gradeLevelNames' => $gradeLevelNames,
            'selectedQuarters' => $selectedQuarters,
            'selectedSubjectId' => $subjectId,
            'subjectAverages' => $subjectAverages,
            'gradeLevelAverage' => $gradeLevelAverage,
        ]);
    }
}

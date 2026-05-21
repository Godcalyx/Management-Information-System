<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        // Base query for alumni
        $baseQuery = Enrollment::where('grade_level_id', 4)
            ->where('completion_status', 'graduated')
            ->where('promotion_status', 'completed');

        // Filter: School Year
        if ($request->filled('school_year')) {
            $baseQuery->where('school_year', $request->school_year);
        }

        // Clone for stats before pagination
        $statsQuery = (clone $baseQuery);

        // Fetch all matching alumni
        $allAlumni = $statsQuery->get();

        // === FIXED MALE / FEMALE COUNT (case-insensitive) ===
        $maleCount = $allAlumni->filter(function ($item) {
            return strtolower($item->sex) === 'male';
        })->count();

        $femaleCount = $allAlumni->filter(function ($item) {
            return strtolower($item->sex) === 'female';
        })->count();

        // Total alumni
        $totalAlumni = $allAlumni->count();

        // Average Age
        $avgAge = null;
        if ($totalAlumni > 0) {
            $ages = $allAlumni->map(function ($e) {
                return $e->birthdate ? Carbon::parse($e->birthdate)->age : null;
            })->filter();
            $avgAge = $ages->count() ? round($ages->avg()) : null;
        }

        // Group by School Year
        $bySchoolYear = $allAlumni
            ->groupBy('school_year')
            ->map(fn($group) => $group->count())
            ->toArray();

        // Stats array for UI
        $stats = [
            'total' => $totalAlumni,
            'male' => $maleCount,
            'female' => $femaleCount,
            'avg_age' => $avgAge,
            'by_school_year' => $bySchool_year ?? [],
        ];

        // Paginated list
        $alumni = (clone $baseQuery)
            ->with('user')
            ->orderByDesc('completed_at')
            ->paginate(25)
            ->withQueryString();

        // Add calculated age to results
        $alumni->getCollection()->transform(function ($record) {
            $record->age = $record->birthdate
                ? Carbon::parse($record->birthdate)->age
                : '—';
            return $record;
        });

        // School year dropdown
        $schoolYears = Enrollment::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->pluck('school_year');

        return view('admin.alumni.index', compact('alumni', 'schoolYears', 'stats'));
    }
}

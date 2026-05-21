@extends('layouts.faculty')

@section('title', 'Teacher Dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center mb-4">
    {{-- <img src="{{ asset('images/logo.jpg') }}" alt="CvSU Logo" class="img-fluid" style="max-height: 70px;"> --}}
    <h2 class="fw-bold text-success mb-0">Teacher Dashboard</h2>
</div>
@endsection

@section('content')
<div class="row g-4">

    {{-- ================= MAIN COLUMN ================= --}}
    <div class="col-lg-9">

        {{-- 🔹 OVERVIEW CARDS --}}
        <div class="row g-4">

            {{-- Total Students --}}
            <div class="col-md-3">
                <div class="card dashboard-card h-100 text-center"
                     data-bs-toggle="modal" data-bs-target="#studentsModal" style="cursor:pointer;">
                    <div class="card-body">
                        <i class="bi bi-people-fill fs-2 text-success"></i>
                        <h6 class="mt-2 fw-bold">Students</h6>
                        <h3 class="text-success">{{ $totalStudents }}</h3>
                    </div>
                </div>
            </div>
            

            {{-- Subjects --}}
            <div class="col-md-3">
                <div class="card dashboard-card h-100 text-center"
                     data-bs-toggle="modal" data-bs-target="#subjectsModal" style="cursor:pointer;">
                    <div class="card-body">
                        <i class="bi bi-journal-bookmark-fill fs-2 text-primary"></i>
                        <h6 class="mt-2 fw-bold">Subjects</h6>
                        <h3 class="text-primary">{{ $totalSubjects }}</h3>
                    </div>
                </div>
            </div>

            {{-- Advisory --}}
            @if($isAdviser)
            <div class="col-md-3">
                <div class="card dashboard-card h-100 text-center"
                     onclick="window.location.href='{{ route('teacher.advisory') }}'" style="cursor:pointer;">
                    <div class="card-body">
                        <i class="bi bi-person-badge-fill fs-2 text-info"></i>
                        <h6 class="mt-2 fw-bold">Advisory</h6>
                        <h3 class="text-info">{{ count($advisoryStudents) }}</h3>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submitted Grades --}}
            <div class="col-md-3">
                <div class="card dashboard-card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-check-fill fs-2 text-warning"></i>
                        <h6 class="mt-2 fw-bold">Submitted</h6>
                        <h3 class="text-warning">{{ $submittedGradesCount }}</h3>
                    </div>
                </div>
            </div>

        </div>

        {{-- 🔹 RECENT ACTIVITY --}}
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Activity</h5>

                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#recent">
                            Recent Grades
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link position-relative" data-bs-toggle="tab" data-bs-target="#returned">
                            Returned
                            @if($returnedGradesCount > 0)
                                <span class="badge bg-danger ms-1">{{ $returnedGradesCount }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- Recent --}}
                    <div class="tab-pane fade show active" id="recent">
                        @forelse($recentGrades as $entry)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $entry->student_name }} — {{ $entry->subject_name }}</span>
                                <span class="text-muted">{{ $entry->grade }}</span>
                            </div>
                        @empty
                            <p class="text-muted text-center">No recent activity</p>
                        @endforelse
                    </div>

                    {{-- Returned --}}
                    <div class="tab-pane fade" id="returned">
                        @forelse($returnedGrades as $grade)
                            <div class="border-bottom py-2">
                                <strong class="text-danger">Returned Grade</strong><br>
                                {{ $grade->user->name }} — {{ $grade->subject->name }}
                            </div>
                        @empty
                            <p class="text-muted text-center">No returned grades</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ================= SIDEBAR ================= --}}
    <div class="col-lg-3">

        {{-- 🧠 AI INSIGHTS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold"><i class="bi bi-cpu me-2"></i>AI Insights</h5>

                <ul class="list-group list-group-flush mt-3">

                    <li class="list-group-item">
                        ⚠️ <strong>{{ $aiInsights['atRiskCount'] ?? 0 }}</strong> at-risk students
                    </li>

                    <li class="list-group-item">
                        📉 <strong>{{ $aiInsights['decliningCount'] ?? 0 }}</strong> declining trends
                    </li>

                    <li class="list-group-item">
                        🏅 <strong>{{ $aiInsights['honorForecastCount'] ?? 0 }}</strong> honor candidates
                    </li>

                    <li class="list-group-item">
                        📊 Lowest Avg: <strong>{{ $aiInsights['lowestSubject'] ?? 'N/A' }}</strong>
                        ({{ $aiInsights['lowestAvg'] ?? 0 }}%)
                    </li>

                </ul>
            </div>
        </div>

        {{-- 🏆 HONOR STUDENTS --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between">
                <h5 class="fw-bold"><i class="bi bi-trophy-fill me-2"></i>Honor Students</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#honorModal">
                    View
                </button>
            </div>

            <ul class="list-group list-group-flush">
                @forelse ($honorStudents->take(5) as $student)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $student->name }}</span>
                        <span class="badge bg-success">{{ $student->overall_average }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center">No honor students</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>

{{-- 🏆 HONOR STUDENTS MODAL --}}
<div class="modal fade" id="honorModal" tabindex="-1" aria-labelledby="honorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="honorModalLabel">
                    <i class="bi bi-trophy-fill me-2 text-warning"></i>
                    Honor Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                @forelse ($honorStudents as $student)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <strong>{{ $student->name }}</strong><br>
                            <small class="text-muted">LRN: {{ $student->lrn ?? 'N/A' }}</small>
                        </div>
                        <span class="badge bg-success fs-6">
                            {{ number_format($student->overall_average, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted text-center">No honor students found.</p>
                @endforelse

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.dashboard-card {
    border-left: 5px solid #198754;
    transition: 0.3s;
}
.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}
</style>
@endpush

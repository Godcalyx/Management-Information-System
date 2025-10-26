@extends('layouts.faculty')

@section('title', 'Teacher Dashboard')

@section('header')
<div class="d-flex justify-content-between align-items-center mb-4">
    <img src="{{ asset('images/logo.jpg') }}" alt="CvSU Logo" class="img-fluid" style="max-height: 80px;">
    <h2 class="mb-0 fw-bold text-success">Welcome to Teacher Portal!</h2>
</div>
@endsection

@section('content')
<div class="row g-4">

    <!-- Main Dashboard Column -->
    <div class="col-lg-9">

        <!-- Overview Cards -->
        <div class="row g-4">
            <div class="col-md-4">
    <div class="card shadow-sm border-0 dashboard-card h-100"
         style="cursor: pointer;"
         data-bs-toggle="modal"
         data-bs-target="#studentsModal">
        <div class="card-body text-center">
            <h5 class="card-title fw-bold"><i class="bi bi-people-fill me-2"></i>Total Students</h5>
            <p class="fs-3 text-success mb-0">{{ $totalStudents }}</p>
            <small class="text-muted">Click to view list</small>
        </div>
    </div>
</div>


            {{-- CLICKABLE SUBJECT CARD --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 dashboard-card h-100" 
                     style="cursor: pointer;" 
                     data-bs-toggle="modal" 
                     data-bs-target="#subjectsModal">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><i class="bi bi-journal-bookmark-fill me-2"></i>Subjects Handled</h5>
                        <p class="fs-3 text-primary mb-0">{{ $totalSubjects }}</p>
                        <small class="text-muted">Click to view list</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm border-0 dashboard-card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><i class="bi bi-file-earmark-check-fill me-2"></i>Grades Submitted</h5>
                        <p class="fs-3 text-warning mb-0">{{ $submittedGradesCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Grade Activity -->
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Grade Entries</h5>
                <ul class="list-group list-group-flush">
                    @forelse ($recentGrades as $entry)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $entry->student_name }} - {{ $entry->subject_name }}</span>
                            <span class="text-muted">{{ $entry->grade }} ({{ ucfirst($entry->quarter) }})</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No recent grade activity.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- <!-- Grade Averages Chart -->
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title fw-bold"><i class="bi bi-bar-chart-fill me-2"></i>Average Grades by Subject</h5>
                @if(count($gradeLabels))
                    <canvas id="gradeChart" style="min-height: 250px;"></canvas>
                @else
                    <p class="text-muted text-center mb-0">No grade data available to display.</p>
                @endif
            </div>
        </div> --}}

    </div>

    <!-- Sidebar Column -->
    <div class="col-lg-3">

        <!-- Quick Grade Entry -->
        <div class="card shadow-sm border-0 mb-4 text-center">
            <div class="card-body">
                <h5 class="card-title fw-bold"><i class="bi bi-plus-square me-2"></i>Quick Grade Entry</h5>
                <a href="{{ route('grades.consolidation') }}" class="btn btn-outline-success btn-sm mt-2 w-100">
                    Enter Grades
                </a>
            </div>
        </div>

        <!-- Honor Students -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold"><i class="bi bi-trophy-fill me-2"></i>Honor Students</h5>
                <ul class="list-group list-group-flush">
                    @forelse ($honorStudents as $student)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $student->name }}</strong><br>
                                <small class="text-muted">Grade {{ $student->grade_level }} — {{ $student->honor }}</small>
                            </div>
                            <span class="badge bg-warning text-dark">{{ $student->average }}%</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No honor students yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ✅ Subjects Handled Modal -->
<div class="modal fade" id="subjectsModal" tabindex="-1" aria-labelledby="subjectsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="subjectsModalLabel"><i class="bi bi-journal-bookmark-fill me-2"></i>Subjects You Handle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(count($subjectsHandled ?? []) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($subjectsHandled as $subject)
    @php
        $colorClass = match($subject->grade_level) {
            7 => 'bg-success',   // Green
            8 => 'bg-warning text-dark', // Yellow
            9 => 'bg-danger',    // Red
            10 => 'bg-primary',  // Blue
            default => 'bg-secondary', // Fallback
        };
    @endphp
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>{{ $subject->subject_name }}</span>
        <span class="badge {{ $colorClass }}">Grade {{ $subject->grade_level }}</span>
    </li>
@endforeach

                    </ul>
                @else
                    <p class="text-muted text-center mb-0">No subjects assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<!-- ✅ Students Per Grade Modal -->
<div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="studentsModalLabel">
                    <i class="bi bi-people-fill me-2"></i>Students You Teach
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                @if(isset($studentsPerGrade) && count($studentsPerGrade) > 0)
                    @foreach($studentsPerGrade as $gradeLevel => $students)
                        @php
                            $colorClass = match($gradeLevel) {
                                7 => 'bg-success text-white',
                                8 => 'bg-warning text-dark',
                                9 => 'bg-danger text-white',
                                10 => 'bg-primary text-white',
                                default => 'bg-secondary text-white',
                            };
                        @endphp
                        <h6 class="fw-bold mt-3 p-2 rounded {{ $colorClass }}">Grade {{ $gradeLevel }}</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($students as $student)
                                <li class="list-group-item">{{ $student->name }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                @else
                    <p class="text-muted text-center mb-0">No students found.</p>
                @endif
            </div>
        </div>
    </div>
</div>


{{-- @section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('gradeChart')?.getContext('2d');
    if (ctx && {!! count($gradeLabels) !!} > 0) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($gradeLabels) !!},
                datasets: [{
                    label: 'Average Grade',
                    data: {!! json_encode($gradeAverages) !!},
                    backgroundColor: '#198754',
                    borderRadius: 5,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        min: 70,
                        max: 100,
                        ticks: { stepSize: 5 }
                    },
                    x: {
                        ticks: { font: { size: 14 } }
                    }
                }
            }
        });
    }
</script>
@endsection --}}

@push('styles')
<style>
    .dashboard-card {
        border-left: 6px solid #14532d;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2, h5 {
        color: #14532d;
    }

    .btn-outline-success, .btn-outline-primary, .btn-outline-dark {
        font-weight: 500;
    }

    .list-group-item {
        font-size: 0.95rem;
    }
</style>
@endpush

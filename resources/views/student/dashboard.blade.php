@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="row g-4">

    <!-- Grade Analytics Card -->
    {{-- <div class="col-md-6 col-xl-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow text-success fs-1"></i>
                <h5 class="mt-3 fw-bold">Grade Analytics</h5>
                <p class="text-muted mb-3">Track your grade trends and subject progress.</p>
                <a href="{{ route('student.grade-analytics') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-bar-chart-line me-1"></i> Analyze Now
                </a>
            </div>
        </div>
    </div> --}}

    <!-- Academic Standing / Honors Card -->
    <div class="col-md-6 col-xl-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card-body text-center">
                <i class="bi bi-award-fill text-warning fs-1"></i>
                <h5 class="mt-3 fw-bold">Academic Standing</h5>
                <p class="text-muted mb-3">Check if you made it to the honors list!</p>
                <a href="{{ route('student.academic-standing') }}" class="btn btn-warning btn-sm text-dark">
                    <i class="bi bi-trophy me-1"></i> View Ranking
                </a>
            </div>
        </div>
    </div>

    <!-- Downloadable Forms Card -->
    <div class="col-md-6 col-xl-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card-body text-center">
                <i class="bi bi-file-earmark-arrow-down-fill text-primary fs-1"></i>
                <h5 class="mt-3 fw-bold">Report Forms</h5>
                <p class="text-muted mb-3">Access forms and school files.</p>
                <a href="{{ route('student.reportforms') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-download me-1"></i> View
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

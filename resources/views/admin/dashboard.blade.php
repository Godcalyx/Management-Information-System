@extends('layouts.admin') 

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-success fw-bold">Admin Dashboard</h2>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Total Enrollments</h5>
                    <p class="card-text fs-4">{{ $total }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Approved</h5>
                    <p class="card-text fs-4">{{ $approved }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text fs-4">{{ $pending }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Rejected</h5>
                    <p class="card-text fs-4">{{ $rejected }}</p>
                </div>
            </div>
        </div>

        {{-- Total Students Enrolled card commented out
        <div class="col-md-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Total Students Enrolled</h5>
                    <p class="card-text fs-4">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
        --}}

        <div class="col-md-3">
            <div class="card text-white bg-dark h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title">Total Professors</h5>
                    <p class="card-text fs-4">{{ $totalProfessors }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional: Quick Actions -->
    <div class="mt-5">
        <h4 class="mb-3">🔧 Quick Actions</h4>
        <div class="d-flex flex-wrap gap-3">
            {{-- <a href="{{ route('enrollments.archive') }}" class="btn btn-outline-success"><i class="bi bi-book me-1"></i> Archive</a> --}}
            <a href="#" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Download Reports</a>
        </div>
    </div>
</div>
@endsection

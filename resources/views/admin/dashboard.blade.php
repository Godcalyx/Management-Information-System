@extends('layouts.admin') 

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-success fw-bold">Admin Dashboard</h2>

    <div class="row g-3">
        <!-- Your existing cards -->
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
            <a href="{{ route('admin.forms.index') }}" class="btn btn-outline-primary"><i class="bi bi-download me-1"></i> Downloadable Forms</a>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-3">
            <a href="{{ route('admin.alumni.index') }}" class="btn btn-outline-primary"><i class="bi bi-people me-1"></i> Alumni History</a>        
        </div>    
        
    </div>
</div>

<!-- Announcement Panel (Bottom-Right, Collapsible) -->
<div class="position-fixed bottom-0 end-0 m-3" style="width: 300px; z-index: 1050;">
    <div class="card shadow-sm">
        <!-- Collapsible Header -->
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center" 
             data-bs-toggle="collapse" data-bs-target="#announcementPanel" style="cursor: pointer;">
            <i class="bi bi-megaphone me-2"></i> ANNOUNCEMENTS
            <span class="badge bg-light text-dark">{{ $announcements->count() }}</span>
        </div>

        <!-- Collapsible Body -->
        <div class="collapse" id="announcementPanel">
            <ul class="list-group list-group-flush">
                @forelse($announcements as $announcement)
                    <li class="list-group-item">
                        <strong>{{ $announcement->title }}</strong><br>
                        <small>{{ \Illuminate\Support\Str::limit($announcement->content, 50) }}</small>
                        {{-- <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small> --}}
                    </li>
                @empty
                    <li class="list-group-item text-muted">No announcements yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@endsection

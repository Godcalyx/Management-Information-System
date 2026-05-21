@extends('layouts.superadmin')

@section('content')

<style>
    .dashboard-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border-radius: 12px;
    }
    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .section-title {
        font-weight: 700;
        letter-spacing: 0.4px;
    }
</style>

<div class="container py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-warning fw-bold mb-0">
            <i class="bi bi-shield-lock-fill me-2"></i> Super Admin Dashboard
        </h1>
        <span class="badge bg-dark px-3 py-2">
            {{ now()->format('F d, Y') }}
        </span>
    </div>

    <!-- ===================== -->
    <!-- TOP ROW : METRICS -->
    <!-- ===================== -->
    <div class="row g-3 mb-4">

        <!-- System Health -->
        <div class="col-md-3">
            <div class="card dashboard-card h-100 text-center
                @if($systemStatus === 'ok') bg-success text-white
                @elseif($systemStatus === 'warning') bg-warning text-dark
                @else bg-danger text-white @endif">
                <div class="card-body">
                    <i class="bi bi-heart-pulse fs-2 mb-2"></i>
                    <h6 class="text-uppercase">System Health</h6>
                    <p class="fw-semibold mb-0">{{ $statusMessage }}</p>
                </div>
            </div>
        </div>

        <!-- Active Admin -->
        <div class="col-md-3">
            <div class="card dashboard-card h-100 text-center bg-secondary text-white">
                <div class="card-body">
                    <i class="bi bi-person-check fs-2 mb-2"></i>
                    <h6 class="text-uppercase">Active Admin</h6>

                    @if($activeAdmin)
                        <p class="fw-semibold mb-0">{{ $activeAdmin->name }}</p>
                        <small>{{ $activeAdmin->email }}</small><br>
                        <span class="badge bg-success mt-2">Active</span>
                    @else
                        <p class="mb-1">No active admin</p>
                        <span class="badge bg-warning text-dark">Inactive</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Enrollments -->
        <div class="col-md-3">
            <div class="card dashboard-card bg-primary text-white h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-journal-bookmark fs-2 mb-2"></i>
                    <h6>Total Enrollments</h6>
                    <p class="fs-4 fw-bold mb-0">{{ $totalEnrollments }}</p>
                </div>
            </div>
        </div>

        <!-- Total Professors -->
        <div class="col-md-3">
            <div class="card dashboard-card bg-dark text-white h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-person-video3 fs-2 mb-2"></i>
                    <h6>Total Professors</h6>
                    <p class="fs-4 fw-bold mb-0">{{ $totalProfessors }}</p>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="col-md-3">
            <div class="card dashboard-card bg-info text-white h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-people-fill fs-2 mb-2"></i>
                    <h6>Total Students</h6>
                    <p class="fs-4 fw-bold mb-0">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== -->
    <!-- SYSTEM CONTROLS -->
    <!-- ===================== -->
    <h5 class="section-title text-muted mb-3">System Controls</h5>

    <div class="row g-3 mb-4">

        <!-- Admin Management -->
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-danger">
                        <i class="bi bi-people-fill me-1"></i> Admin Management
                    </h6>
                    <p class="text-muted small">
                        Activate or deactivate administrator accounts.
                    </p>
                    <a href="{{ route('admins.index') }}" class="btn btn-sm btn-primary">
                        Manage Admins
                    </a>
                </div>
            </div>
        </div>

        <!-- Backup -->
        <div class="col-md-6">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <h6 class="fw-bold text-warning">
                        <i class="bi bi-database-check me-1"></i> Backup & Recovery
                    </h6>

                    <p class="small mb-1">
                        Last Backup: <strong>{{ $lastBackup ?? 'N/A' }}</strong>
                    </p>
                    <p class="small">
                        Next Backup: <strong>{{ $nextBackup ?? 'N/A' }}</strong>
                    </p>

                    <form action="{{ route('super_admin.backup.create') }}" method="POST">
                        @csrf
                        <button class="btn btn-sm btn-success">
                            Create Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- ===================== -->
    <!-- AUDIT LOGS -->
    <!-- ===================== -->
    <h5 class="section-title text-muted mb-3">Audit & Activity Logs</h5>

    <div class="row g-3">
        <div class="col-md-12">
            <div class="card dashboard-card h-100">
                <div class="card-body">

                    <p class="text-muted small mb-3">
                        Recent administrative actions performed in the system.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- 
                                @forelse($auditLogs as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $log->user_name }}</td>
                                        <td>{{ $log->action }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">No recent activity</td>
                                    </tr>
                                @endforelse
                                --}}
                                <tr>
                                    <td colspan="4" class="text-muted">
                                        No audit logs available
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <a href="{{ route('super_admin.audit.index') }}" class="btn btn-sm btn-secondary mt-2">
                        View All Logs
                    </a>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.admin')

@section('title', 'Audit Trail Logs')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Audit Trail Logs</h2>

    {{-- Legend --}}
    <div class="mb-3">
        <span class="me-3"><span class="legend-dot bg-danger"></span> Admin</span>
        <span class="me-3"><span class="legend-dot bg-primary"></span> Teacher</span>
        <span class="me-3"><span class="legend-dot bg-success"></span> Student</span>
        <span class="me-3"><span class="legend-dot bg-info"></span> Superadmin</span>
    </div>

    <style>
        .legend-dot { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 5px; }
        .stats-card { font-size: 0.85rem; }
    </style>

    {{-- Stats summary --}}
    <div class="row mb-3">
        @foreach ([
            'Total Logs' => $logs->total(),
            'Unique Users' => $logs->pluck('user_id')->unique()->count(),
            'Recent Action' => optional($logs->first())->action ?? '-',
            'Last Log Time' => optional($logs->first())->created_at?->format('Y-m-d H:i') ?? '-'
        ] as $label => $value)
        <div class="col-md-3 mb-2">
            <div class="card shadow-sm border-start border-3 border-primary stats-card">
                <div class="card-body py-2 px-3">
                    <h6 class="text-muted mb-1">{{ $label }}</h6>
                    <h5 class="fw-bold mb-0">{{ $value }}</h5>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.audit-trails.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search action" value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">-- Filter by User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Filter by Role --</option>
                    @foreach(['admin' => 'Admin', 'professor' => 'Teacher', 'student' => 'Student', 'super_admin' => 'Superadmin'] as $key => $label)
                        <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    {{-- Logs table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
    @forelse($logs as $log)
        @php
            $user = $log->user;
            $role = $user->role ?? 'N/A';
            $roleColor = match(strtolower($role)) {
                'admin' => 'danger',
                'teacher', 'professor' => 'primary',
                'student' => 'success',
                'super_admin' => 'info',
                default => 'secondary'
            };

            $actionLower = strtolower(trim($log->action));
            $isLogin = str_contains($actionLower, 'logged in');
            $isLogout = str_contains($actionLower, 'logged out');
        @endphp

        <tr>
            <td>{{ $user->name ?? 'Unknown' }}</td>
            <td><span class="badge bg-{{ $roleColor }}">{{ strtoupper($role) }}</span></td>
            <td>
                {{ $log->action }}
            </td>
            <td>{{ $log->ip_address ?? '-' }}</td>
            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center">No audit logs found.</td>
        </tr>
    @endforelse
</tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

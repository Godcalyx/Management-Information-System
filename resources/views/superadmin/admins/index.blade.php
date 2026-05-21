@extends('layouts.superadmin')

@section('content')

<style>
    .admin-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .table thead th {
        vertical-align: middle;
        font-size: 0.9rem;
        letter-spacing: 0.4px;
    }
    .table tbody td {
        vertical-align: middle;
    }
</style>

<div class="container py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-danger mb-0">
            <i class="bi bi-people-fill me-2"></i> Manage Admin Accounts
        </h2>

        <a href="{{ route('super_admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Admin Table -->
    <div class="card admin-card">
        <div class="card-body">

            <p class="text-muted small mb-3">
                Toggle admin access by activating or deactivating accounts.  
                Only active admins can log in to the system.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Admin Name</th>
                            <th>Email Address</th>
                            <th>Status</th>
                            <th style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td class="fw-semibold">
                                    <i class="bi bi-person-circle me-1 text-secondary"></i>
                                    {{ $admin->name }}
                                </td>

                                <td>{{ $admin->email }}</td>

                                <td>
                                    @if($admin->status === 'active')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-2">
                                            <i class="bi bi-slash-circle"></i> Inactive
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($admin->status === 'active')
                                            <form action="{{ route('admins.deactivate', $admin->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Deactivate this admin account?')">
                                                    <i class="bi bi-person-x"></i> Deactivate
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admins.activate', $admin->id) }}" method="POST">
                                                @csrf
                                                <button
                                                    class="btn btn-sm btn-success"
                                                    onclick="return confirm('Activate this admin account?')">
                                                    <i class="bi bi-person-check"></i> Activate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted py-4">
                                    No admin accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

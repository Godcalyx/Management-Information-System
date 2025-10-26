@extends('layouts.admin')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Enrollment Requests</h2>
        <a href="{{ route('enrollments.archive') }}" class="btn btn-secondary">
            View Archive →
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Bar -->
    <form method="GET" action="{{ route('admin.enrollments.index') }}" class="mb-4">
        <div class="input-group">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Search by name or LRN..." 
                value="{{ request('search') }}"
            >
            <button class="btn btn-outline-success" type="submit">
                <i class="bi bi-search me-1"></i> Search
            </button>
        </div>
    </form>

    <!-- Enrollment Table -->
    @if($enrollments->count())
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr> 
                        <th>#</th>
                        <th>Name</th>
                        <th>LRN</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($enrollments as $enrollment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td >{{ $enrollment->first_name }} {{ $enrollment->last_name }}</td>
                            <td>{{ $enrollment->lrn }}</td>
                            <td>{{ $enrollment->email }}</td>
                            <td>
                                <span class="badge 
                                    @if($enrollment->status === 'pending') bg-warning text-dark
                                    @elseif($enrollment->status === 'approved') bg-success
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- View -->
                                    <button class="btn btn-sm btn-info text-white" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewModal{{ $enrollment->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>

                                    @if($enrollment->status === 'pending')
                                        <!-- Approve -->
                                        <form action="{{ route('admin.enrollments.approve', $enrollment->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <form action="{{ route('admin.enrollments.reject', $enrollment->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i> Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Include modal partial --}}
                        @include('admin.enrollments._modal', ['enrollment' => $enrollment])
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $enrollments->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-secondary text-center mt-4">
            No enrollment requests found.
        </div>
    @endif
</div>
@endsection

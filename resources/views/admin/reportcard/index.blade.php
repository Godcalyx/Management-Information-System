@extends('layouts.admin')

@section('title', 'Form Requests')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Form Requests</h2>
        <a href="{{ route('admin.reportcard.archive') }}" class="btn btn-secondary">
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

    <!-- Table -->
    @if($requests->count())
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Date Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($requests as $index => $request)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>
                                <span class="badge 
                                    @if($request->status === 'pending') bg-warning text-dark
                                    @elseif($request->status === 'approved') bg-success
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('M d, Y • h:i A') }}</td>
                            <td>
                                @if($request->status === 'pending')
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Approve -->
                                        <form action="{{ route('admin.reportcard.approve', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Decline -->
                                        <form action="{{ route('admin.reportcard.decline', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i> Decline
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <em class="text-muted">No action needed</em>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-secondary text-center mt-4">
            No report card requests found.
        </div>
    @endif
</div>
@endsection

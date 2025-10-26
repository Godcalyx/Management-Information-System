@extends('layouts.admin') {{-- your admin layout --}}

@section('content')
<div class="container py-4">
    <h2>Pending Student Enrollments</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($pendingEnrollments->isEmpty())
        <p>No pending enrollments.</p>
    @else
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th>Name</th>
                    <th>Grade Level</th>
                    <th>School Year</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingEnrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->lrn ?? 'N/A' }}</td>
                        <td>{{ $enrollment->first_name }} {{ $enrollment->last_name }}</td>
                        <td>{{ $enrollment->grade_level }}</td>
                        <td>{{ $enrollment->school_year }}</td>
                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.enrollments.approve', $enrollment) }}" style="display:inline-block">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.enrollments.reject', $enrollment) }}" style="display:inline-block">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Form Requests')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Form Requests</h2>
        <a href="{{ route('admin.reportcard.archive') }}" class="btn btn-secondary">
            View Archive →
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('admin.reportcard.index') }}" class="mb-4 row g-3">
        <div class="col-md-4">
            <input type="search" name="search" class="form-control"
                placeholder="Search by name and form type..."
                value="{{ request('search') }}">
        </div>
    </form>

    {{-- FORM REQUESTS TABLE --}}
    @if($requests->count())
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Form Type</th>
                        <th>Status</th>
                        <th>Date Requested</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($requests as $index => $request)
                        <tr class="table-row-hover">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $request->user->name }}</td>
                            <td>{{ $request->form_type }}</td>
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
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        {{-- Approve --}}
                                        <form action="{{ route('admin.reportcard.approve', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>

                                        {{-- Decline
                                        <form action="{{ route('admin.reportcard.decline', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i> Decline
                                            </button>
                                        </form> --}}
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

        {{-- Pagination --}}
        {{-- <div class="d-flex justify-content-center mt-4">
            {{ $requests->withQueryString()->links() }}
        </div> --}}
    @else
        <div class="alert alert-secondary text-center mt-4">
            No form requests found.
        </div>
    @endif
</div>

{{-- CUSTOM STYLES --}}
@push('styles')
<style>
    .table-row-hover:hover {
        background-color: #e6f4ea; /* subtle green hover for academic theme */
        transition: background-color 0.2s ease;
    }
    .table-success {
        background-color: #198754 !important; /* consistent green header */
        color: white;
    }
    .table-success th {
        border-color: #198754;
    }
</style>
@endpush

{{-- SCRIPTS --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-submit search after typing
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let typingTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => searchInput.form.submit(), 400);
        });
    }

      // Prevent typing numbers
    searchInput.addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (/\d/.test(char)) {  // if the character is a digit
            e.preventDefault();  // prevent input
        }
    });
});
</script>
@endsection
@endsection
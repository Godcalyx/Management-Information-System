@extends('layouts.admin')

@section('title', 'Form Request Archive')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Form Request Archive</h2>
        <a href="{{ route('admin.reportcard.index') }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs" id="archiveTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">Approved</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">Rejected</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="archiveTabsContent">

        <!-- Approved Requests -->
        <div class="tab-pane fade show active" id="approved" role="tabpanel">
            @if($approved->count())
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-striped align-middle text-center mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Date Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approved as $req)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $req->user->name }}</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                    <td>{{ $req->updated_at->format('M d, Y • h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $approved->links() }}
                </div>
            @else
                <div class="alert alert-secondary text-center mt-4">
                    No approved requests found.
                </div>
            @endif
        </div>

        <!-- Rejected Requests -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @if($rejected->count())
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-striped align-middle text-center mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Date Rejected</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejected as $req)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $req->user->name }}</td>
                                    <td><span class="badge bg-danger">Rejected</span></td>
                                    <td>{{ $req->updated_at->format('M d, Y • h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $rejected->links() }}
                </div>
            @else
                <div class="alert alert-secondary text-center mt-4">
                    No rejected requests found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

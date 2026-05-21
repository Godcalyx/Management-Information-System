@extends('layouts.admin')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Enrollment Archive</h2>
        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">
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

        <!-- Approved Enrollments -->
        <div class="tab-pane fade show active" id="approved" role="tabpanel">
            @if($approved->count())
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-striped align-middle text-center mb-0">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>LRN</th>
                                <th>Grade Level</th>
                                <th>Approved On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approved as $enrollment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $enrollment->first_name }} {{ $enrollment->middle_name }} {{ $enrollment->last_name }}</td>
                                    <td>{{ $enrollment->lrn }}</td>
                                    <td>
    @php
    $grade = $enrollment->gradeLevel->name ?? 'N/A';

    $gradeColor = match((int) $grade) {
        7 => 'bg-success',
        8 => 'bg-warning text-dark',
        9 => 'bg-danger',
        10 => 'bg-primary',
        default => 'bg-secondary'
    };
@endphp
<span class="badge {{ $gradeColor }}">Grade {{ $grade }}</span>
</td>

                                    <td>{{ $enrollment->updated_at->format('F d, Y') }}</td>
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
                    No approved enrollments found.
                </div>
            @endif
        </div>

        <!-- Rejected Enrollments -->
        <div class="tab-pane fade" id="rejected" role="tabpanel">
            @if($rejected->count())
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-striped align-middle text-center mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>LRN</th>
                                <th>Grade Level</th>
                                <th>Rejected On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejected as $enrollment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $enrollment->first_name }} {{ $enrollment->middle_name }} {{ $enrollment->last_name }}</td>
                                    <td>{{ $enrollment->lrn }}</td>
                                    <td>
   @php
    $grade = $enrollment->gradeLevel->name ?? 'N/A';

    $gradeColor = match((int) $grade) {
        7 => 'bg-success',
        8 => 'bg-warning text-dark',
        9 => 'bg-danger',
        10 => 'bg-primary',
        default => 'bg-secondary'
    };
@endphp
<span class="badge {{ $gradeColor }}">Grade {{ $grade }}</span>

</td>

                                    <td>{{ $enrollment->updated_at->format('F d, Y') }}</td>
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
                    No rejected enrollments found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

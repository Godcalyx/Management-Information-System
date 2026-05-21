@extends('layouts.admin')

@section('title', 'Alumni History')

@section('content')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Alumni History</h2>
    </div>

    {{-- Compact Stats --}}
    <div class="row g-2 mb-3">
        <div class="col-auto">
            <div class="card shadow-sm stats-card" style="min-width:130px;">
                <div class="card-body py-2 px-3">
                    <div class="small text-muted">Total Alumni</div>
                    <div class="fw-bold fs-5">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-auto">
            <div class="card shadow-sm stats-card" style="min-width:130px;">
                <div class="card-body py-2 px-3">
                    <div class="small text-muted">Male</div>
                    <div class="fw-bold fs-5 text-primary">{{ $stats['male'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-auto">
            <div class="card shadow-sm stats-card" style="min-width:130px;">
                <div class="card-body py-2 px-3">
                    <div class="small text-muted">Female</div>
                    <div class="fw-bold fs-5 text-danger">{{ $stats['female'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        @if(!empty($stats['by_school_year']))
            <div class="col-auto">
                <div class="card shadow-sm stats-card" style="min-width:200px;">
                    <div class="card-body py-2 px-3">
                        <div class="small text-muted">By School Year</div>
                        <div class="small">
                            @foreach($stats['by_school_year'] as $sy => $count)
                                <span class="badge bg-light text-dark me-1">{{ $sy }}: {{ $count }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filter by School Year --}}
    <form method="GET" action="{{ route('admin.alumni.index') }}" class="mb-3 d-flex align-items-center gap-2">
        <label for="school_year" class="form-label mb-0">Filter by School Year:</label>
        <select name="school_year" id="school_year" class="form-select" style="width:auto;">
            <option value="">All</option>
            @foreach($schoolYears as $year)
                <option value="{{ $year }}" {{ request('school_year') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if($alumni->isEmpty())
                <p class="p-4 text-center text-muted">No alumni records found.</p>
            @else
               <div class="table-responsive">
    <table class="table table-hover table-striped align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th class="text-center" style="width:60px;">NO.</th>
                <th class="text-center">LRN</th>
                <th class="text-center">Name</th>
                <th class="text-center">Sex</th>
                <th class="text-center">Age</th>
                <th class="text-center">School Year</th>
                <th class="text-center">Files</th>
            </tr>
        </thead>
        <tbody>
            @foreach($alumni as $record)
                <tr>
                    <td class="text-center">{{ $loop->iteration + ($alumni->currentPage()-1) * $alumni->perPage() }}</td>
                    <td class="text-center">{{ $record->lrn }}</td>
                   <td style="text-align: center;"class="fw-semibold">
    {{ $record->last_name }}, {{ $record->first_name }}
    @if($record->middle_name)
        {{ ' ' . substr($record->middle_name, 0, 1) . '.' }}
    @endif
</td>

                    <td class="text-center">{{ $record->sex ? ucfirst($record->sex) : '—' }}</td>
                    <td class="text-center">{{ $record->age }}</td>
                    <td class="text-center">{{ $record->school_year }}</td>
                    <td class="text-center">
    @if($record->user) 
        <a href="{{ route('admin.form137.export', $record->user->id) }}"
           class="btn btn-sm btn-info"
           target="_blank">
            Export Form 137
        </a>
    @endif
</td>

                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="p-3 d-flex justify-content-between align-items-center">
    <div class="small text-muted">
        Showing {{ $alumni->firstItem() ?? 0 }} to {{ $alumni->lastItem() ?? 0 }} of {{ $alumni->total() }} records
    </div>
    <div>
        {{ $alumni->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

            @endif
        </div>
    </div>
</div>
@endsection

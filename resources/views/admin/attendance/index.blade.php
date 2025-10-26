@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-success mb-4">Attendance Monitoring</h2>

    {{-- Filter Section --}}
    <form method="GET" action="{{ route('attendance.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label fw-bold">Grade Level</label>
            <select name="grade" class="form-select" required>
                <option value="">Select grade</option>
                @foreach (['7','8','9','10'] as $g)
                    <option value="{{ $g }}" {{ request('grade') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Month</label>
            <select name="month" class="form-select" required>
                @foreach ([
                    'January','February','March','April','May',
                    'June','July','August','September','October','November','December'
                ] as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">School Year</label>
            <input type="text" name="school_year" value="{{ request('school_year', '2025-2026') }}" class="form-control" required>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-success w-100">Load Students</button>
        </div>
    </form>

    @if(isset($students) && count($students))
        <form method="POST" action="{{ route('attendance.store') }}">
            @csrf

            <input type="hidden" name="school_year" value="{{ $school_year }}">
            <input type="hidden" name="month" value="{{ $month }}">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-success text-center">
                        <tr>
                            <th>LRN</th>
                            <th>Student Name</th>
                            <th>Days of School</th>
                            <th>Days Present</th>
                            <th>Days Absent</th>
                            <th>Times Tardy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
    @php
        $rec = $records[$student->id] ?? null;
    @endphp
    <tr>
        <td class="text-center">{{ $student->lrn ?? 'N/A' }}</td>
        <td>{{ $student->name }}</td>
        <td><input type="number" min="0" name="records[{{ $student->id }}][days_of_school]" value="{{ $rec->days_of_school ?? '' }}" class="form-control text-center"></td>
        <td><input type="number" min="0" name="records[{{ $student->id }}][days_present]" value="{{ $rec->days_present ?? '' }}" class="form-control text-center"></td>
        <td><input type="number" min="0" name="records[{{ $student->id }}][days_absent]" value="{{ $rec->days_absent ?? '' }}" class="form-control text-center"></td>
        <td><input type="number" min="0" name="records[{{ $student->id }}][times_tardy]" value="{{ $rec->times_tardy ?? '' }}" class="form-control text-center"></td>
    </tr>
@endforeach

                    </tbody>
                </table>
            </div>

            <div class="mt-3 text-end">
                <button class="btn btn-primary px-4">Save Attendance</button>
            </div>
        </form>
    @elseif(request()->filled('grade') && request()->filled('month'))
        <div class="alert alert-warning">No students found for Grade {{ $grade }} ({{ $month }}, {{ $school_year }}).</div>
    @endif
</div>
@endsection

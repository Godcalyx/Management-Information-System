@extends('layouts.faculty')

@section('content')

<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Encode Grades</h2>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" id="successAlert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Grade Level Selection -->
    <form method="GET" action="{{ route('grades.consolidation') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="grade_level" class="form-label">Select Grade Level</label>
                <select name="grade_level" id="grade_level" class="form-select" onchange="this.form.submit()">
                    <option disabled selected>-- Choose Grade Level --</option>
                    @foreach($gradeLevels as $id => $name)
                        <option value="{{ $id }}" {{ request('grade_level') == $id ? 'selected' : '' }}>
                            Grade {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if($subjects->isEmpty())
        <div class="alert alert-warning text-center">No subjects assigned for this grade level.</div>

    @elseif(request('grade_level'))

        <!-- Student Search -->
        <div class="mb-3">
            <label for="searchInput" class="form-label">Search Student</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search student...">
        </div>

        <!-- Grades Table Form -->
        <form action="{{ route('grades.store') }}" method="POST" id="gradesForm">
            @csrf
            <input type="hidden" name="grade_level_id" value="{{ $gradeLevelId }}">

            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-striped align-middle text-center mb-0" id="gradesTable" style="font-size: 14px; min-width:1200px;">
                    <thead class="table-success position-sticky top-0" style="z-index: 10;">
                        <tr>
                            <th rowspan="2">ID</th>
                            <th rowspan="2">LRN</th>
                            <th rowspan="2" class="text-start">Student Name</th>
                            @foreach($subjects as $subject)
                                <th colspan="4">{{ $subject->name }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($subjects as $subject)
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3</th>
                                <th>Q4</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->lrn }}</td>
                                <td class="text-start text-truncate" style="max-width: 220px;">
                                    {{ trim($student->last_name) }}, {{ trim($student->first_name) }}
                                    @if($student->middle_name) {{ trim($student->middle_name) }} @endif
                                    @if($student->extension_name) {{ trim($student->extension_name) }} @endif
                                </td>

                                @foreach($subjects as $subject)
                                    @php
                                        $grades = $existingGrades[$student->user_id][$subject->id] ?? [];
                                    @endphp

                                    @for($q = 1; $q <= 4; $q++)
                                        @php
                                            $grade = old("grades.{$student->user_id}.{$subject->id}.{$q}") ?? ($grades[$q] ?? '');
                                        @endphp
                                        <td>
                                            <input type="text"
                                                   name="grades[{{ $student->user_id }}][{{ $subject->id }}][{{ $q }}]"
                                                   class="form-control grade-input text-center"
                                                   value="{{ $grade }}"
                                                   maxlength="2"
                                                   placeholder="--">
                                        </td>
                                    @endfor
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmSubmitModal">
                    Submit Grades
                </button>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmSubmitModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Confirm Submission</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to submit these grades?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Yes, Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // Search filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#gradesTable tbody tr').forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });

    // Only FAIL = red highlight
    function updateVisuals(row) {
        row.querySelectorAll('input.grade-input').forEach(input => {
            const td = input.parentElement;
            const value = parseInt(input.value);

            td.style.backgroundColor = 'transparent';
            td.style.color = '';

            if (!isNaN(value) && value < 75) {
                td.style.backgroundColor = '#dc3545';
                td.style.color = 'white';
            }
        });
    }

    // Input rules
    document.querySelectorAll('input.grade-input').forEach(input => {

        input.addEventListener('input', () => {
            let val = input.value.replace(/\D/g,'').slice(0,2);
            input.value = val;
            updateVisuals(input.closest('tr'));
        });

        input.addEventListener('blur', () => {
            if (!input.value) return;
            let val = parseInt(input.value);
            if (val < 70) val = 70;
            if (val > 99) val = 99;
            input.value = val;
            updateVisuals(input.closest('tr'));
        });

        input.addEventListener('keydown', e => {
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') e.preventDefault();
        });
    });

    // Initial paint
    document.querySelectorAll('#gradesTable tbody tr').forEach(updateVisuals);

    // Submit confirm
    document.getElementById('confirmSubmitBtn').onclick =
        () => document.getElementById('gradesForm').submit();

    // Auto-hide success alert
    const alertBox = document.getElementById('successAlert');
    if (alertBox) {
        setTimeout(() => new bootstrap.Alert(alertBox).close(), 3000);
    }

});
</script>

@endif
</div>
@endsection

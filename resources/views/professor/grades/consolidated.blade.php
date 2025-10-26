@extends('layouts.faculty')

@section('title', 'Grade Consolidation')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Grade Consolidation</h2>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('grades.consolidated') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="grade_level" class="form-label">Select Grade Level</label>
            <select name="grade_level" id="grade_level" class="form-select" onchange="this.form.submit()">
                <option disabled selected>-- Choose Grade Level --</option>
                @foreach($gradeLevels as $level)
                    <option value="{{ $level }}" {{ $selectedGrade == $level ? 'selected' : '' }}>
                        Grade {{ $level }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="quarter" class="form-label">Select Quarter</label>
            <select name="quarter" id="quarter" class="form-select" onchange="this.form.submit()">
                <option disabled selected>-- Choose Quarter --</option>
                @foreach(['1' => '1st Quarter', '2' => '2nd Quarter', '3' => '3rd Quarter', '4' => '4th Quarter'] as $key => $label)
                    <option value="{{ $key }}" {{ $quarter == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Search Field -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search by LRN or student name...">
        </div>
    </div>

    <!-- Grades Table -->
    @if($students->isEmpty())
        <div class="alert alert-warning text-center">No students found for this grade level and quarter.</div>
    @else
        <div class="table-responsive shadow-sm rounded-3">
            <table id="gradesTable" class="table table-striped align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>LRN</th>
                        <th class="text-start">Student Name</th>
                        @foreach($subjects as $subject)
                            <th>{{ $subject->name }}</th>
                        @endforeach
                        <th>Average</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->lrn }}</td>
                            <td class="text-start">
                                {{ $student->last_name }}, {{ $student->first_name }}
                                {{ $student->middle_name ? ' ' . $student->middle_name : '' }}
                                {{ $student->extension_name ? ' ' . $student->extension_name : '' }}
                            </td>

                            @foreach($subjects as $subject)
                                @php
                                    $grade = $student->grades->firstWhere('subject_id', $subject->id);
                                @endphp
                                <td @if($grade)
                                        data-bs-toggle="tooltip" 
                                        title="@if($grade->grade >= 90) Excellent 
                                               @elseif($grade->grade >= 75) Satisfactory 
                                               @else Needs Improvement 
                                               @endif">
                                    @endif
                                >
                                    {{ $grade ? $grade->grade : '—' }}
                                </td>
                            @endforeach

                            <td><strong>{{ is_numeric($student->average) ? number_format($student->average, 2) : '—' }}</strong></td>

                            @php
                                $remark = $student->remarks;
                                $badgeClass = match(true) {
                                    str_contains($remark, 'Highest') => 'bg-warning text-dark',
                                    str_contains($remark, 'High') => 'bg-primary text-white',
                                    str_contains($remark, 'With Honors') => 'bg-success text-white',
                                    str_contains($remark, 'Did Not Meet') => 'bg-danger text-white',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <td>
                                <span class="badge {{ $badgeClass }}" 
                                      data-bs-toggle="tooltip"
                                      title="{{ $remark }} means average is {{ 
                                          str_contains($remark, 'Highest') ? '98+' : 
                                          (str_contains($remark, 'High') ? '95–97.99' : 
                                          (str_contains($remark, 'With Honors') ? '90–94.99' : 
                                          (str_contains($remark, 'Did Not Meet') ? 'below 75' : '75–89.99'))) 
                                      }}">
                                    {{ $remark }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Real-time search
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#gradesTable tbody tr');

    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        rows.forEach(row => {
            const lrn = row.cells[0]?.innerText.toLowerCase() || '';
            const name = row.cells[1]?.innerText.toLowerCase() || '';
            row.style.display = (lrn.includes(filter) || name.includes(filter)) ? '' : 'none';
        });
    });

    // Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection

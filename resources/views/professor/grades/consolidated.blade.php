@extends('layouts.faculty')

@section('title', 'Grade Consolidation')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Grade Consolidation</h2>
    </div>

     <!-- Legend Indicators (dots) -->
<div class="mb-4 d-flex align-items-center gap-3">
    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background: linear-gradient(90deg, #FFD700, #FFC107);" title="98–100 Highest Honors"></span>
    <small>98–100 Highest Honors</small>

    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#0d6efd;" title="95–97 High Honors"></span>
    <small>95–97 High Honors</small>

    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#198754;" title="90–94 With Honors"></span>
    <small>90–94 With Honors</small>

    <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:#6c757d;" title="Not eligible for honors"></span>
    <small>Not eligible for honors</small>
</div>


    <!-- Filters -->
    <form method="GET" action="{{ route('grades.consolidated') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="grade_level_id" class="form-label">Select Grade Level</label>
            <select name="grade_level_id" id="grade_level_id" class="form-select" onchange="this.form.submit()">
                <option disabled selected>-- Choose Grade Level --</option>
                @foreach($gradeLevels as $level)
                    <option value="{{ $level->id }}" {{ $selectedGradeId == $level->id ? 'selected' : '' }}>
                        {{ $level->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label for="quarter" class="form-label">Select Quarter</label>
            <select name="quarter" id="quarter" class="form-select" onchange="this.form.submit()">
                <option disabled selected>-- Choose Quarter --</option>
                @foreach(['1' => '1st Quarter', '2' => '2nd Quarter', '3' => '3rd Quarter', '4' => '4th Quarter'] as $key => $label)
                    <option value="{{ $key }}" {{ $quarter == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <!-- Search Field -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search by LRN or Name">
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
                        <th>ID</th>
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
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->lrn }}</td>
                            <td class="text-start" style="white-space: nowrap;">
                                {{ trim($student->last_name) }}{{ $student->extension_name ? ' ' . trim($student->extension_name) : '' }},
                                {{ trim($student->first_name) }}{{ $student->middle_name ? ' ' . trim($student->middle_name) : '' }}
                            </td>

                            {{-- Subject Grades --}}
                            @foreach($subjects as $subject)
                                @php
                                    $grade = $student->grades->firstWhere('subject_id', $subject->id);
                                    $tooltip = '';
                                    if ($grade) {
                                        if ($grade->grade >= 90) $tooltip = 'Excellent';
                                        elseif ($grade->grade >= 75) $tooltip = 'Satisfactory';
                                        else $tooltip = 'Needs Improvement';
                                    }
                                @endphp
                                <td @if($grade) data-bs-toggle="tooltip" title="{{ $tooltip }}" @endif>
                                    {{ $grade ? $grade->grade : '—' }}
                                </td>
                            @endforeach

                            {{-- Average & Honors Badge --}}
                            @php
                                $gradesArray = $student->grades->pluck('grade')->toArray();
                                $avg = $student->average ?? 0;

                                $minGrade = count($gradesArray) ? min($gradesArray) : 0;
                                $honorsEligible = $minGrade >= 87 && $avg >= 90;

                                // Average badge
                                $avgBadgeClass = 'badge ';
                                $avgBadgeStyle = '';

                                if ($honorsEligible) {
                                    if ($avg >= 98) {
                                        $avgBadgeClass .= ''; // style used
                                        $avgBadgeStyle = 'background: linear-gradient(90deg, #FFD700, #FFC107); color: #1a202c;';
                                    } elseif ($avg >= 95) {
                                        $avgBadgeClass .= 'bg-primary text-white';
                                    } elseif ($avg >= 90) {
                                        $avgBadgeClass .= 'bg-success text-white';
                                    }
                                } else {
                                    $avgBadgeClass .= 'bg-secondary text-white';
                                }

                                // Remarks badge text (honors)
                                if ($honorsEligible) {
                                    if ($avg >= 98) $remark = '🥇 With Highest Honors';
                                    elseif ($avg >= 95) $remark = '🥈 With High Honors';
                                    elseif ($avg >= 90) $remark = '🏅 With Honors';
                                } else {
                                    $remark = $student->remarks ?? '';
                                }

                                $remarkBadgeClass = $avgBadgeClass;
                                $remarkBadgeStyle = $avgBadgeStyle;
                            @endphp

                            <td>
                                <span class="{{ $avgBadgeClass }}" style="{{ $avgBadgeStyle }}">
                                    {{ is_numeric($avg) ? number_format($avg, 2) : '—' }}
                                </span>
                            </td>

                            <td>
                                <span class="badge {{ $remarkBadgeClass }}" style="{{ $remarkBadgeStyle }}" data-bs-toggle="tooltip" title="{{ $remark }}">
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
            const lrn = row.cells[1]?.innerText.toLowerCase() || '';
            const name = row.cells[2]?.innerText.toLowerCase() || '';
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

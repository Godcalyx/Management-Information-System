@extends('layouts.admin')

@section('content')
<div class="container mt-4">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Summary of Grades</h2>
    </div>

    {{-- Filters: Grade Level, Subject, Quarters --}}
    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="grade_level_id" class="form-label">Grade Level</label>
            <select id="grade_level_id" name="grade_level_id" class="form-select" onchange="this.form.submit()">
                <option value="" {{ !$selectedGradeLevel ? 'selected' : '' }}>-- Select Grade Level --</option>
                @foreach($gradeLevels as $level)
                    <option value="{{ $level->id }}" {{ $selectedGradeLevel == $level->id ? 'selected' : '' }}>
                        Grade {{ $level->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Select a grade level to view its summary.</div>
        </div>

        <div class="col-md-3">
            <label for="subject_id" class="form-label">Subject</label>
            <select id="subject_id" name="subject_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- All Subjects --</option>
                @foreach($subjects as $sub)
                    {{-- Note: subjects list already filtered by grade level and optional subjectId in controller --}}
                    <option value="{{ $sub->id }}" {{ (isset($selectedSubjectId) && $selectedSubjectId == $sub->id) ? 'selected' : '' }}>
                        {{ $sub->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Selecting a subject will show only that subject's columns.</div>
        </div>

        <div class="col-md-4">
            <label class="form-label">Quarters (multi-select)</label>
            <div class="d-flex gap-3 align-items-center">
                @php
                    $qs = $selectedQuarters ?? [];
                @endphp

                @for($q=1;$q<=4;$q++)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="quarters[]" value="{{ $q }}" id="q{{ $q }}"
                            {{ in_array($q, $qs) ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label" for="q{{ $q }}">Q{{ $q }}</label>
                    </div>
                @endfor

                <div class="form-check ms-3">
                    <input class="form-check-input" type="checkbox" id="allQs" {{ empty($selectedQuarters) ? 'checked' : '' }}
                        onchange="toggleAllQs(this)">
                    <label class="form-check-label" for="allQs">All</label>
                </div>
            </div>
            <small class="text-muted d-block mt-1">Choose one or more quarters. Table adapts to the selection.</small>
        </div>

        {{-- <div class="col-md-2 text-end">
            Keep a submit button for accessibility/manual submit
            <button type="submit" class="btn btn-primary">Apply</button>
        </div> --}}
    </form>

    {{-- If NO grade level selected --}}
    @if(!$selectedGradeLevel)
        <div class="alert alert-info text-center fw-bold">
            Please choose a grade level to display results.
        </div>
    @endif

    {{-- Show summary table --}}
    @if($selectedGradeLevel)
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            @php
                $quarters = !empty($selectedQuarters) ? $selectedQuarters : [1,2,3,4];
                sort($quarters);
                $quarterCount = count($quarters);
                $subjectCount = $subjects->count();
                // colspan count for header calculation
                $colsPerSubject = $quarterCount + 1; // quarter columns + subject avg
                $fixedCols = 6; // #,LRN,Name,Grade Level,Age,Sex
                $totalCols = $fixedCols + ($subjectCount * $colsPerSubject);
            @endphp

            <table class="table table-striped align-middle text-center mb-0" style="font-size:14px;">
                <thead class="table-success">
    <tr>
        <th rowspan="2">#</th>
        <th rowspan="2">LRN</th>
        <th rowspan="2" class="text-start">Name</th>
        <th rowspan="2">Grade Level</th>
        <th rowspan="2">Age</th>
        <th rowspan="2">Sex</th>

        @foreach($subjects as $subject)
            <th colspan="{{ count($quarters) }}">{{ $subject->name }}</th>
            <th rowspan="2">Avg</th>
        @endforeach

        <th rowspan="2">Overall Avg</th>
    </tr>
    <tr>
        @foreach($subjects as $subject)
            @foreach($quarters as $q)
                <th>Q{{ $q }}</th>
            @endforeach
        @endforeach
    </tr>
</thead>


                <tbody>
@forelse($enrollments as $index => $student)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $student->lrn }}</td>
        <td class="text-start" style="white-space:nowrap;">
            {{ $student->last_name }}{{ $student->extension_name ? ' '.$student->extension_name : '' }},
            {{ $student->first_name }}{{ $student->middle_name ? ' '.$student->middle_name : '' }}
        </td>
        <td>{{ $gradeLevelNames[$student->grade_level_id] ?? '-' }}</td>
        <td>{{ $student->age ?? '-' }}</td>
        <td>{{ $student->sex ?? '-' }}</td>

        @php
            $studentTotal = 0;
            $studentCount = 0;
        @endphp

        {{-- Loop through each subject --}}
        @foreach($subjects as $subject)
            @php
                $grades = $gradesData[$student->user_id][$subject->id] ?? [];
                $subjectTotal = 0;
                $subjectCount = 0;
            @endphp

            {{-- Loop through selected quarters --}}
            @foreach($quarters as $q)
                @php
                    $grade = $grades[$q] ?? null;
                    if($grade !== null){
                        $subjectTotal += $grade;
                        $subjectCount++;
                    }
                @endphp
                <td class="{{ $grade !== null && $grade < 75 ? 'text-danger' : '' }}">
                    {{ $grade !== null ? $grade : '—' }}
                </td>
            @endforeach

            @php
                $subjectAvg = $subjectCount ? round($subjectTotal / $subjectCount) : null;
                if($subjectAvg !== null){
                    $studentTotal += $subjectAvg;
                    $studentCount++;
                }
            @endphp

            <td class="fw-bold {{ $subjectAvg !== null && $subjectAvg < 75 ? 'text-danger' : '' }}">
                {{ $subjectAvg !== null ? $subjectAvg : '—' }}
            </td>
        @endforeach

        {{-- Individual Overall Average --}}
        @php
            $overallAvg = $studentCount ? round($studentTotal / $studentCount) : null;
        @endphp
        <td class="fw-bold {{ $overallAvg !== null && $overallAvg < 75 ? 'text-danger' : '' }}">
            {{ $overallAvg !== null ? $overallAvg : '—' }}
        </td>

    </tr>
@empty
    <tr>
        <td colspan="{{ max(6, $totalCols + 1) }}" class="text-center text-muted">
            No students found for this grade level.
        </td>
    </tr>
@endforelse
</tbody>

            </table>

            {{-- Summary footer / legend --}}
            <div class="mt-3 small text-muted">
                Showing quarters: <strong>{{ implode(', ', $quarters) }}</strong>.
                Subject average computed over selected quarters only.
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    // helper: toggle checkboxes for "All"
    function toggleAllQs(checkbox) {
    if (checkbox.checked) {
        document.querySelectorAll('input[name="quarters[]"]').forEach(cb => {
            cb.checked = false;
        });
    }
    checkbox.closest('form').submit();
}

</script>

@endsection

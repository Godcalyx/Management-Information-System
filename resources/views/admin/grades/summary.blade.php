@extends('layouts.admin')

@section('title', 'Grade Summary')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Grade Summary Report</h2>
    </div>

    <!-- Grade Level Filter -->
    <form method="GET" action="{{ route('grades.summary') }}" id="gradeFilterForm" class="mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="gradeLevel" class="col-form-label fw-semibold">Filter by Grade Level:</label>
            </div>
            <div class="col-auto">
                <select name="grade_level" id="gradeLevel" class="form-select">
                    <option disabled selected>-- Select Grade Level --</option>
                    @foreach($gradeLevels as $level)
                        <option value="{{ $level }}" {{ request('grade_level') == $level ? 'selected' : '' }}>
                            Grade {{ $level }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Grade Summary Table -->
    @if(request('grade_level'))
        <div class="table-responsive shadow-sm rounded-3">
            <table class="table table-striped align-middle text-center mb-0" style="font-size: 14px;">
                <thead class="table-success align-middle">
                    <tr>
                        <th rowspan="2">#</th>
                        <th rowspan="2" class="text-start">Student Name</th>
                        <th rowspan="2">LRN</th>
                        <th rowspan="2">Sex</th>
                        <th rowspan="2">Age</th>
                        <th rowspan="2">Grade Level</th>
                        @foreach ($subjects as $subject)
                            <th colspan="5">{{ $subject->name }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($subjects as $subject)
                            <th>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th>AVE</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enrollments as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-start">
                                {{ $student->last_name }},
                                {{ $student->first_name }}
                                {{ $student->middle_name }}
                                {{ $student->extension_name }}
                            </td>
                            <td>{{ $student->lrn }}</td>
                            <td>{{ $student->sex }}</td>
                            <td>{{ $student->age }}</td>
                            <td>{{ $student->grade_level }}</td>

                            @foreach ($subjects as $subject)
                                @php
                                    $grades = $gradesData[$student->user_id][$subject->id] ?? [];
                                    $q1 = $grades['1'] ?? '';
                                    $q2 = $grades['2'] ?? '';
                                    $q3 = $grades['3'] ?? '';
                                    $q4 = $grades['4'] ?? '';
                                    $ave = '';
                                    if ($q1 !== '' && $q2 !== '' && $q3 !== '' && $q4 !== '') {
                                        $ave = round(($q1 + $q2 + $q3 + $q4) / 4);
                                    }
                                @endphp
                                <td>{{ $q1 }}</td>
                                <td>{{ $q2 }}</td>
                                <td>{{ $q3 }}</td>
                                <td>{{ $q4 }}</td>
                                <td><strong>{{ $ave }}</strong></td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center mt-4" role="alert">
            Please select a grade level to view the summary of grades.
        </div>
    @endif
</div>

<!-- Auto-submit Script -->
<script>
    document.getElementById('gradeLevel').addEventListener('change', function () {
        document.getElementById('gradeFilterForm').submit();
    });
</script>
@endsection

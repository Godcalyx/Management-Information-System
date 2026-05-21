@extends('layouts.faculty')

@section('title', 'Student Details')

@section('content')
<div class="container mt-5">

    <h2 class="fw-bold mb-3">{{ $student->name }}</h2>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p><strong>LRN:</strong> {{ $student->lrn }}</p>
            <p><strong>Sex:</strong> @php
        $sex = \App\Models\Enrollment::where('user_id', $student->id)
                    ->latest('id')
                    ->value('sex');
    @endphp
    {{ $sex ?? '—' }}</p>
            <p><strong>Age:</strong> @php
    $birth = \App\Models\Enrollment::where('user_id', $student->id)
                ->latest('id')
                ->value('birthdate');
@endphp
{{ $birth ? \Carbon\Carbon::parse($birth)->age : '—' }}</p>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-journal-text me-1"></i> Grades Overview
        </div>
        <div class="card-body">

            @if($grades->isEmpty())
                <div class="alert alert-warning text-center">No grades available for this student.</div>
            @else
                @foreach($grades as $subjectId => $subjectGrades)
                    <h5 class="fw-bold mt-4">{{ $subjectGrades->first()->subject->name }}</h5>
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Quarter</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($q = 1; $q <= 4; $q++)
                                @php
                                    $gradeRecord = $subjectGrades->firstWhere('quarter', $q);
                                    $gradeValue = $gradeRecord ? $gradeRecord->grade : 'N/A';
                                @endphp
                                <tr>
                                    <td>Q{{ $q }}</td>
                                    <td>{{ $gradeValue }}</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection

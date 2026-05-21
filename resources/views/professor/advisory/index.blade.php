@extends('layouts.faculty')

@section('title', 'Advisory Class')

@section('content')
<div class="container mt-4">

    <h2 class="fw-bold mb-3">
        <i class="bi bi-people-fill me-2"></i>
        Advisory Class — Grade {{ $gradeLevelName }}
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <table class="table table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Name</th>
                        <th>LRN</th>
                        <th>Sex</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->lrn }}</td>
                            <td>
    @php
        $sex = \App\Models\Enrollment::where('user_id', $student->id)
                    ->latest('id')
                    ->value('sex');
    @endphp
    {{ $sex ?? '—' }}
</td>   
                            <td>
                                @php
    $birth = \App\Models\Enrollment::where('user_id', $student->id)
                ->latest('id')
                ->value('birthdate');
@endphp
{{ $birth ? \Carbon\Carbon::parse($birth)->age : '—' }}

                            </td>
                            <td>
                                <a href="{{ route('teacher.advisory.view', $student->id) }}"
                                   class="btn btn-sm btn-primary">
                                   View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Grades')
@section('header', 'Grades')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-success fw-bold">Current School Year: 2024–2025</h5>
    </div>

    <div class="table-responsive shadow-sm rounded-3">
        <table class="table table-striped align-middle text-center mb-0">
            <thead class="table-success">
                <tr>
                    <th>Subject</th>
                    <th>Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                    <th>Final Grade</th>
                    <th>Trend</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $finalGrades = [];
                @endphp

                @foreach ($subjects as $subject => $grades)
                    @php
                        $q1 = $grades['q1'] ?? null;
                        $q2 = $grades['q2'] ?? null;
                        $q3 = $grades['q3'] ?? null;
                        $q4 = $grades['q4'] ?? null;

                        $validGrades = collect([$q1, $q2, $q3, $q4])->filter(fn($g) => is_numeric($g));
                        $final = $validGrades->isNotEmpty() ? $validGrades->avg() : null;

                        if ($final !== null) $finalGrades[] = $final;

                        $trend = '—';
                        if (is_numeric($q3) && is_numeric($q4)) {
                            $trend = $q4 > $q3 ? '📈 Improved' : ($q4 < $q3 ? '⚠️ Dropped' : '➖ Same');
                        }

                        $status = $final !== null ? ($final >= 75 ? '✅ Passed' : '❌ Failed') : '—';
                        $statusClass = $final !== null ? ($final >= 75 ? 'text-success fw-bold' : 'text-danger fw-bold') : 'text-muted';
                    @endphp

                    <tr>
                        <td class="text-start">{{ $subject }}</td>
                        <td>{{ is_numeric($q1) ? $q1 : '—' }}</td>
                        <td>{{ is_numeric($q2) ? $q2 : '—' }}</td>
                        <td>{{ is_numeric($q3) ? $q3 : '—' }}</td>
                        <td>{{ is_numeric($q4) ? $q4 : '—' }}</td>
                        <td class="fw-bold">{{ $final !== null ? number_format($final, 2) : '—' }}</td>
                        <td>{{ $trend }}</td>
                        <td class="{{ $statusClass }}">{{ $status }}</td>
                    </tr>
                @endforeach

                {{-- GWA Row --}}
                @if(count($finalGrades))
                    @php
                        $gwa = collect($finalGrades)->avg();
                    @endphp
                    <tr class="table-secondary fw-bold">
                        <td colspan="5" class="text-end">General Weighted Average (GWA)</td>
                        <td>{{ number_format($gwa, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartData = {
        labels: ['Q1', 'Q2', 'Q3', 'Q4'],
        datasets: [
            @foreach ($subjects as $subject => $grades)
            {
                label: "{{ $subject }}",
                data: [
                    {{ is_numeric($grades['q1'] ?? null) ? $grades['q1'] : 'null' }},
                    {{ is_numeric($grades['q2'] ?? null) ? $grades['q2'] : 'null' }},
                    {{ is_numeric($grades['q3'] ?? null) ? $grades['q3'] : 'null' }},
                    {{ is_numeric($grades['q4'] ?? null) ? $grades['q4'] : 'null' }},
                ],
                borderColor: '#' + Math.floor(Math.random()*16777215).toString(16).padStart(6, '0'),
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.3
            },
            @endforeach
        ]
    };
</script>
@endsection

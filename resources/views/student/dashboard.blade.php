@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="row g-4">

    <!-- =========================
     | Subjects You Excel In (Detailed Table)
     ========================= -->
    <div class="shadow-sm p-4 rounded-3 bg-light border border-info">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="fw-bold text-info mb-0">
            <i class="bi bi-bar-chart-line-fill text-info fs-1"></i> 📊 Subjects You Excel In
        </h3>
        <!-- Trend Legend -->
        <div class="text-end">
            <span class="badge bg-success">↑ Improving</span>
            <span class="badge bg-danger">↓ Declining</span>
            <span class="badge bg-secondary">→ No Change</span>
            <span class="badge bg-secondary">❌ Excluded</span>
        </div>
    </div>

    <p class="text-muted small">
        Based on your <strong>average performance across all quarters</strong>.
    </p>

    <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th class="text-start">Subject</th>
                    {{-- <th>Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                    <th>Final</th> --}}
                    <th>Average</th>
                    <th>Trend</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subjectBreakdown as $subject)
                    <tr class="{{ !$subject['is_excellent'] ? 'table-secondary text-muted' : '' }}">
                        <td class="text-start fw-semibold">{{ $subject['name'] }}</td>
                        {{-- <td>{{ $subject['q1'] ?? '—' }}</td>
                        <td>{{ $subject['q2'] ?? '—' }}</td>
                        <td>{{ $subject['q3'] ?? '—' }}</td>
                        <td>{{ $subject['q4'] ?? '—' }}</td>
                        <td>{{ $subject['final'] ?? '—' }}</td> --}}
                        <td class="fw-bold">{{ number_format($subject['average'], 2) }}%</td>
                        <td>
                            @if(!$subject['is_excellent'])
                                <span class="badge bg-secondary">❌ excluded</span>
                            @else
                                @php
                                    $trendClass = match ($subject['trend']) {
                                        '↑' => 'bg-success',
                                        '↓' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $trendClass }}">
                                    {{ $subject['trend'] }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


    {{-- <!-- =========================
     | Grade Trend Line Chart
     ========================= -->
    @if(count($quarters))
    <div class="col-12 col-lg-10 col-xl-8 mx-auto">
        <div class="shadow-sm p-4 rounded-3 bg-light border border-primary">
            <i class="bi bi-graph-up text-primary fs-1"></i>
            <h3 class="mt-2 fw-bold text-primary">📈 Grade Trends</h3>
            <p class="text-muted small">Your academic progress over time.</p>

            <div id="gradeTrendChart"></div>
        </div>
    </div>
    @endif --}}

</div>

{{-- <script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================
     | Grade Trend Chart
     ========================= */
    @if(count($quarters))
        new ApexCharts(
            document.querySelector("#gradeTrendChart"),
            {
                chart: { type: 'line', height: 300, toolbar: { show: false } },
                stroke: { curve: 'smooth', width: 3 },
                dataLabels: { enabled: false },
                series: [{
                    name: 'Average Grade',
                    data: @json($quarterlyAverages)
                }],
                xaxis: { categories: @json($quarters) },
                yaxis: { min: 70, max: 100 }
            }
        ).render();
    @endif
});
</script> --}}
@endsection

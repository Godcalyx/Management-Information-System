@extends('layouts.app')

@section('title', 'Grade Analytics')
@section('header', 'Grade Analytics')

@section('content')
<div class="container mt-5">

    <!-- Page Header -->
    <div class="mb-4">
        <h2 class="fw-bold text-success">Subject Performance Overview</h2>
        <p class="text-muted">View your average grades per subject below.</p>
    </div>

    <!-- No Data Alert -->
    @if(empty($labels) || empty($data))
        <div class="alert alert-warning shadow-sm">
            No grade data available yet. Your performance chart will appear here once grades are recorded.
        </div>
    @else
        <!-- Chart Card -->
        <div class="card shadow-sm rounded-3 p-4 mb-4">
            <canvas id="gradeChart" height="150"></canvas>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(!empty($labels) && !empty($data))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('gradeChart').getContext('2d');

    const labels = {!! json_encode($labels) !!};
    const data = {!! json_encode($data) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Grade',
                data: data,
                backgroundColor: '#198754',
                borderColor: '#14532d',
                borderWidth: 1,
                borderRadius: 5, // Rounded bars for modern look
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: {
                    min: 70,
                    max: 100,
                    ticks: { stepSize: 5 },
                    title: {
                        display: true,
                        text: 'Grade'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Subjects'
                    }
                }
            }
        }
    });
});
</script>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Academic Standing')
@section('header', 'Academic Standing')

@section('content')
<div class="container mt-5">

    {{-- Header & Student Standing --}}
    <div class="text-center mb-5">
        <h3 class="fw-bold text-success mb-2">🏅 Academic Standing</h3>

        @if($percentile !== null)
            @php
                if ($percentile <= 10) {
                    $message = "Outstanding! You are in the top 10% of your class!";
                } elseif ($percentile <= 20) {
                    $message = "Excellent! You are in the top 20% of your class!";
                } elseif ($percentile <= 50) {
                    $message = "Good job! You are in the top half of your class!";
                } else {
                    $message = "Keep improving! Every effort counts!";
                }
            @endphp
            <p class="lead mb-2">{{ $message }}</p>

            <p class="mb-0">
                Honor Status:
                @if ($honor)
                    <span class="badge bg-warning text-dark" title="Based on your average">{{ $honor }}</span>
                @else
                    <span class="badge bg-secondary" title="No honor level this term">No Honors</span>
                @endif
            </p>
        @else
            <p class="lead mb-2">No ranking available yet. Your grades may still be incomplete.</p>
        @endif
    </div>

    <hr class="mb-4">

    {{-- Personal Grade Details --}}
    {{-- <div class="mb-3">
        <h5 class="fw-bold">📊 Your Grades</h5>
        @if($grades->count())
            <div class="table-responsive shadow-sm rounded-3">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-success text-center">
                        <tr>
                            <th>Subject</th>
                            <th>Quarter 1</th>
                            <th>Quarter 2</th>
                            <th>Quarter 3</th>
                            <th>Quarter 4</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($grades as $grade)
                            <tr>
                                <td>{{ $grade->subject_name }}</td>
                                <td>{{ $grade->q1 ?? '-' }}</td>
                                <td>{{ $grade->q2 ?? '-' }}</td>
                                <td>{{ $grade->q3 ?? '-' }}</td>
                                <td>{{ $grade->q4 ?? '-' }}</td>
                                <td>{{ $grade->final ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-secondary text-center">
                No grades available yet.
            </div>
        @endif
    </div> --}}
</div>
@endsection

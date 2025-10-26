@extends('layouts.app')

@section('title', 'Request School Forms')
@section('header', 'Request School Forms')

@section('content')
<div class="container mt-5">

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <!-- Header -->
            <h4 class="fw-bold mb-4">📋 Available Forms</h4>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Forms Table -->
            <div class="table-responsive">
                <table class="table table-striped align-middle text-center mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Code</th>
                            <th>Form Name</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Form 138</td>
                            <td>Report Card</td>
                            <td>Official student academic record for the semester/year.</td>
                            <td>
                                <form action="{{ route('reportcard.request') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="form_type" value="report_card">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Request
                                    </button>
                                </form>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Form 137</td>
                            <td>Permanent Record</td>
                            <td>Comprehensive record of a student's academic performance.</td>
                            <td>
                                <form action="" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="form_type" value="permanent_record">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-file-earmark-arrow-up me-1"></i> Request
                                    </button>
                        {{-- Add more rows for other forms later --}}
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

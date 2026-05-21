@extends('layouts.app') 

@section('title', 'Student Settings')
@section('header', 'Settings')

@section('content')
<div class="container mt-5">


    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Profile Info -->
    <div class="card shadow-sm mb-4 rounded-3">
        <div class="card-body d-flex flex-column flex-md-row align-items-center gap-4">

            <!-- User Info and Upload Form -->
           <div class="flex-grow-1">
    <h4 class="fw-bold mb-2">{{ auth()->user()->name }}</h4>
    <p class="mb-3"><strong>Email :</strong> {{ auth()->user()->email }}</p>
    <p class="mb-3"><strong>LRN:</strong> {{ auth()->user()->lrn }}</p>

    @php
        // Get the latest enrollment for the current user
        $enrollment = \App\Models\Enrollment::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->first();

        // Fetch the grade level name dynamically
        $gradeLevel = $enrollment ? optional($enrollment->gradeLevel)->name ?? 'N/A' : 'N/A';
    @endphp

    <p class="mb-3"><strong>Grade Level :</strong> {{ $gradeLevel }}</p>
</div>

        </div>
    </div>

    <!-- Password Change -->
<div class="card shadow-sm mb-4 rounded-3">
    <div class="card-body">
        <h5 class="fw-bold mb-3">Change Password</h5>
        <form action="{{ route('settings.change-password') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                @error('current_password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="new_password" class="form-control" placeholder="Enter new password">
                @error('new_password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Re-enter new password">
            </div>

            <!-- Forgot Password Link -->
            <div class="mb-3">
                <a href="{{ route('student.password.request') }}" class="text-decoration-none">Forgot your password?</a>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="bi bi-key-fill me-1"></i> Change Password
            </button>
        </form>

           
    </div>
</div>


</div>
@endsection

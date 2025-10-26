@extends('layouts.faculty')

@section('title', 'Change Password')

@section('content')
<div class="container mt-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Change Password</h2>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Change Password Form -->
    <div class="card shadow-sm rounded-3 p-4">
        <form action="{{ route('professor.change-password.update') }}" method="POST">
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

            <button type="submit" class="btn btn-success">
                <i class="bi bi-key-fill me-1"></i> Change Password
            </button>
        </form>
    </div>

</div>
@endsection

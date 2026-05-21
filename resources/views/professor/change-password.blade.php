@extends('layouts.faculty') 

@section('title', 'Change Password')

@section('content')
<div class="container mt-5">

<!-- Header -->
         <h2 class="fw-bold text-success mb-4">Change Password</h2>

    <!-- Change Password Form -->
    <div class="card shadow-sm rounded-3 p-4">
        <form action="{{ route('professor.change-password.update') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Current Password</label>
                <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                @error('current_password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password" required>
                @error('new_password')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
                <div id="passwordHelp" class="form-text">
                    Minimum 8 characters, include uppercase, lowercase, number & symbol.
                </div>
                <div class="mt-2" id="password-strength"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Re-enter new password" required>
            </div>

            <div class="mb-3">
                <a href="{{ route('professor.password.request') }}" class="text-decoration-none">Forgot your password?</a>
            </div>

            <button type="submit" class="btn btn-warning text-dark">
                <i class="bi bi-key-fill me-1"></i> Change Password
            </button>
        </form>
    </div>
</div>

<!-- Password Strength Script -->
<script>
    const passwordInput = document.getElementById('new_password');
    const strengthDiv = document.getElementById('password-strength');

    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        let strength = '';
        let color = '';

        if(val.length < 8) {
            strength = 'Too short';
            color = 'red';
        } else if(!/[A-Z]/.test(val) || !/[a-z]/.test(val) || !/[0-9]/.test(val) || !/[^A-Za-z0-9]/.test(val)) {
            strength = 'Weak';
            color = 'orange';
        } else {
            strength = 'Strong';
            color = 'green';
        }

        strengthDiv.textContent = 'Password strength: ' + strength;
        strengthDiv.style.color = color;
    });
</script>
@endsection

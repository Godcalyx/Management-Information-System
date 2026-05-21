@extends('layouts.guest')

@section('title', 'Admin Reset Password')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm rounded-3 p-4">
                <h4 class="fw-bold mb-3">Admin Reset Password</h4>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.password.email') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        @error('email')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-warning text-dark w-100">
                        Send Password Reset Link
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('login.admin') }}">Back to Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

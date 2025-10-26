@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Change Your Password</h2>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
@endsection

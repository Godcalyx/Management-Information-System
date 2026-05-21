@extends('layouts.guest')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm w-100 p-4" style="max-width: 400px; border-radius: 12px;">
        <div class="card-body text-center">
            <h3 class="mb-2">Two-Factor Authentication</h3>
            <p class="text-muted mb-4">
                Enter the 6-digit code sent to your email.
            </p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('2fa.store') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="two_factor_code" maxlength="6"
                           class="form-control text-center fs-4 fw-bold @error('two_factor_code') is-invalid @enderror"
                           value="{{ old('two_factor_code') }}" required autofocus>
                    @error('two_factor_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Verify</button>
            </form>

            <form method="POST" action="{{ route('2fa.resend') }}" id="resend-form">
                @csrf
                <button type="submit" class="btn btn-link p-0" id="resend-btn">
                    Didn't receive a code? Resend
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-control {
        letter-spacing: 0.3rem;
    }
    @media (max-width: 400px) {
        .form-control {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    const TIMER_KEY = 'twofa_timer_end';
    const DURATION = 300;

    const timerDisplay = document.getElementById('timer');
    const resendBtn = document.getElementById('resend-btn');

    let countdown = null;

    function getRemainingTime() {
        const end = sessionStorage.getItem(TIMER_KEY);
        if (!end) return 0;
        return Math.max(0, Math.floor((end - Date.now()) / 1000));
    }

    function updateTimer() {
        const timeLeft = getRemainingTime();

        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerDisplay.textContent = 'Expired';
            resendBtn.disabled = false;
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        resendBtn.disabled = true;
    }

    function startTimer(reset = false) {
        if (reset || !sessionStorage.getItem(TIMER_KEY)) {
            sessionStorage.setItem(
                TIMER_KEY,
                Date.now() + DURATION * 1000
            );
        }

        updateTimer();
        clearInterval(countdown);
        countdown = setInterval(updateTimer, 1000);
    }

    startTimer();

    document.getElementById('resend-form').addEventListener('submit', () => {
        sessionStorage.removeItem(TIMER_KEY);
    });
</script>

@endsection

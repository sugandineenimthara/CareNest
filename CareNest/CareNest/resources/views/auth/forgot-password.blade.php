@extends('layouts.auth')

@section('title', 'Forgot Password - CareNest')

@section('styles')
<style>
    .forgot-card {
        padding: 48px;
        width: 100%;
        max-width: 440px;
        text-align: center;
    }

    .forgot-title {
        font-family: var(--font-heading);
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .forgot-subtext {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 32px;
        line-height: 1.5;
    }

    .status-alert {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 14px;
        border-radius: 12px;
        font-size: 13px;
        margin-bottom: 24px;
        text-align: left;
        word-break: break-word;
    }

    .error-alert {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #ef4444;
        padding: 12px;
        border-radius: 12px;
        font-size: 13px;
        margin-bottom: 20px;
        text-align: left;
    }

    .back-link {
        display: inline-block;
        margin-top: 24px;
        font-size: 14px;
        font-weight: 700;
        color: #00c853;
        text-decoration: none;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>
@endsection

@section('content')
<div class="glass-card forgot-card">
    <h1 class="forgot-title">Forgot Password?</h1>
    <p class="forgot-subtext">No worries! Enter your registered email address and we'll send you a password recovery link.</p>

    @if (session('status'))
        <div class="status-alert">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-alert">
            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf

        <div class="input-wrapper" style="text-align: left; margin-bottom: 24px;">
            <label class="input-label">EMAIL ADDRESS</label>
            <input type="email" name="email" class="input-field" placeholder="Enter your registered email" value="{{ old('email') }}" required autofocus>
            <i class="fa-regular fa-envelope input-icon"></i>
        </div>

        <button type="submit" class="btn-emerald">
            Send Reset Link <i class="fa-solid fa-paper-plane"></i>
        </button>

        <a href="{{ route('login') }}" class="back-link">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Login
        </a>
    </form>
</div>
@endsection

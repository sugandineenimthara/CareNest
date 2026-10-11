@extends('layouts.auth')

@section('title', 'Reset Password - CareNest')

@section('styles')
<style>
    .reset-card {
        padding: 48px;
        width: 100%;
        max-width: 440px;
        text-align: center;
    }

    .reset-title {
        font-family: var(--font-heading);
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .reset-subtext {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 32px;
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
</style>
@endsection

@section('content')
<div class="glass-card reset-card">
    <h1 class="reset-title">Reset Password</h1>
    <p class="reset-subtext">Set your new password for CareNest account</p>

    @if($errors->any())
        <div class="error-alert">
            <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="input-wrapper" style="text-align: left; margin-bottom: 20px;">
            <label class="input-label">EMAIL ADDRESS</label>
            <input type="email" name="email" class="input-field" value="{{ old('email', $email) }}" required readonly>
            <i class="fa-regular fa-envelope input-icon"></i>
        </div>

        <div class="input-wrapper" style="text-align: left; margin-bottom: 20px;">
            <label class="input-label">NEW PASSWORD</label>
            <input type="password" name="password" class="input-field" placeholder="••••••••" required autofocus>
            <i class="fa-solid fa-lock input-icon"></i>
        </div>

        <div class="input-wrapper" style="text-align: left; margin-bottom: 28px;">
            <label class="input-label">CONFIRM NEW PASSWORD</label>
            <input type="password" name="password_confirmation" class="input-field" placeholder="••••••••" required>
            <i class="fa-solid fa-lock input-icon"></i>
        </div>

        <button type="submit" class="btn-emerald">
            Reset Password <i class="fa-solid fa-check"></i>
        </button>
    </form>
</div>
@endsection

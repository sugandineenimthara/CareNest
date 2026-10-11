@extends('layouts.auth')

@section('title', 'Login - CareNest')

@section('styles')
<style>
    .login-container {
        position: relative;
        width: 100%;
        max-width: 1100px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
    }

    /* Floating Images */
    .floating-img {
        position: absolute;
        border-radius: 36px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border: 4px solid rgba(255, 255, 255, 0.7);
        transition: transform 0.4s ease;
    }

    .floating-img:hover {
        transform: scale(1.03);
    }

    .float-top-right {
        top: -10px;
        right: 40px;
        width: 260px;
        height: 260px;
        transform: rotate(4deg);
    }

    .float-bottom-left {
        bottom: -20px;
        left: 40px;
        width: 240px;
        height: 240px;
        transform: rotate(-6deg);
    }

    .floating-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Center Login Glass Card */
    .login-card {
        padding: 48px;
        width: 100%;
        max-width: 440px;
        text-align: center;
        z-index: 5;
    }

    .login-title {
        font-family: var(--font-heading);
        font-size: 36px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        letter-spacing: -0.5px;
    }

    .login-subtitle {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 32px;
    }

    .input-wrapper-login {
        text-align: left;
        margin-bottom: 20px;
    }

    .forgot-link {
        float: right;
        font-size: 12px;
        font-weight: 700;
        color: #00c853;
        text-decoration: none;
    }

    .forgot-link:hover {
        text-decoration: underline;
    }

    .action-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-top: 10px;
    }

    /* Bottom Pill Badge */
    .bottom-badge-wrapper {
        position: absolute;
        bottom: -50px;
        left: 50%;
        transform: translateX(-50%);
    }

    .bottom-badge {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        padding: 8px 24px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #1b4d3e;
        text-transform: uppercase;
        border: 1px solid rgba(255, 255, 255, 0.9);
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

    @media (max-width: 992px) {
        .floating-img {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <!-- Top Right Floating Image -->
    <div class="floating-img float-top-right">
        <img src="{{ asset('images/mothernchild.jpg') }}" alt="Mother holding child">
    </div>

    <!-- Bottom Left Floating Image -->
    <div class="floating-img float-bottom-left">
        <img src="{{ asset('images/linedrawing.jpg') }}" alt="Mother and baby line drawing">
    </div>

    <!-- Center Login Glass Card -->
    <div class="glass-card login-card">
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">MATERNAL & PEDIATRIC CARE SYSTEM</p>

        @if(session('success'))
            <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 12px; font-size: 13px; margin-bottom: 20px; text-align: left;">
                <i class="fa-solid fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-alert">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error-alert">
                <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Username / Email -->
            <div class="input-wrapper-login">
                <label class="input-label">USERNAME OR EMAIL</label>
                <div class="input-wrapper" style="margin-bottom: 0;">
                    <input type="text" name="email" class="input-field" placeholder="Enter your username, email, or phone" value="{{ old('email') }}" required autofocus>
                    <i class="fa-regular fa-user input-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="input-wrapper-login">
                <div style="margin-bottom: 8px;">
                    <label class="input-label" style="display: inline-block;">PASSWORD</label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>
                <div class="input-wrapper" style="margin-bottom: 0;">
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                    <i class="fa-solid fa-lock input-icon"></i>
                </div>
            </div>

            <!-- Buttons -->
            <div class="action-group">
                <button type="submit" class="btn-emerald">
                    Login <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div style="display: flex; gap: 10px; margin-top: 8px;">
                    <a href="{{ route('register.mother') }}" class="btn-pink" style="flex: 1; font-size: 13px; text-align: center;">
                        Register as Mother
                    </a>
                    <a href="{{ route('register.midwife') }}" class="btn-pink" style="flex: 1; font-size: 13px; text-align: center; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0;">
                        Register as Midwife
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bottom Badge -->
    <div class="bottom-badge-wrapper">
        <div class="bottom-badge">
            CARING FOR MOTHERS & CHILDREN
        </div>
    </div>
</div>
@endsection

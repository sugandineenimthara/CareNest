@extends('layouts.auth')

@section('title', 'Register - CareNest')

@section('header_action')
    <a href="{{ route('login') }}" class="header-link">Login</a>
@endsection

@section('styles')
<style>
    .register-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        width: 100%;
        max-width: 1100px;
    }

    /* Left Side Content */
    .hero-title {
        font-family: var(--font-heading);
        font-size: 52px;
        font-weight: 800;
        line-height: 1.1;
        color: #1e293b;
        margin-bottom: 18px;
        letter-spacing: -1px;
    }

    .hero-title .highlight {
        color: #1b5e20;
        font-style: italic;
    }

    .hero-subtitle {
        font-size: 15px;
        line-height: 1.6;
        color: #64748b;
        margin-bottom: 30px;
        max-width: 440px;
    }

    .illustration-wrapper {
        position: relative;
        border-radius: 36px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        max-width: 440px;
    }

    .illustration-img {
        width: 100%;
        height: 380px;
        object-fit: cover;
        display: block;
    }

    .illustration-badge {
        position: absolute;
        bottom: 20px;
        left: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 14px 20px;
        border-radius: 20px;
        font-size: 13px;
        font-style: italic;
        color: #334155;
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    /* Right Side Glass Form */
    .register-card {
        padding: 44px;
        max-width: 480px;
        width: 100%;
        margin-left: auto;
    }

    .badge-sub {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #1b5e20;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .card-title {
        font-family: var(--font-heading);
        font-size: 34px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .card-subtext {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 28px;
    }

    /* Segmented Role Switcher */
    .role-section {
        margin-bottom: 24px;
    }

    .role-switcher {
        display: flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 16px;
        gap: 4px;
    }

    .role-btn {
        flex: 1;
        padding: 12px;
        border: none;
        background: transparent;
        border-radius: 12px;
        font-family: var(--font-main);
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .role-btn.active {
        background: #ffffff;
        color: var(--forest-green);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .login-redirect {
        text-align: center;
        margin-top: 24px;
        font-size: 13px;
        color: #64748b;
    }

    .login-redirect a {
        color: #00c853;
        font-weight: 700;
        text-decoration: none;
    }

    .error-msg {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }

    @media (max-width: 900px) {
        .register-grid {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        .register-card {
            margin-left: 0;
            max-width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="register-grid">
    <!-- Left Hero Column -->
    <div>
        <h1 class="hero-title">Start your <span class="highlight">nurturing</span> journey.</h1>
        <p class="hero-subtitle">A safe, ethereal space designed for mothers and healthcare professionals to connect, grow, and provide the best care.</p>
        
        <div class="illustration-wrapper">
            <img src="{{ asset('images/mother_child_nurturing.jpg') }}" alt="Mother holding child" class="illustration-img">
            <div class="illustration-badge">
                "Every nest begins with a single branch of care."
            </div>
        </div>
    </div>

    <!-- Right Glassmorphism Form -->
    <div class="glass-card register-card">
        <span class="badge-sub">REGISTRATION</span>
        <h2 class="card-title">Welcome</h2>
        <p class="card-subtext">Create your account for the Maternal and Pediatric Care System</p>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Role Selector -->
            <div class="role-section">
                <label class="input-label">I AM A...</label>
                <div class="role-switcher">
                    <button type="button" class="role-btn active" onclick="selectRole('mother', this)">Mother</button>
                    <button type="button" class="role-btn" onclick="selectRole('provider', this)">Provider</button>
                </div>
                <input type="hidden" name="role" id="selected_role" value="mother">
                @error('role') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Full Name -->
            <div class="input-wrapper">
                <label class="input-label">FULL NAME</label>
                <input type="text" name="name" class="input-field" placeholder="Enter Your Name" value="{{ old('name') }}" required>
                <i class="fa-regular fa-user input-icon"></i>
                @error('name') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Email Address -->
            <div class="input-wrapper">
                <label class="input-label">EMAIL ADDRESS</label>
                <input type="email" name="email" class="input-field" placeholder="Enter a valid email address" value="{{ old('email') }}" required>
                <i class="fa-regular fa-envelope input-icon"></i>
                @error('email') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Password -->
            <div class="input-wrapper">
                <label class="input-label">PASSWORD</label>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                <i class="fa-solid fa-lock input-icon"></i>
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-emerald">
                Create Account <i class="fa-solid fa-arrow-right"></i>
            </button>

            <!-- Login Link -->
            <div class="login-redirect">
                Already have an account? <a href="{{ route('login') }}">Login</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectRole(role, btn) {
        document.getElementById('selected_role').value = role;
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
</script>
@endsection

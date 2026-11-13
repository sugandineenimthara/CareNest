@extends('layouts.auth')

@section('title', 'Enter Verification Code - CareNest')

@section('styles')
<style>
    .otp-card {
        padding: 48px;
        width: 100%;
        max-width: 420px;
        text-align: center;
    }
    .otp-title {
        font-family: var(--font-heading);
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .otp-subtext {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 32px;
        line-height: 1.5;
    }
    .otp-email-badge {
        display: inline-block;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
        font-weight: 700;
        font-size: 13px;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 28px;
    }
    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-bottom: 28px;
    }
    .otp-digit {
        width: 50px;
        height: 58px;
        text-align: center;
        font-size: 24px;
        font-weight: 800;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        color: #1b4d3e;
        background: white;
    }
    .otp-digit:focus {
        border-color: #00c853;
        box-shadow: 0 0 0 3px rgba(0,200,83,0.15);
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
    .status-alert {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 12px;
        border-radius: 12px;
        font-size: 13px;
        margin-bottom: 20px;
        text-align: left;
    }
    .back-link {
        display: inline-block;
        margin-top: 20px;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
    }
    .back-link:hover { color: #00c853; }
    .resend-link {
        font-size: 13px;
        color: #64748b;
        margin-top: 16px;
    }
    .resend-link a {
        color: #00c853;
        font-weight: 700;
        text-decoration: none;
    }
    .resend-link a:hover { text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="glass-card otp-card">
    <div style="font-size:40px; margin-bottom:12px;">📩</div>
    <h1 class="otp-title">Check Your Email</h1>
    <p class="otp-subtext">We sent a 6-digit code to</p>
    <div class="otp-email-badge">{{ $email }}</div>

    @if (session('status'))
        <div class="status-alert">
            <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error-alert">
            <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('password.otp.verify') }}" method="POST" id="otpForm">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="code" id="codeHidden">

        <div class="otp-inputs">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" inputmode="numeric" maxlength="1" class="otp-digit" data-index="{{ $i }}" autocomplete="off">
            @endfor
        </div>

        <button type="submit" class="btn-emerald">
            Verify Code <i class="fa-solid fa-arrow-right"></i>
        </button>
    </form>

    <div class="resend-link">
        Didn't receive it? <a href="{{ route('password.request') }}">Request a new code</a>
    </div>

    <a href="{{ route('password.request') }}" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>
@endsection

@section('scripts')
<script>
    const digits = document.querySelectorAll('.otp-digit');
    const form   = document.getElementById('otpForm');
    const hidden = document.getElementById('codeHidden');

    digits.forEach((input, idx) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(-1);
            if (input.value && idx < 5) digits[idx + 1].focus();
            syncHidden();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && idx > 0) {
                digits[idx - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            [...pasted].forEach((ch, i) => {
                if (digits[i]) digits[i].value = ch;
            });
            const next = Math.min(pasted.length, 5);
            digits[next].focus();
            syncHidden();
        });
    });

    function syncHidden() {
        hidden.value = [...digits].map(d => d.value).join('');
    }

    form.addEventListener('submit', () => syncHidden());

    digits[0].focus();
</script>
@endsection

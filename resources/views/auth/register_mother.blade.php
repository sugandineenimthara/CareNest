@extends('layouts.auth')

@section('title', 'Mother Registration - CareNest')

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

    .login-card {
        padding: 36px;
        width: 100%;
        max-width: 520px;
        text-align: center;
        z-index: 5;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .login-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .notice-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 20px;
        text-align: left;
        font-size: 13px;
        color: #166534;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        line-height: 1.4;
    }

    .input-wrapper-login {
        text-align: left;
        margin-bottom: 16px;
    }

    .input-field {
        width: 100%;
        padding: 11px 14px;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        font-family: inherit;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
    }

    .input-field:focus {
        border-color: #00c853;
        box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.12);
    }
    
    .input-label {
        font-size: 11px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        display: block;
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
<div class="login-container">
    <div class="login-card">
        <h1 class="login-title">Mother Registration</h1>
        <p class="login-subtitle" style="font-size: 12px; color: #64748b; margin-bottom: 18px;">MATERNAL & PEDIATRIC CARE SYSTEM</p>

        <div class="notice-box">
            <i class="fa-solid fa-circle-info" style="font-size: 16px; margin-top: 2px; color: #00c853;"></i>
            <div>
                <strong>Midwife Clinic Registration Required:</strong>
                Your area midwife registers you first and provides you with your <strong>Unique Mother ID</strong>. Enter that ID below to request account activation.
            </div>
        </div>

        @if($errors->any())
            <div class="error-alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.mother') }}" method="POST">
            @csrf

            <div class="input-wrapper-login">
                <label class="input-label">UNIQUE MOTHER ID (PROVIDED BY MIDWIFE) <span style="color: #ef4444;">*</span></label>
                <input type="number" name="mother_id" class="input-field" placeholder="e.g. 5" value="{{ old('mother_id') }}" required autofocus>
            </div>

            <div class="input-wrapper-login">
                <label class="input-label">FULL NAME <span style="color: #ef4444;">*</span></label>
                <input type="text" name="mother_name" class="input-field" placeholder="Enter your full name" value="{{ old('mother_name') }}" required>
            </div>

            <div class="input-wrapper-login">
                <label class="input-label">EMAIL ADDRESS <span style="color: #ef4444;">*</span></label>
                <input type="email" name="email" class="input-field" placeholder="name@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="input-wrapper-login" style="display: flex; gap: 12px;">
                <div style="flex: 1;">
                    <label class="input-label">PHONE NUMBER</label>
                    <input type="text" name="phone_no" class="input-field" placeholder="07XXXXXXXX" value="{{ old('phone_no') }}">
                </div>
                <div style="flex: 1;">
                    <label class="input-label">DATE OF BIRTH</label>
                    <input type="date" name="date_of_birth" class="input-field" value="{{ old('date_of_birth') }}">
                </div>
            </div>

            <div class="input-wrapper-login">
                <label class="input-label">RESIDENTIAL ADDRESS</label>
                <input type="text" name="address" class="input-field" placeholder="Enter your residential address" value="{{ old('address') }}">
            </div>

            <div class="input-wrapper-login" style="display: flex; gap: 12px;">
                <div style="flex: 1;">
                    <label class="input-label">PASSWORD <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                </div>
                <div style="flex: 1;">
                    <label class="input-label">CONFIRM PASSWORD <span style="color: #ef4444;">*</span></label>
                    <input type="password" name="password_confirmation" class="input-field" placeholder="••••••••" required>
                </div>
            </div>

            <div class="action-group" style="margin-top: 24px;">
                <button type="submit" class="btn-pink" style="width: 100%; padding: 13px; background: linear-gradient(135deg, #1b5e20, #00695c); color: white; border: none; border-radius: 12px; font-weight: 800; font-size: 15px; cursor: pointer; box-shadow: 0 4px 14px rgba(27,94,32,0.25);">
                    Submit Registration Request
                </button>
                <div style="margin-top: 14px; text-align: center;">
                    <a href="{{ route('login') }}" style="color: #64748b; font-size: 13px; font-weight: 600; text-decoration: none;">
                        Already registered? <span style="color: #00c853; font-weight: 700;">Login here</span>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

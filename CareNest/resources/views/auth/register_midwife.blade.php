@extends('layouts.auth')

@section('title', 'Midwife Registration - CareNest')

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

    /* Center Login Glass Card */
    .login-card {
        padding: 30px;
        width: 100%;
        max-width: 500px;
        text-align: center;
        z-index: 5;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .login-title {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .input-wrapper-login {
        text-align: left;
        margin-bottom: 15px;
    }

    .input-field {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }
    
    .input-label {
        font-size: 12px;
        font-weight: bold;
        color: #555;
        margin-bottom: 4px;
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
        <h1 class="login-title">Midwife Registration</h1>
        <p class="login-subtitle" style="font-size: 11px; color: #666; margin-bottom: 20px;">MATERNAL & PEDIATRIC CARE SYSTEM</p>

        @if($errors->any())
            <div class="error-alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.midwife') }}" method="POST">
            @csrf

            <div class="input-wrapper-login">
                <label class="input-label">FULL NAME</label>
                <input type="text" name="midwife_name" class="input-field" value="{{ old('midwife_name') }}" required>
            </div>

            <div class="input-wrapper-login">
                <label class="input-label">EMAIL ADDRESS</label>
                <input type="email" name="email" class="input-field" value="{{ old('email') }}" required>
            </div>
            
            <div class="input-wrapper-login">
                <label class="input-label">ASSIGNED AREA</label>
                <select name="area_id" class="input-field" required>
                    <option value="">Select an Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->area_id }}">{{ $area->area_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="input-wrapper-login" style="display: flex; gap: 10px;">
                <div style="flex: 1;">
                    <label class="input-label">PASSWORD</label>
                    <input type="password" name="password" class="input-field" required>
                </div>
                <div style="flex: 1;">
                    <label class="input-label">CONFIRM PASSWORD</label>
                    <input type="password" name="password_confirmation" class="input-field" required>
                </div>
            </div>

            <div class="action-group" style="margin-top: 20px;">
                <button type="submit" class="btn-pink" style="width: 100%; padding: 12px; background: #00c853; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    Submit Request
                </button>
                <div style="margin-top: 10px; text-align: center;">
                    <a href="{{ route('login') }}" style="color: #666; font-size: 13px; text-decoration: none;">Already have an account? Login</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password - CareNest</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --primary: #00c853;
            --primary-dark: #1b4d3e;
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-main);
            background: #f8fafc;
            color: #0f172a;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-dark);
            border-bottom: 1px solid #e2e8f0;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: #e8f5e9;
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .nav-list {
            list-style: none;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .nav-item a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .nav-item.active a {
            background: #e8f5e9;
            color: var(--primary);
            font-weight: 700;
        }

        .nav-item a i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.7);
            position: sticky;
            top: 0;
            z-index: 90;
            min-height: 76px;
            box-sizing: border-box;
        }

        .header-left {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-left: auto;
        }

        .content-area {
            padding: 48px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px -4px rgba(15, 23, 42, 0.05);
            padding: 36px 40px;
            width: 100%;
            max-width: 480px;
        }

        .card-title {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 24px;
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
        }

        .forgot-link {
            font-size: 12.5px;
            font-weight: 700;
            color: #00c853;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.15s;
        }

        .forgot-link:hover {
            color: #00963d;
            text-decoration: underline;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field {
            width: 100%;
            height: 46px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0 42px 0 14px;
            font-family: var(--font-main);
            font-size: 14px;
            color: #0f172a;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .input-field:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.12);
        }

        .btn-eye {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
        }

        .btn-eye:hover { color: #475569; }

        .field-error {
            color: #dc2626;
            font-size: 12.5px;
            font-weight: 600;
            margin-top: 6px;
        }

        .btn-action {
            width: 100%;
            height: 46px;
            background: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-action:hover {
            background: #00b34a;
        }

        .btn-verify {
            background: #1b4d3e;
            margin-top: 10px;
        }

        .btn-verify:hover {
            background: #153b30;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <span>CareNest</span>
        </div>

        <ul class="nav-list">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-chart-pie"></i><span>Dashboard</span></a>
            </li>
            @if(in_array($role, ['provider', 'admin']))
            <li class="nav-item">
                <a href="{{ route('admin.midwife-requests') }}"><i class="fa-solid fa-user-clock"></i><span>Midwife Requests</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.midwives.index') }}"><i class="fa-solid fa-user-nurse"></i><span>Manage Midwives</span></a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('admin.mothers.index') }}"><i class="fa-solid fa-person-breastfeeding"></i><span>Mothers Registry</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children Registry</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-box-open"></i><span>Thriposha Book</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a>
            </li>
            <li class="nav-item active" style="margin-top: 16px;">
                <a href="{{ route('password.change') }}"><i class="fa-solid fa-key"></i><span>Change Password</span></a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">CareNest</div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <main class="content-area">
            <div class="card">
                <h1 class="card-title">Change Password</h1>

                @if(session('success'))
                    <div class="alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('password.update_auth') }}" method="POST" id="passwordForm">
                    @csrf

                    <!-- Current Password Step -->
                    <div id="currentPasswordSection" style="{{ $identityVerified ? 'display: none;' : 'display: block;' }}">
                        <div class="form-group">
                            <div class="label-row">
                                <label class="form-label" for="current_password">Current Password</label>
                                <a href="{{ route('password.request') }}?from=change-password" class="forgot-link" title="Forgot Password?">
                                    <i class="fa-solid fa-key"></i> Forgot Password?
                                </a>
                            </div>
                            <div class="input-wrapper">
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password" 
                                       class="input-field" 
                                       placeholder="Enter current password">
                                <button type="button" class="btn-eye" onclick="toggleVisibility('current_password', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div id="currentPasswordError" class="field-error" style="display: none;"></div>
                        </div>

                        <button type="button" class="btn-action btn-verify" id="btnVerify" onclick="verifyCurrentPassword()">
                            Verify
                        </button>
                    </div>

                    <!-- New Password Step (Visible only after verification) -->
                    <div id="newPasswordSection" style="{{ $identityVerified ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group">
                            <label class="form-label" for="password">New Password</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="input-field" 
                                       placeholder="Enter new password" 
                                       {{ $identityVerified ? 'required' : '' }}>
                                <button type="button" class="btn-eye" onclick="toggleVisibility('password', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-wrapper">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       class="input-field" 
                                       placeholder="Re-enter new password" 
                                       {{ $identityVerified ? 'required' : '' }}>
                                <button type="button" class="btn-eye" onclick="toggleVisibility('password_confirmation', this)">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-action" id="btnSubmit">
                            Change Password
                        </button>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        function toggleVisibility(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        async function verifyCurrentPassword() {
            const currentPwInput = document.getElementById('current_password');
            const currentPw = currentPwInput.value.trim();
            const errDiv = document.getElementById('currentPasswordError');
            const btnVerify = document.getElementById('btnVerify');

            if (!currentPw) {
                errDiv.style.display = 'block';
                errDiv.innerText = 'Please enter current password.';
                currentPwInput.focus();
                return;
            }

            btnVerify.disabled = true;
            btnVerify.innerText = 'Verifying...';
            errDiv.style.display = 'none';

            try {
                const res = await fetch("{{ route('password.verify_current') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ current_password: currentPw })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    // Hide current password section and show new password section
                    document.getElementById('currentPasswordSection').style.display = 'none';
                    const newSection = document.getElementById('newPasswordSection');
                    newSection.style.display = 'block';
                    document.getElementById('password').required = true;
                    document.getElementById('password_confirmation').required = true;
                    document.getElementById('password').focus();
                } else {
                    errDiv.style.display = 'block';
                    errDiv.innerText = data.message || 'Incorrect password.';
                    btnVerify.disabled = false;
                    btnVerify.innerText = 'Verify';
                }
            } catch (e) {
                errDiv.style.display = 'block';
                errDiv.innerText = 'Verification failed. Please try again.';
                btnVerify.disabled = false;
                btnVerify.innerText = 'Verify';
            }
        }
    </script>
</body>
</html>

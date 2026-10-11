<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Mother - CareNest</title>
    <meta name="description" content="Register a new mother into the CareNest maternal care system with pregnancy history.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --sidebar-width: 260px;
            --bg-gradient: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 50%, #f0f9ff 100%);
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-main); background: var(--bg-gradient); background-attachment: fixed; color: var(--text-dark); min-height: 100vh; display: flex; }

        /* Sidebar */
        .sidebar { width: var(--sidebar-width); background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-right: 1px solid rgba(226,232,240,0.8); display: flex; flex-direction: column; padding: 30px 20px; position: fixed; top:0; bottom:0; left:0; z-index:100; }
        .sidebar-brand { display:flex; align-items:center; gap:10px; font-family:var(--font-heading); font-size:24px; font-weight:800; color:#1b4d3e; text-decoration:none; margin-bottom:36px; padding-left:10px; }
        .sidebar-menu { list-style:none; display:flex; flex-direction:column; gap:6px; flex:1; overflow-y:auto; padding-right:4px; }
        .sidebar-menu::-webkit-scrollbar { width:4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:4px; }
        .nav-item a { display:flex; align-items:center; gap:14px; padding:12px 18px; border-radius:16px; font-size:14px; font-weight:600; color:#64748b; text-decoration:none; transition:all 0.25s ease; }
        .nav-item a:hover { background:#f1f5f9; color:var(--text-dark); }
        .nav-item.active a { background:linear-gradient(135deg,#1b5e20 0%,#00695c 100%); color:#fff; box-shadow:0 8px 20px rgba(27,94,32,0.25); }
        .nav-item i { font-size:16px; width:20px; text-align:center; }
        .sidebar-footer { margin-top:20px; display:flex; flex-direction:column; gap:12px; }
        .logout-btn { display:flex; align-items:center; gap:10px; background:transparent; border:none; padding:10px 18px; font-family:var(--font-main); font-size:14px; font-weight:600; color:#64748b; cursor:pointer; transition:color 0.2s ease; width:100%; }
        .logout-btn:hover { color:#ef4444; }

        /* Main */
        .main-wrapper { margin-left:var(--sidebar-width); flex:1; display:flex; flex-direction:column; min-height:100vh; }
        .top-header { display:flex; justify-content:space-between; align-items:center; padding:20px 48px; background:rgba(255,255,255,0.7); backdrop-filter:blur(16px); border-bottom:1px solid rgba(226,232,240,0.6); position:sticky; top:0; z-index:90; }
        .header-left { font-family:var(--font-heading); font-size:22px; font-weight:800; color:#1b4d3e; display:flex; align-items:center; gap:12px; }
        .back-btn { background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#64748b; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .back-btn:hover { background:#e2e8f0; color:#1b5e20; }
        .header-right { display:flex; align-items:center; gap:20px; }
        .user-badge-container { display:flex; align-items:center; gap:10px; padding-left:12px; border-left:1px solid #e2e8f0; }
        .user-role-label { font-size:13px; font-weight:700; color:#334155; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#475569; font-size:16px; }

        /* Content */
        .content-container { padding: 40px 48px; max-width: 1100px; width: 100%; margin: 0 auto; }
        .page-title { font-family:var(--font-heading); font-size:34px; font-weight:800; color:#0f172a; margin-bottom:4px; }
        .page-sub { font-size:14px; color:var(--text-muted); margin-bottom:36px; }

        /* Progress Steps */
        .progress-steps { display:flex; align-items:center; gap:0; margin-bottom:40px; }
        .step-item { display:flex; align-items:center; gap:10px; flex:1; }
        .step-circle { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:var(--font-heading); font-size:14px; font-weight:800; transition:all 0.3s; flex-shrink:0; }
        .step-circle.active { background:linear-gradient(135deg,#1b5e20,#00695c); color:#fff; box-shadow:0 4px 14px rgba(27,94,32,0.35); }
        .step-circle.completed { background:#00c853; color:#fff; }
        .step-circle.pending { background:#f1f5f9; color:#94a3b8; }
        .step-label { font-size:12px; font-weight:700; color:#64748b; white-space:nowrap; }
        .step-label.active { color:#1b5e20; }
        .step-connector { flex:1; height:2px; background:#e2e8f0; margin:0 12px; border-radius:2px; }
        .step-connector.done { background:#00c853; }

        /* Form Sections */
        .form-section {
            background: rgba(255,255,255,0.92); backdrop-filter:blur(16px);
            border-radius:28px; padding:36px;
            border:1px solid rgba(255,255,255,0.8);
            box-shadow:0 12px 36px rgba(0,0,0,0.05);
            margin-bottom:28px;
        }
        .section-header { display:flex; align-items:center; gap:14px; margin-bottom:28px; padding-bottom:18px; border-bottom:2px solid #f1f5f9; }
        .section-icon { width:46px; height:46px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
        .sec-green { background:#e8f5e9; color:#2e7d32; }
        .sec-teal { background:#e0f7fa; color:#00695c; }
        .sec-blue { background:#e3f2fd; color:#1565c0; }
        .sec-rose { background:#fce4ec; color:#c62828; }
        .section-title { font-family:var(--font-heading); font-size:20px; font-weight:800; color:#0f172a; }
        .section-desc { font-size:13px; color:#64748b; margin-top:2px; }

        /* Grid Layouts */
        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .form-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
        .form-grid-4 { display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:20px; }
        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }
        .span-4 { grid-column: span 4; }

        /* Form Controls */
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-label { font-size:12px; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.8px; }
        .form-label span.req { color:#e11d48; }
        .form-control {
            padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:14px;
            font-family:var(--font-main); font-size:14px; color:var(--text-dark);
            background:#fafafa; outline:none; transition:all 0.2s ease; width:100%;
        }
        .form-control:focus { border-color:#00c853; background:#fff; box-shadow:0 0 0 3px rgba(0,200,83,0.1); }
        .form-control.is-invalid { border-color:#ef4444; background:#fff5f5; }
        select.form-control { cursor:pointer; }
        textarea.form-control { resize:vertical; min-height:80px; }

        /* Boolean Radio / Checkbox Groups */
        .bool-group { display:flex; gap:10px; }
        .bool-option { flex:1; }
        .bool-option input[type="radio"] { display:none; }
        .bool-option label {
            display:flex; align-items:center; justify-content:center; gap:6px;
            padding:9px 12px; border-radius:12px; border:1.5px solid #e2e8f0;
            font-size:13px; font-weight:700; color:#64748b; cursor:pointer;
            transition:all 0.2s ease; background:#fafafa;
        }
        .bool-option input[type="radio"]:checked + label { border-color:#00c853; background:#e8f5e9; color:#1b5e20; }

        /* Error messages */
        .field-error { font-size:11px; color:#ef4444; font-weight:600; margin-top:2px; }

        /* Previous Pregnancy History */
        .prev-preg-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
        .prev-preg-table thead th { font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; padding:8px 12px; text-align:left; border-bottom:1.5px solid #f1f5f9; white-space:nowrap; }
        .prev-preg-table tbody tr { border-bottom:1px solid #f8fafc; }
        .prev-preg-table td { padding:8px 6px; vertical-align:middle; }
        .prev-preg-table td input, .prev-preg-table td select { padding:8px 10px; border:1.5px solid #e2e8f0; border-radius:10px; font-size:12px; color:var(--text-dark); background:#fafafa; outline:none; transition:border-color 0.2s; width:100%; min-width:80px; }
        .prev-preg-table td input:focus, .prev-preg-table td select:focus { border-color:#00c853; background:#fff; }
        .prev-preg-table td input[type="checkbox"] { width:18px; height:18px; accent-color:#00c853; cursor:pointer; min-width:unset; }
        .remove-row-btn { background:#fee2e2; border:none; border-radius:8px; width:28px; height:28px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#dc2626; font-size:12px; transition:all 0.2s; }
        .remove-row-btn:hover { background:#fca5a5; }

        .add-row-btn {
            display:flex; align-items:center; gap:8px;
            padding:10px 18px; border-radius:12px;
            border:2px dashed #cbd5e1; background:transparent;
            font-size:13px; font-weight:700; color:#64748b;
            cursor:pointer; transition:all 0.2s ease; margin-top:4px;
        }
        .add-row-btn:hover { border-color:#00c853; color:#1b5e20; background:#f0fdf4; }

        /* Family Health Checkboxes */
        .health-check-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .health-check-item { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; border:1.5px solid #e2e8f0; background:#fafafa; cursor:pointer; transition:all 0.2s; }
        .health-check-item:hover { border-color:#00c853; background:#f0fdf4; }
        .health-check-item input[type="checkbox"] { width:18px; height:18px; accent-color:#00c853; cursor:pointer; flex-shrink:0; }
        .health-check-label { font-size:13px; font-weight:600; color:#475569; }

        /* Action Buttons */
        .form-actions { display:flex; justify-content:flex-end; gap:14px; margin-top:8px; }
        .btn-cancel { padding:13px 24px; border-radius:14px; border:1.5px solid #e2e8f0; background:#fff; font-family:var(--font-heading); font-size:15px; font-weight:700; color:#64748b; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .btn-cancel:hover { background:#f8fafc; border-color:#cbd5e1; }
        .btn-submit { padding:13px 32px; border-radius:14px; border:none; background:linear-gradient(135deg,#1b5e20,#00695c); font-family:var(--font-heading); font-size:15px; font-weight:700; color:#fff; cursor:pointer; box-shadow:0 8px 20px rgba(27,94,32,0.25); transition:all 0.25s ease; display:flex; align-items:center; gap:8px; }
        .btn-submit:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(27,94,32,0.35); }

        /* Alert / Error Box */
        .error-box { background:#fef2f2; border:1px solid #fca5a5; border-radius:16px; padding:16px 20px; margin-bottom:24px; }
        .error-box-title { font-size:14px; font-weight:800; color:#dc2626; margin-bottom:8px; display:flex; align-items:center; gap:8px; }
        .error-box ul { list-style:none; display:flex; flex-direction:column; gap:4px; }
        .error-box li { font-size:13px; color:#b91c1c; display:flex; align-items:flex-start; gap:6px; }
        .error-box li::before { content:"•"; color:#f87171; }

        @media (max-width:1024px) {
            .sidebar { width:80px; padding:20px 10px; }
            .sidebar-brand span, .nav-item span { display:none; }
            .main-wrapper { margin-left:80px; }
            .form-grid-3, .form-grid-4 { grid-template-columns:1fr 1fr; }
            .span-3, .span-4 { grid-column:span 2; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-leaf" style="color:#00c853;"></i>
            <span>CareNest</span>
        </a>
        <ul class="sidebar-menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if(Auth::check() && (Auth::user()->role === 'provider' || Auth::user()->role === 'admin'))
            <li class="nav-item">
                <a href="{{ route('admin.midwife-requests') }}">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Midwife Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.midwives.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Manage Midwives</span>
                </a>
            </li>
            @endif
            <li class="nav-item active">
                <a href="{{ route('admin.mothers.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Mothers</span>
                </a>
            </li>
            <li class="nav-item"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>


            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <!-- Header -->
        <header class="top-header">
            <div class="header-left">
                <a href="{{ route('admin.mothers.index') }}" class="back-btn" title="Back to Mothers">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                Register New Mother
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <main class="content-container">
            <h1 class="page-title">Mother Registration</h1>
            <p class="page-sub">Fill in all sections below to register a new mother. Pregnancy history details are optional.</p>

            <!-- Progress Indicator -->
            <div class="progress-steps">
                <div class="step-item">
                    <div class="step-circle active">1</div>
                    <div class="step-label active">Personal Info</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-circle pending">2</div>
                    <div class="step-label">Pregnancy History</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-circle pending">3</div>
                    <div class="step-label">Previous Pregnancies</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-circle pending">4</div>
                    <div class="step-label">Family Health</div>
                </div>
            </div>

            @if($errors->any())
                <div class="error-box">
                    <div class="error-box-title"><i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:</div>
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.mothers.store') }}" method="POST" id="motherRegistrationForm">
                @csrf

                <!-- ============ SECTION 1: Personal Details ============ -->
                <div class="form-section" id="section-personal">
                    <div class="section-header">
                        <div class="section-icon sec-green">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <div class="section-title">Personal Information</div>
                            <div class="section-desc">Mother's basic personal and contact details</div>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="mother_name">Mother's Full Name <span class="req">*</span></label>
                            <input type="text" id="mother_name" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror"
                                value="{{ old('mother_name') }}" placeholder="Enter full name" required>
                            @error('mother_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone_no">Phone Number</label>
                            <input type="tel" id="phone_no" name="phone_no" class="form-control @error('phone_no') is-invalid @enderror"
                                value="{{ old('phone_no') }}" placeholder="e.g. 0771234567">
                            @error('phone_no')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="midwife_id">Assigned Midwife <span class="req">*</span></label>
                            <select id="midwife_id" name="midwife_id" class="form-control @error('midwife_id') is-invalid @enderror" required>
                                <option value="">— Select Midwife —</option>
                                @foreach($midwives as $midwife)
                                    <option value="{{ $midwife->midwife_id }}" {{ old('midwife_id') == $midwife->midwife_id ? 'selected' : '' }}>
                                        {{ $midwife->midwife_name }} {{ $midwife->area ? '(' . $midwife->area->area_name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('midwife_id')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="height">Height (cm)</label>
                            <input type="number" id="height" name="height" step="0.01" min="50" max="250"
                                class="form-control @error('height') is-invalid @enderror"
                                value="{{ old('height') }}" placeholder="e.g. 162">
                            @error('height')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="weight">Weight (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.01" min="20" max="300"
                                class="form-control @error('weight') is-invalid @enderror"
                                value="{{ old('weight') }}" placeholder="e.g. 58.5">
                            @error('weight')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="husband_name">Husband's Name</label>
                            <input type="text" id="husband_name" name="husband_name" class="form-control @error('husband_name') is-invalid @enderror"
                                value="{{ old('husband_name') }}" placeholder="Husband's full name">
                            @error('husband_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="husband_occupation">Husband's Occupation</label>
                            <input type="text" id="husband_occupation" name="husband_occupation" class="form-control @error('husband_occupation') is-invalid @enderror"
                                value="{{ old('husband_occupation') }}" placeholder="e.g. Farmer, Teacher">
                            @error('husband_occupation')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group span-2">
                            <label class="form-label" for="address">Home Address</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                placeholder="Enter full address...">{{ old('address') }}</textarea>
                            @error('address')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- ============ SECTION 2: Current Pregnancy History ============ -->
                <div class="form-section" id="section-pregnancy">
                    <div class="section-header">
                        <div class="section-icon sec-teal">
                            <i class="fa-solid fa-baby-carriage"></i>
                        </div>
                        <div>
                            <div class="section-title">Current Pregnancy History</div>
                            <div class="section-desc">Details about the current pregnancy at time of registration</div>
                        </div>
                    </div>

                    <div class="form-grid-3">
                        <div class="form-group">
                            <label class="form-label" for="no_living_children">No. of Living Children</label>
                            <input type="number" id="no_living_children" name="no_living_children" min="0" max="20"
                                class="form-control @error('no_living_children') is-invalid @enderror"
                                value="{{ old('no_living_children', 0) }}" placeholder="0">
                            @error('no_living_children')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="age_of_youngest_child">Age of Youngest Child (years)</label>
                            <input type="number" id="age_of_youngest_child" name="age_of_youngest_child" min="0" max="50"
                                class="form-control @error('age_of_youngest_child') is-invalid @enderror"
                                value="{{ old('age_of_youngest_child') }}" placeholder="e.g. 2">
                            @error('age_of_youngest_child')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="no_of_weeks_pregnant_at_registration">Weeks Pregnant at Registration</label>
                            <input type="number" id="no_of_weeks_pregnant_at_registration" name="no_of_weeks_pregnant_at_registration" min="0" max="45"
                                class="form-control @error('no_of_weeks_pregnant_at_registration') is-invalid @enderror"
                                value="{{ old('no_of_weeks_pregnant_at_registration') }}" placeholder="e.g. 12">
                            @error('no_of_weeks_pregnant_at_registration')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_menstrual_period">Last Menstrual Period (LMP)</label>
                            <input type="date" id="last_menstrual_period" name="last_menstrual_period"
                                class="form-control @error('last_menstrual_period') is-invalid @enderror"
                                value="{{ old('last_menstrual_period') }}">
                            @error('last_menstrual_period')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="expected_date_of_delivery">Expected Date of Delivery (EDD)</label>
                            <input type="date" id="expected_date_of_delivery" name="expected_date_of_delivery"
                                class="form-control @error('expected_date_of_delivery') is-invalid @enderror"
                                value="{{ old('expected_date_of_delivery') }}">
                            @error('expected_date_of_delivery')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="date_confirmed_by_US">Date Confirmed by Ultrasound</label>
                            <input type="date" id="date_confirmed_by_US" name="date_confirmed_by_US"
                                class="form-control @error('date_confirmed_by_US') is-invalid @enderror"
                                value="{{ old('date_confirmed_by_US') }}">
                            @error('date_confirmed_by_US')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group span-3">
                            <label class="form-label" for="first_fetal_movements_date">First Fetal Movements Date</label>
                            <input type="date" id="first_fetal_movements_date" name="first_fetal_movements_date"
                                class="form-control @error('first_fetal_movements_date') is-invalid @enderror"
                                value="{{ old('first_fetal_movements_date') }}" style="max-width:300px;">
                            @error('first_fetal_movements_date')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- ============ SECTION 3: Previous Pregnancy History ============ -->
                <div class="form-section" id="section-prev-pregnancy">
                    <div class="section-header">
                        <div class="section-icon sec-blue">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <div class="section-title">Previous Pregnancy History</div>
                            <div class="section-desc">Record each previous pregnancy separately. Add as many rows as needed.</div>
                        </div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="prev-preg-table" id="prevPregTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Which Pregnancy</th>
                                    <th>Date of Birth</th>
                                    <th>Birth Weight (kg)</th>
                                    <th>Gender</th>
                                    <th>Results</th>
                                    <th>Place</th>
                                    <th title="Rubella Vaccinated">Rubella</th>
                                    <th title="Folic Acid Vaccinated">Folic Acid</th>
                                    <th title="Infertility">Infertility</th>
                                    <th title="Blood Relation Marriage">Blood Rel.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="prevPregBody">
                                <!-- Rows added by JS -->
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="addPrevPregRow" class="add-row-btn">
                        <i class="fa-solid fa-plus"></i> Add Previous Pregnancy
                    </button>
                </div>

                <!-- ============ SECTION 4: Family Health History ============ -->
                <div class="form-section" id="section-family-health">
                    <div class="section-header">
                        <div class="section-icon sec-rose">
                            <i class="fa-solid fa-heart-pulse"></i>
                        </div>
                        <div>
                            <div class="section-title">Family Health History</div>
                            <div class="section-desc">Known hereditary or family medical conditions</div>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="diabetes">Diabetes</label>
                            <select id="diabetes" name="diabetes" class="form-control @error('diabetes') is-invalid @enderror">
                                <option value="">— Select —</option>
                                <option value="None" {{ old('diabetes') == 'None' ? 'selected' : '' }}>None</option>
                                <option value="Type 1" {{ old('diabetes') == 'Type 1' ? 'selected' : '' }}>Type 1</option>
                                <option value="Type 2" {{ old('diabetes') == 'Type 2' ? 'selected' : '' }}>Type 2</option>
                                <option value="Gestational" {{ old('diabetes') == 'Gestational' ? 'selected' : '' }}>Gestational</option>
                                <option value="Family History" {{ old('diabetes') == 'Family History' ? 'selected' : '' }}>Family History</option>
                            </select>
                            @error('diabetes')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="high_blood_pressure">High Blood Pressure</label>
                            <select id="high_blood_pressure" name="high_blood_pressure" class="form-control @error('high_blood_pressure') is-invalid @enderror">
                                <option value="">— Select —</option>
                                <option value="None" {{ old('high_blood_pressure') == 'None' ? 'selected' : '' }}>None</option>
                                <option value="Yes" {{ old('high_blood_pressure') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="Family History" {{ old('high_blood_pressure') == 'Family History' ? 'selected' : '' }}>Family History</option>
                                <option value="Controlled" {{ old('high_blood_pressure') == 'Controlled' ? 'selected' : '' }}>Controlled with Medication</option>
                            </select>
                            @error('high_blood_pressure')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blood_related_diseases">Blood Related Diseases</label>
                            <input type="text" id="blood_related_diseases" name="blood_related_diseases"
                                class="form-control @error('blood_related_diseases') is-invalid @enderror"
                                value="{{ old('blood_related_diseases') }}"
                                placeholder="e.g. Anaemia, Thalassaemia...">
                            @error('blood_related_diseases')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="other_health">Other Health Conditions</label>
                            <input type="text" id="other_health" name="other_health"
                                class="form-control @error('other_health') is-invalid @enderror"
                                value="{{ old('other_health') }}"
                                placeholder="Any other relevant conditions...">
                            @error('other_health')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <a href="{{ route('admin.mothers.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Register Mother
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        // ---- Previous Pregnancy History dynamic rows ----
        let rowCount = 0;

        function addPrevPregRow() {
            rowCount++;
            const tbody = document.getElementById('prevPregBody');
            const tr = document.createElement('tr');
            tr.id = `prevRow_${rowCount}`;
            const idx = rowCount - 1;

            tr.innerHTML = `
                <td style="text-align:center; font-size:12px; font-weight:800; color:#94a3b8;">${rowCount}</td>
                <td>
                    <select name="prev_pregnancy[${idx}][which_pregnancy]" style="min-width:110px;">
                        <option value="">Select</option>
                        <option>1st</option><option>2nd</option><option>3rd</option>
                        <option>4th</option><option>5th</option><option>6th+</option>
                    </select>
                </td>
                <td><input type="date" name="prev_pregnancy[${idx}][date_of_birth]"></td>
                <td><input type="number" name="prev_pregnancy[${idx}][birth_weight]" step="0.01" min="0" max="10" placeholder="kg" style="min-width:70px;"></td>
                <td>
                    <select name="prev_pregnancy[${idx}][gender]" style="min-width:90px;">
                        <option value="">Any</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </td>
                <td><input type="text" name="prev_pregnancy[${idx}][results]" placeholder="Live/Still/Abort" style="min-width:110px;"></td>
                <td><input type="text" name="prev_pregnancy[${idx}][place_of_pregnancy]" placeholder="Hospital/Home" style="min-width:120px;"></td>
                <td style="text-align:center;"><input type="checkbox" name="prev_pregnancy[${idx}][rubella_vaccinated]" title="Rubella Vaccinated"></td>
                <td style="text-align:center;"><input type="checkbox" name="prev_pregnancy[${idx}][folic_acid_vaccinated]" title="Folic Acid Vaccinated"></td>
                <td style="text-align:center;"><input type="checkbox" name="prev_pregnancy[${idx}][infertility]" title="Infertility"></td>
                <td style="text-align:center;"><input type="checkbox" name="prev_pregnancy[${idx}][blood_relation_marriage]" title="Blood Relation Marriage"></td>
                <td>
                    <button type="button" class="remove-row-btn" onclick="removeRow('prevRow_${rowCount}')" title="Remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }

        function removeRow(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
            // Re-number visible rows
            document.querySelectorAll('#prevPregBody tr td:first-child').forEach((td, i) => {
                td.textContent = i + 1;
            });
        }

        document.getElementById('addPrevPregRow').addEventListener('click', addPrevPregRow);

        // Auto-calculate EDD from LMP (280 days / 40 weeks)
        document.getElementById('last_menstrual_period').addEventListener('change', function () {
            if (this.value) {
                const lmp = new Date(this.value);
                const edd = new Date(lmp.getTime() + (280 * 24 * 60 * 60 * 1000));
                document.getElementById('expected_date_of_delivery').value = edd.toISOString().split('T')[0];
            }
        });

        // Loading state on submit
        document.getElementById('motherRegistrationForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Registering...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

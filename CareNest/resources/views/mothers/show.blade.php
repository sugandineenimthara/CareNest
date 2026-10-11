<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mother->mother_name }} - CareNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --font-main:'Plus Jakarta Sans',sans-serif; --font-heading:'Outfit',sans-serif; --sidebar-width:260px; --bg-gradient:linear-gradient(135deg,#eef2ff 0%,#f0fdf4 50%,#f0f9ff 100%); --text-dark:#1e293b; --text-muted:#64748b; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:var(--font-main); background:var(--bg-gradient); background-attachment:fixed; color:var(--text-dark); min-height:100vh; display:flex; }

        .sidebar { width:var(--sidebar-width); background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); border-right:1px solid rgba(226,232,240,0.8); display:flex; flex-direction:column; padding:30px 20px; position:fixed; top:0; bottom:0; left:0; z-index:100; }
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

        .main-wrapper { margin-left:var(--sidebar-width); flex:1; display:flex; flex-direction:column; min-height:100vh; }
        .top-header { display:flex; justify-content:space-between; align-items:center; padding:20px 48px; background:rgba(255,255,255,0.7); backdrop-filter:blur(16px); border-bottom:1px solid rgba(226,232,240,0.6); position:sticky; top:0; z-index:90; }
        .header-left { font-family:var(--font-heading); font-size:22px; font-weight:800; color:#1b4d3e; display:flex; align-items:center; gap:12px; }
        .back-btn { background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#64748b; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .back-btn:hover { background:#e2e8f0; color:#1b5e20; }
        .header-right { display:flex; align-items:center; gap:14px; }
        .edit-btn { display:flex; align-items:center; gap:8px; padding:9px 18px; border-radius:12px; background:#e8f5e9; color:#1b5e20; border:none; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .edit-btn:hover { background:#c8e6c9; }
        .user-badge-container { display:flex; align-items:center; gap:10px; padding-left:12px; border-left:1px solid #e2e8f0; }
        .user-role-label { font-size:13px; font-weight:700; color:#334155; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#475569; font-size:16px; }

        .content-container { padding:36px 48px; max-width:1200px; width:100%; margin:0 auto; }

        /* Hero Profile Card */
        .profile-hero {
            background:linear-gradient(135deg,#1b5e20 0%,#00695c 60%,#004d40 100%);
            border-radius:28px; padding:36px; margin-bottom:28px;
            color:#fff; display:flex; align-items:center; gap:28px;
            box-shadow:0 16px 48px rgba(27,94,32,0.3); position:relative; overflow:hidden;
        }
        .profile-hero::before { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.05); }
        .profile-hero::after { content:''; position:absolute; right:60px; bottom:-60px; width:150px; height:150px; border-radius:50%; background:rgba(255,255,255,0.04); }
        .hero-avatar { width:80px; height:80px; border-radius:24px; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-family:var(--font-heading); font-size:30px; font-weight:800; color:#fff; flex-shrink:0; border:2px solid rgba(255,255,255,0.2); }
        .hero-info { flex:1; }
        .hero-name { font-family:var(--font-heading); font-size:28px; font-weight:800; margin-bottom:4px; }
        .hero-id { font-size:13px; opacity:0.7; margin-bottom:12px; }
        .hero-tags { display:flex; flex-wrap:wrap; gap:8px; }
        .hero-tag { background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); padding:5px 12px; border-radius:20px; font-size:12px; font-weight:700; display:flex; align-items:center; gap:6px; }
        .hero-stats { display:flex; gap:28px; }
        .hero-stat { text-align:center; }
        .hero-stat-num { font-family:var(--font-heading); font-size:24px; font-weight:800; }
        .hero-stat-label { font-size:11px; opacity:0.7; text-transform:uppercase; letter-spacing:0.8px; margin-top:2px; }

        /* Info Cards Grid */
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px; }
        .info-card { background:rgba(255,255,255,0.92); backdrop-filter:blur(16px); border-radius:24px; padding:28px; border:1px solid rgba(255,255,255,0.8); box-shadow:0 8px 24px rgba(0,0,0,0.05); }
        .card-header { display:flex; align-items:center; gap:12px; margin-bottom:20px; padding-bottom:14px; border-bottom:1.5px solid #f1f5f9; }
        .card-icon { width:40px; height:40px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
        .ci-green { background:#e8f5e9; color:#2e7d32; }
        .ci-teal { background:#e0f7fa; color:#00695c; }
        .ci-blue { background:#e3f2fd; color:#1565c0; }
        .ci-rose { background:#fce4ec; color:#c62828; }
        .ci-amber { background:#fff8e1; color:#f57f17; }
        .ci-purple { background:#ede7f6; color:#6d28d9; }
        .card-title { font-family:var(--font-heading); font-size:17px; font-weight:800; color:#0f172a; }

        .info-rows { display:flex; flex-direction:column; gap:12px; }
        .info-row { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
        .info-row-label { font-size:12px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.6px; flex-shrink:0; }
        .info-row-value { font-size:14px; font-weight:600; color:#334155; text-align:right; }
        .info-divider { height:1px; background:#f8fafc; }

        .bmi-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:800; }
        .bmi-normal { background:#d1fae5; color:#065f46; }
        .bmi-high { background:#fee2e2; color:#991b1b; }
        .bmi-low { background:#fef3c7; color:#92400e; }

        /* Full Width Cards */
        .full-card { background:rgba(255,255,255,0.92); backdrop-filter:blur(16px); border-radius:24px; padding:28px; border:1px solid rgba(255,255,255,0.8); box-shadow:0 8px 24px rgba(0,0,0,0.05); margin-bottom:20px; }

        /* Previous Pregnancy Table */
        .preg-table { width:100%; border-collapse:collapse; }
        .preg-table th { font-size:10px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.8px; padding:8px 14px; text-align:left; border-bottom:1.5px solid #f1f5f9; }
        .preg-table td { padding:12px 14px; font-size:13px; color:#334155; border-bottom:1px solid #f8fafc; }
        .preg-table tr:last-child td { border-bottom:none; }
        .bool-yes { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:6px; background:#d1fae5; color:#065f46; font-size:10px; }
        .bool-no { display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:6px; background:#f1f5f9; color:#94a3b8; font-size:10px; }

        .no-data { text-align:center; padding:32px; color:#94a3b8; font-size:14px; }
        .no-data i { font-size:32px; display:block; margin-bottom:8px; color:#cbd5e1; }

        .flash-success { background:#d1fae5; border:1px solid #6ee7b7; color:#065f46; padding:14px 20px; border-radius:16px; margin-bottom:24px; display:flex; align-items:center; gap:10px; font-weight:600; font-size:14px; }

        @media (max-width:1024px) { .sidebar { width:80px; padding:20px 10px; } .sidebar-brand span, .nav-item span { display:none; } .main-wrapper { margin-left:80px; } .info-grid { grid-template-columns:1fr; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand"><i class="fa-solid fa-leaf" style="color:#00c853;"></i><span>CareNest</span></a>
        <ul class="sidebar-menu">
            <li class="nav-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            @if(Auth::check() && (Auth::user()->role === 'provider' || Auth::user()->role === 'admin'))
            <li class="nav-item"><a href="{{ route('admin.midwife-requests') }}"><i class="fa-solid fa-user-check"></i><span>Midwife Requests</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.midwives.index') }}"><i class="fa-solid fa-user-nurse"></i><span>Manage Midwives</span></a></li>
            @endif
            <li class="nav-item active"><a href="{{ route('admin.mothers.index') }}"><i class="fa-solid fa-user-nurse"></i><span>Mothers</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>


            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">
                <a href="{{ route('admin.mothers.index') }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                Mother Profile
            </div>
            <div class="header-right">
                <a href="{{ route('admin.mothers.edit', $mother->mother_id) }}" class="edit-btn">
                    <i class="fa-solid fa-pen"></i> Edit Record
                </a>
                @include('partials.top-header-user')
            </div>
        </header>

        <main class="content-container">
            @if(session('success'))
                <div class="flash-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
            @endif

            @php
                $initials = collect(explode(' ', $mother->mother_name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
                $latestPregnancy = $mother->pregnancyHistories->last();
            @endphp

            <!-- Hero -->
            <div class="profile-hero">
                <div class="hero-avatar">{{ $initials }}</div>
                <div class="hero-info">
                    <div class="hero-name">{{ $mother->mother_name }}</div>
                    <div class="hero-id">Mother ID #{{ $mother->mother_id }} &nbsp;·&nbsp; Registered {{ $mother->created_at->format('d M Y') }}</div>
                    <div class="hero-tags">
                        <span class="hero-tag"><i class="fa-solid fa-stethoscope"></i> {{ $mother->midwife->midwife_name ?? 'Unassigned' }}</span>
                        <span class="hero-tag"><i class="fa-solid fa-location-dot"></i> {{ $mother->midwife->area->area_name ?? 'No Area' }}</span>
                        @if($mother->phone_no)
                            <span class="hero-tag"><i class="fa-solid fa-phone"></i> {{ $mother->phone_no }}</span>
                        @endif
                        @if($latestPregnancy && $latestPregnancy->expected_date_of_delivery)
                            <span class="hero-tag"><i class="fa-solid fa-calendar-heart"></i> EDD: {{ \Carbon\Carbon::parse($latestPregnancy->expected_date_of_delivery)->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-num">{{ $mother->age ?? '--' }}</div>
                        <div class="hero-stat-label">Age</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">{{ $mother->bmi ?? '--' }}</div>
                        <div class="hero-stat-label">BMI</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">{{ $mother->children->count() }}</div>
                        <div class="hero-stat-label">Children</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">{{ $mother->previousPregnancyHistories->count() }}</div>
                        <div class="hero-stat-label">Prev. Preg.</div>
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="info-grid">
                <!-- Personal Details -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon ci-green"><i class="fa-solid fa-id-card"></i></div>
                        <div class="card-title">Personal Details</div>
                    </div>
                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label">Full Name</span>
                            <span class="info-row-value">{{ $mother->mother_name }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Date of Birth</span>
                            <span class="info-row-value">{{ $mother->date_of_birth ? $mother->date_of_birth->format('d M Y') : '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Phone</span>
                            <span class="info-row-value">{{ $mother->phone_no ?? '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Address</span>
                            <span class="info-row-value" style="max-width:240px;">{{ $mother->address ?? '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Husband</span>
                            <span class="info-row-value">{{ $mother->husband_name ?? '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Husband Occupation</span>
                            <span class="info-row-value">{{ $mother->husband_occupation ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Clinical Measurements -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon ci-teal"><i class="fa-solid fa-weight-scale"></i></div>
                        <div class="card-title">Clinical Measurements</div>
                    </div>
                    <div class="info-rows">
                        <div class="info-row">
                            <span class="info-row-label">Height</span>
                            <span class="info-row-value">{{ $mother->height ? $mother->height . ' cm' : '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Weight</span>
                            <span class="info-row-value">{{ $mother->weight ? $mother->weight . ' kg' : '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">BMI</span>
                            <span class="info-row-value">
                                @if($mother->bmi)
                                    @php $bmi = $mother->bmi; @endphp
                                    <span class="bmi-badge {{ $bmi < 18.5 ? 'bmi-low' : ($bmi > 25 ? 'bmi-high' : 'bmi-normal') }}">
                                        <i class="fa-solid fa-{{ $bmi < 18.5 ? 'arrow-down' : ($bmi > 25 ? 'arrow-up' : 'check') }}"></i>
                                        {{ $bmi }} ({{ $bmi < 18.5 ? 'Underweight' : ($bmi > 30 ? 'Obese' : ($bmi > 25 ? 'Overweight' : 'Normal')) }})
                                    </span>
                                @else
                                    <span style="color:#94a3b8;">—</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Assigned Midwife</span>
                            <span class="info-row-value">{{ $mother->midwife->midwife_name ?? '—' }}</span>
                        </div>
                        <div class="info-divider"></div>
                        <div class="info-row">
                            <span class="info-row-label">Clinic Area</span>
                            <span class="info-row-value">{{ $mother->midwife->area->area_name ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Current Pregnancy -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon ci-blue"><i class="fa-solid fa-baby-carriage"></i></div>
                        <div class="card-title">Current Pregnancy</div>
                    </div>
                    @if($latestPregnancy)
                        <div class="info-rows">
                            <div class="info-row">
                                <span class="info-row-label">Living Children</span>
                                <span class="info-row-value">{{ $latestPregnancy->no_living_children ?? 0 }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">Youngest Child Age</span>
                                <span class="info-row-value">{{ $latestPregnancy->age_of_youngest_child ? $latestPregnancy->age_of_youngest_child . ' yrs' : '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">LMP Date</span>
                                <span class="info-row-value">{{ $latestPregnancy->last_menstrual_period ? $latestPregnancy->last_menstrual_period->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">EDD</span>
                                <span class="info-row-value" style="color:#1b5e20; font-weight:800;">{{ $latestPregnancy->expected_date_of_delivery ? $latestPregnancy->expected_date_of_delivery->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">US Confirmed</span>
                                <span class="info-row-value">{{ $latestPregnancy->date_confirmed_by_US ? $latestPregnancy->date_confirmed_by_US->format('d M Y') : '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">Weeks at Registration</span>
                                <span class="info-row-value">{{ $latestPregnancy->no_of_weeks_pregnant_at_registration ? $latestPregnancy->no_of_weeks_pregnant_at_registration . ' wks' : '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">First Fetal Movement</span>
                                <span class="info-row-value">{{ $latestPregnancy->first_fetal_movements_date ? $latestPregnancy->first_fetal_movements_date->format('d M Y') : '—' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="no-data"><i class="fa-solid fa-notes-medical"></i>No pregnancy history recorded yet.</div>
                    @endif
                </div>

                <!-- Family Health History -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon ci-rose"><i class="fa-solid fa-heart-pulse"></i></div>
                        <div class="card-title">Family Health History</div>
                    </div>
                    @if($mother->familyHealthHistory)
                        @php $fh = $mother->familyHealthHistory; @endphp
                        <div class="info-rows">
                            <div class="info-row">
                                <span class="info-row-label">Diabetes</span>
                                <span class="info-row-value">{{ $fh->diabetes ?? '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">High Blood Pressure</span>
                                <span class="info-row-value">{{ $fh->high_blood_pressure ?? '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">Blood Related Diseases</span>
                                <span class="info-row-value">{{ $fh->blood_related_diseases ?? '—' }}</span>
                            </div>
                            <div class="info-divider"></div>
                            <div class="info-row">
                                <span class="info-row-label">Other</span>
                                <span class="info-row-value">{{ $fh->other ?? '—' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="no-data"><i class="fa-solid fa-heart"></i>No family health history recorded.</div>
                    @endif
                </div>
            </div>

            <!-- Previous Pregnancy History -->
            <div class="full-card">
                <div class="card-header">
                    <div class="card-icon ci-purple" style="width:40px;height:40px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;background:#ede7f6;color:#6d28d9;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="card-title">Previous Pregnancy History</div>
                    <span style="margin-left:auto; background:#ede7f6; color:#6d28d9; border-radius:20px; padding:4px 12px; font-size:12px; font-weight:700;">
                        {{ $mother->previousPregnancyHistories->count() }} record(s)
                    </span>
                </div>
                @if($mother->previousPregnancyHistories->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="preg-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Which Pregnancy</th>
                                    <th>Date of Birth</th>
                                    <th>Birth Weight</th>
                                    <th>Gender</th>
                                    <th>Results</th>
                                    <th>Place</th>
                                    <th>Rubella</th>
                                    <th>Folic Acid</th>
                                    <th>Infertility</th>
                                    <th>Blood Rel. Marriage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mother->previousPregnancyHistories as $i => $prev)
                                    <tr>
                                        <td style="font-weight:800; color:#94a3b8;">{{ $i+1 }}</td>
                                        <td>{{ $prev->which_pregnancy ?? '—' }}</td>
                                        <td>{{ $prev->date_of_birth ? $prev->date_of_birth->format('d M Y') : '—' }}</td>
                                        <td>{{ $prev->birth_weight ? $prev->birth_weight . ' kg' : '—' }}</td>
                                        <td>{{ $prev->gender ?? '—' }}</td>
                                        <td>{{ $prev->results ?? '—' }}</td>
                                        <td>{{ $prev->place_of_pregnancy ?? '—' }}</td>
                                        <td><span class="{{ $prev->rubella_vaccinated ? 'bool-yes' : 'bool-no' }}"><i class="fa-solid fa-{{ $prev->rubella_vaccinated ? 'check' : 'xmark' }}"></i></span></td>
                                        <td><span class="{{ $prev->folic_acid_vaccinated ? 'bool-yes' : 'bool-no' }}"><i class="fa-solid fa-{{ $prev->folic_acid_vaccinated ? 'check' : 'xmark' }}"></i></span></td>
                                        <td><span class="{{ $prev->infertility ? 'bool-yes' : 'bool-no' }}"><i class="fa-solid fa-{{ $prev->infertility ? 'check' : 'xmark' }}"></i></span></td>
                                        <td><span class="{{ $prev->blood_relation_marriage ? 'bool-yes' : 'bool-no' }}"><i class="fa-solid fa-{{ $prev->blood_relation_marriage ? 'check' : 'xmark' }}"></i></span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="no-data"><i class="fa-solid fa-clock-rotate-left"></i>No previous pregnancy records.</div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>

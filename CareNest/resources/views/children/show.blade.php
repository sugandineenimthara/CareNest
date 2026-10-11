<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $child->display_name }} - CareNest</title>
    <meta name="description" content="View child record, linked mother details, and complete immunization schedule in CareNest.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --primary-emerald: #00e676;
            --dark-emerald: #1b5e20;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --sidebar-width: 260px;
            --bg-gradient: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 50%, #f0f9ff 100%);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-main);
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226,232,240,0.8);
            display: flex; flex-direction: column;
            padding: 30px 20px; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            font-family: var(--font-heading); font-size: 24px; font-weight: 800;
            color: #1b4d3e; text-decoration: none; margin-bottom: 36px; padding-left: 10px;
        }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto; }
        .nav-item a {
            display: flex; align-items: center; gap: 14px; padding: 12px 18px;
            border-radius: 16px; font-size: 14px; font-weight: 600; color: #64748b;
            text-decoration: none; transition: all 0.25s ease;
        }
        .nav-item a:hover { background: #f1f5f9; color: var(--text-dark); }
        .nav-item.active a {
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            color: #ffffff; box-shadow: 0 8px 20px rgba(27,94,32,0.25);
        }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer { margin-top: 20px; }
        .logout-btn {
            display: flex; align-items: center; gap: 10px; background: transparent; border: none;
            padding: 10px 18px; font-family: var(--font-main); font-size: 14px; font-weight: 600;
            color: #64748b; cursor: pointer; width: 100%;
        }
        .logout-btn:hover { color: #ef4444; }

        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 48px; background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6); position: sticky; top: 0; z-index: 90;
        }
        .header-left { display: flex; align-items: center; gap: 14px; font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #1b4d3e; }
        .back-btn {
            width: 38px; height: 38px; border-radius: 50%; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            color: #475569; text-decoration: none; transition: all 0.2s ease;
        }
        .back-btn:hover { background: #e2e8f0; color: #1e293b; }
        .header-actions { display: flex; align-items: center; gap: 12px; }
        .btn-action {
            padding: 10px 20px; border-radius: 14px; font-family: var(--font-heading);
            font-size: 13px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-edit { background: white; border: 1.5px solid #cbd5e1; color: #334155; }
        .btn-edit:hover { background: #f8fafc; border-color: #94a3b8; }
        .btn-record-vaccine {
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            border: none; color: white; box-shadow: 0 4px 14px rgba(0,200,83,0.3); cursor: pointer;
        }
        .btn-record-vaccine:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,200,83,0.4); }

        .content-container { padding: 36px 48px; display: flex; flex-direction: column; gap: 28px; }

        .alert-success {
            padding: 16px 20px; background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 14px; color: #065f46; font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }

        /* Hero banner */
        .child-hero {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 24px;
            padding: 28px 36px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04); flex-wrap: wrap; gap: 20px;
        }
        .hero-left { display: flex; align-items: center; gap: 20px; }
        .hero-avatar {
            width: 72px; height: 72px; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; font-weight: 800;
        }
        .hero-male { background: #e0f2fe; color: #0284c7; }
        .hero-female { background: #fce7f3; color: #db2777; }
        .hero-title { font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #1e293b; }
        .hero-tags { display: flex; align-items: center; gap: 10px; margin-top: 6px; flex-wrap: wrap; }
        .tag {
            font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .tag-id { background: #f1f5f9; color: #475569; }
        .tag-area { background: #e0f2fe; color: #0369a1; }
        .tag-midwife { background: #ecfdf5; color: #047857; }

        /* Two column layout */
        .show-layout {
            display: grid; grid-template-columns: 380px 1fr; gap: 28px;
        }
        @media (max-width: 1024px) {
            .show-layout { grid-template-columns: 1fr; }
        }

        .side-col { display: flex; flex-direction: column; gap: 24px; }
        .main-col { display: flex; flex-direction: column; gap: 24px; }

        /* Detail card */
        .info-card {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 20px;
            padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            display: flex; flex-direction: column; gap: 18px;
        }
        .card-header-row {
            display: flex; align-items: center; justify-content: space-between;
            padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;
        }
        .card-title {
            font-family: var(--font-heading); font-size: 16px; font-weight: 800; color: #1e293b;
            display: flex; align-items: center; gap: 10px;
        }
        .card-title i { color: #00c853; font-size: 16px; }

        .info-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 13px; padding: 6px 0; border-bottom: 1px dashed #f1f5f9;
        }
        .info-row:last-child { border-bottom: none; }
        .info-lbl { color: var(--text-muted); font-weight: 600; }
        .info-val { color: var(--text-dark); font-weight: 700; text-align: right; }

        /* Linked Mother Highlight Card */
        .mother-highlight-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1.5px solid #a7f3d0; border-radius: 20px;
            padding: 24px; box-shadow: 0 4px 20px rgba(0,200,83,0.08);
            display: flex; flex-direction: column; gap: 16px;
        }
        .mother-header { display: flex; align-items: center; gap: 12px; }
        .mother-icon {
            width: 44px; height: 44px; border-radius: 14px; background: white;
            color: #047857; display: flex; align-items: center; justify-content: center;
            font-size: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }
        .mother-name { font-family: var(--font-heading); font-size: 17px; font-weight: 800; color: #064e3b; }
        .mother-id-tag { font-size: 12px; color: #047857; font-weight: 600; }
        .btn-view-mother {
            width: 100%; padding: 10px; border-radius: 12px; background: white;
            border: 1px solid #86efac; color: #065f46; font-family: var(--font-heading);
            font-size: 13px; font-weight: 700; text-align: center; text-decoration: none;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-view-mother:hover { background: #047857; color: white; border-color: #047857; }

        /* Immunization Schedule Progress & Timeline */
        .progress-box {
            background: white; border: 1px solid #e2e8f0; border-radius: 16px;
            padding: 16px 20px; display: flex; flex-direction: column; gap: 10px;
        }
        .progress-meta { display: flex; justify-content: space-between; font-size: 13px; font-weight: 700; }
        .progress-bar-bg { width: 100%; height: 10px; background: #e2e8f0; border-radius: 20px; overflow: hidden; }
        .progress-bar-fill {
            height: 100%; background: linear-gradient(90deg, #00e676, #00c853);
            border-radius: 20px; transition: width 0.5s ease;
        }

        .vaccine-list { display: flex; flex-direction: column; gap: 12px; }
        .vaccine-item {
            background: white; border: 1px solid #e2e8f0; border-radius: 16px;
            padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
            gap: 16px; transition: all 0.2s ease;
        }
        .vaccine-item:hover { border-color: #cbd5e1; box-shadow: 0 4px 14px rgba(0,0,0,0.03); }
        .vac-left { display: flex; align-items: center; gap: 14px; }
        .vac-icon {
            width: 40px; height: 40px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 16px;
        }
        .vac-done-icon { background: #dcfce7; color: #16a34a; }
        .vac-due-icon { background: #fee2e2; color: #dc2626; }
        .vac-up-icon { background: #f1f5f9; color: #64748b; }

        .vac-title { font-family: var(--font-heading); font-size: 15px; font-weight: 800; color: #1e293b; }
        .vac-sub { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .vac-right { display: flex; align-items: center; gap: 14px; text-align: right; }
        .badge-status {
            padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
        }
        .status-completed { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .status-overdue { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
        .status-due { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-upcoming { background: #f1f5f9; color: #64748b; }

        .btn-log-dose {
            padding: 6px 14px; border-radius: 10px; background: #00c853; color: white;
            border: none; font-size: 12px; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; gap: 6px; text-decoration: none;
        }
        .btn-log-dose:hover { background: #009624; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15,23,42,0.6); backdrop-filter: blur(4px);
            z-index: 1000; align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-card {
            background: white; border-radius: 24px; padding: 32px; width: 100%;
            max-width: 520px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            display: flex; flex-direction: column; gap: 20px;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;
        }
        .modal-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #1e293b; }
        .close-btn {
            background: transparent; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;
        }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 13px; font-weight: 700; color: #334155; }
        .form-input, .form-select {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 12px;
            font-family: var(--font-main); font-size: 14px;
        }
        .form-input:focus, .form-select:focus {
            border-color: #00e676; outline: none; box-shadow: 0 0 0 3px rgba(0,230,118,0.15);
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
            <li class="nav-item"><a href="{{ route('dashboard') }}"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a></li>
            @if(Auth::check() && (Auth::user()->role === 'provider' || Auth::user()->role === 'admin'))
            <li class="nav-item"><a href="{{ route('admin.midwife-requests') }}"><i class="fa-solid fa-user-check"></i><span>Midwife Requests</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.midwives.index') }}"><i class="fa-solid fa-user-nurse"></i><span>Manage Midwives</span></a></li>
            @endif
            <li class="nav-item"><a href="{{ route('admin.mothers.index') }}"><i class="fa-solid fa-user-nurse"></i><span>Mothers</span></a></li>
            <li class="nav-item active"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>
            <li class="nav-item"><a href="#lab-tests"><i class="fa-solid fa-vial-circle-check"></i><span>Lab Tests</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">
                <a href="{{ route('admin.children.index') }}" class="back-btn" title="Back to Children">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span>Child Profile &amp; Immunization</span>
            </div>
            <div class="header-right" style="display: flex; align-items: center; gap: 16px;">
                <div class="header-actions">
                    <a href="{{ route('admin.children.edit', $child->child_id) }}" class="btn-action btn-edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Child</span>
                    </a>
                    <button type="button" class="btn-action btn-record-vaccine" onclick="openVaccineModal('', '')">
                        <i class="fa-solid fa-syringe"></i>
                        <span>Record Vaccine Dose</span>
                    </button>
                </div>
                @include('partials.top-header-user')
            </div>
        </header>

        <div class="content-container">
            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Hero Banner -->
            <div class="child-hero">
                <div class="hero-left">
                    <div class="hero-avatar {{ $child->gender === 'Female' ? 'hero-female' : 'hero-male' }}">
                        <i class="fa-solid {{ $child->gender === 'Female' ? 'fa-venus' : 'fa-mars' }}"></i>
                    </div>
                    <div>
                        <h1 class="hero-title">{{ $child->display_name }}</h1>
                        <div class="hero-tags">
                            <span class="tag tag-id"><i class="fa-solid fa-fingerprint"></i> Child ID: #{{ $child->child_id }}</span>
                            <span class="tag tag-area"><i class="fa-solid fa-hospital"></i> Area: {{ $child->area ? $child->area->area_name : ($child->midwife?->area?->area_name ?? 'N/A') }}</span>
                            <span class="tag tag-midwife"><i class="fa-solid fa-user-nurse"></i> Midwife: {{ $child->midwife ? $child->midwife->midwife_name : 'N/A' }}</span>
                            <span class="tag" style="background:#f1f5f9; color:#334155;"><i class="fa-solid fa-cake-candles"></i> {{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }} ({{ $child->age }})</span>
                        </div>
                    </div>
                </div>

                <div>
                    @if($child->bcg_vaccinated_at_birth)
                        <span class="badge-status status-completed" style="font-size:13px; padding:6px 16px;">
                            <i class="fa-solid fa-shield-virus"></i> BCG Vaccinated (&lt;24hrs)
                        </span>
                    @else
                        <span class="badge-status status-due" style="font-size:13px; padding:6px 16px;">
                            <i class="fa-solid fa-clock"></i> BCG Pending
                        </span>
                    @endif
                </div>
            </div>

            <!-- 2-Column Content -->
            <div class="show-layout">
                <!-- Left Column -->
                <div class="side-col">
                    <!-- LINKED MOTHER DETAILS CARD (REQUIRED) -->
                    <div class="mother-highlight-card">
                        <div class="mother-header">
                            <div class="mother-icon"><i class="fa-solid fa-person-breastfeeding"></i></div>
                            <div>
                                <div class="mother-id-tag">LINKED MOTHER RECORD</div>
                                <div class="mother-name">{{ $child->mother ? $child->mother->mother_name : 'Unknown Mother' }}</div>
                            </div>
                        </div>

                        @if($child->mother)
                            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                                <div class="info-row" style="border-color:#bbf7d0;">
                                    <span class="info-lbl" style="color:#065f46;">Mother Unique ID</span>
                                    <span class="info-val" style="color:#064e3b;">#{{ $child->mother->mother_id }}</span>
                                </div>
                                <div class="info-row" style="border-color:#bbf7d0;">
                                    <span class="info-lbl" style="color:#065f46;">Phone Number</span>
                                    <span class="info-val" style="color:#064e3b;">{{ $child->mother->phone_no ?? 'N/A' }}</span>
                                </div>
                                <div class="info-row" style="border-color:#bbf7d0;">
                                    <span class="info-lbl" style="color:#065f46;">Mother Age / BMI</span>
                                    <span class="info-val" style="color:#064e3b;">{{ $child->mother->age ?? 'N/A' }} yrs / BMI {{ $child->mother->bmi ?? 'N/A' }}</span>
                                </div>
                                <div class="info-row" style="border-color:#bbf7d0;">
                                    <span class="info-lbl" style="color:#065f46;">Address</span>
                                    <span class="info-val" style="color:#064e3b;">{{ $child->mother->address ?? 'N/A' }}</span>
                                </div>
                                <div class="info-row" style="border-color:#bbf7d0;">
                                    <span class="info-lbl" style="color:#065f46;">Assigned Midwife</span>
                                    <span class="info-val" style="color:#064e3b;">{{ $child->mother->midwife ? $child->mother->midwife->midwife_name : 'N/A' }}</span>
                                </div>
                            </div>

                            <a href="{{ route('admin.mothers.show', $child->mother_id) }}" class="btn-view-mother">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                <span>Open Full Mother Profile</span>
                            </a>
                        @endif
                    </div>

                    <!-- Child Measurements Card -->
                    <div class="info-card">
                        <div class="card-header-row">
                            <h2 class="card-title"><i class="fa-solid fa-ruler-combined"></i> Birth Measurements</h2>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Birth Weight</span>
                            <span class="info-val">{{ $child->birth_weight ? $child->birth_weight . ' kg' : 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Birth Length</span>
                            <span class="info-val">{{ $child->birth_length ? $child->birth_length . ' cm' : 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">Gender</span>
                            <span class="info-val">{{ $child->gender }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">BCG Batch No</span>
                            <span class="info-val">{{ $child->bcg_batch_no ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-lbl">BCG Vaccination Date</span>
                            <span class="info-val">{{ $child->bcg_vaccinated_date ? \Carbon\Carbon::parse($child->bcg_vaccinated_date)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Clinical Health Details -->
                    <div class="info-card">
                        <div class="card-header-row">
                            <h2 class="card-title"><i class="fa-solid fa-stethoscope"></i> Clinical Observations</h2>
                        </div>
                        <p style="font-size:13px; color:#475569; line-height:1.6;">
                            {{ $child->health_details ?: 'No special health complications or birth defects recorded.' }}
                        </p>
                    </div>
                </div>

                <!-- Right Column: Immunization Schedule -->
                <div class="main-col">
                    <div class="info-card">
                        <div class="card-header-row">
                            <div>
                                <h2 class="card-title"><i class="fa-solid fa-shield-virus"></i> National Immunization Schedule</h2>
                                <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                                    Track required milestones from birth up to 5 years (Sri Lanka MOH EPI protocol)
                                </p>
                            </div>
                            <button type="button" class="btn-action btn-record-vaccine" onclick="openVaccineModal('', '')">
                                <i class="fa-solid fa-plus"></i> Record Dose
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        @php
                            $completedDoses = collect($scheduleStatus)->where('status', 'Completed')->count();
                            $totalDoses = count($scheduleStatus);
                            $percent = round(($completedDoses / $totalDoses) * 100);
                        @endphp
                        <div class="progress-box">
                            <div class="progress-meta">
                                <span>Vaccination Milestone Completion</span>
                                <span style="color:#00c853;">{{ $completedDoses }} of {{ $totalDoses }} Completed ({{ $percent }}%)</span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>

                        <!-- Vaccine Milestones Timeline -->
                        <div class="vaccine-list">
                            @foreach($scheduleStatus as $item)
                                <div class="vaccine-item">
                                    <div class="vac-left">
                                        <div class="vac-icon {{ $item['status'] === 'Completed' ? 'vac-done-icon' : ($item['status'] === 'Overdue' ? 'vac-due-icon' : 'vac-up-icon') }}">
                                            @if($item['status'] === 'Completed')
                                                <i class="fa-solid fa-check"></i>
                                            @elseif($item['status'] === 'Overdue')
                                                <i class="fa-solid fa-triangle-exclamation"></i>
                                            @elseif($item['status'] === 'Due Now')
                                                <i class="fa-solid fa-clock"></i>
                                            @else
                                                <i class="fa-solid fa-calendar"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="vac-title">{{ $item['vaccine_name'] }} ({{ $item['dose'] }})</div>
                                            <div class="vac-sub">
                                                <span>{{ $item['description'] }}</span> •
                                                <span style="font-weight:700; color:#334155;">{{ $item['target_period'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="vac-right">
                                        @if($item['status'] === 'Completed' && $item['record'])
                                            <div>
                                                <span class="badge-status status-completed">
                                                    <i class="fa-solid fa-circle-check"></i> Completed
                                                </span>
                                                <div style="font-size:11px; color:#475569; margin-top:3px;">
                                                    Batch: <strong>{{ $item['record']->batch_no }}</strong> • {{ \Carbon\Carbon::parse($item['record']->immunization_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                        @elseif($item['status'] === 'Overdue')
                                            <div>
                                                <span class="badge-status status-overdue">Overdue</span>
                                                <div style="margin-top:4px;">
                                                    <button type="button" class="btn-log-dose" onclick="openVaccineModal('{{ $item['vaccine_name'] }}', '{{ $item['dose'] }}')">
                                                        <i class="fa-solid fa-syringe"></i> Record Now
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif($item['status'] === 'Due Now')
                                            <div>
                                                <span class="badge-status status-due">Due Now</span>
                                                <div style="margin-top:4px;">
                                                    <button type="button" class="btn-log-dose" onclick="openVaccineModal('{{ $item['vaccine_name'] }}', '{{ $item['dose'] }}')">
                                                        <i class="fa-solid fa-syringe"></i> Record
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <div>
                                                <span class="badge-status status-upcoming">Due ~ {{ \Carbon\Carbon::parse($item['due_date'])->format('M Y') }}</span>
                                                <div style="margin-top:4px;">
                                                    <button type="button" class="btn-log-dose" style="background:#64748b;" onclick="openVaccineModal('{{ $item['vaccine_name'] }}', '{{ $item['dose'] }}')">
                                                        Log Early
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Vaccine Dose Modal -->
    <div class="modal-overlay" id="vaccineModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-syringe" style="color:#00c853;"></i> Record Vaccine Dose</h3>
                <button type="button" class="close-btn" onclick="closeVaccineModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('admin.immunizations.child.store') }}" method="POST">
                @csrf
                <input type="hidden" name="child_id" value="{{ $child->child_id }}">

                <div class="form-group">
                    <label class="form-label">Child</label>
                    <input type="text" class="form-input" value="{{ $child->display_name }} (ID: #{{ $child->child_id }})" readonly style="background:#f1f5f9;">
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="modal_vaccine_name">Vaccine Name *</label>
                    <select name="vaccine_name" id="modal_vaccine_name" class="form-select" required>
                        <option value="">-- Choose Vaccine --</option>
                        <option value="BCG">BCG (Birth, within 24 hrs)</option>
                        <option value="Penta 1">Penta 1 (2 Months)</option>
                        <option value="Polio 1 (OPV)">Polio 1 - OPV (2 Months)</option>
                        <option value="FITV 1">FITV 1 (4 Months)</option>
                        <option value="Penta 2">Penta 2 (6 Months)</option>
                        <option value="Polio 2 (OPV)">Polio 2 - OPV (6 Months)</option>
                        <option value="MMR 1">MMR 1 (9 Months)</option>
                        <option value="FITV 2">FITV 2 (9 Months)</option>
                        <option value="JE">JE (12 Months / 1 Year)</option>
                        <option value="DPT">DPT Booster (18 Months / 1.5 Years)</option>
                        <option value="Polio 3 (OPV)">Polio 3 - OPV (18 Months / 1.5 Years)</option>
                        <option value="MMR 2">MMR 2 Booster (3 Years)</option>
                        <option value="DT">DT Booster (5 Years)</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="modal_batch_no">Vaccine Batch Number *</label>
                    <input type="text" name="batch_no" id="modal_batch_no" class="form-input" placeholder="e.g. VAC-2026-B981" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="modal_immunization_date">Date Vaccinated *</label>
                    <input type="date" name="immunization_date" id="modal_immunization_date" class="form-input" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="modal_midwife_id">Administering Midwife</label>
                    <select name="midwife_id" id="modal_midwife_id" class="form-select">
                        @foreach($midwives as $mw)
                            <option value="{{ $mw->midwife_id }}" {{ $child->midwife_id == $mw->midwife_id ? 'selected' : '' }}>
                                {{ $mw->midwife_name }} ({{ $mw->area ? $mw->area->area_name : 'No Area' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="modal_remarks">Remarks / Observations</label>
                    <input type="text" name="remarks" id="modal_remarks" class="form-input" placeholder="e.g. Well tolerated, right arm anterolateral...">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                    <button type="button" class="btn-action btn-edit" onclick="closeVaccineModal()">Cancel</button>
                    <button type="submit" class="btn-action btn-record-vaccine">
                        <i class="fa-solid fa-check"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openVaccineModal(vaccineName, dose) {
            const modal = document.getElementById('vaccineModal');
            const select = document.getElementById('modal_vaccine_name');
            if (vaccineName) {
                // Find matching option
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value.toLowerCase().includes(vaccineName.toLowerCase().split(' ')[0])) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
            modal.classList.add('open');
        }

        function closeVaccineModal() {
            document.getElementById('vaccineModal').classList.remove('open');
        }

        // Close on clicking overlay
        document.getElementById('vaccineModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVaccineModal();
            }
        });
    </script>
</body>
</html>

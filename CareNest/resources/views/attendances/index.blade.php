<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Attendances - CareNest</title>
    <meta name="description" content="Manage clinic attendance for mother clinics and pediatric clinics. Track total attendances per clinic.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --primary: #00c853;
            --primary-dark: #1b5e20;
            --secondary: #00695c;
            --mother-purple: #7c3aed;
            --mother-bg: #ede9fe;
            --child-blue: #1d4ed8;
            --child-bg: #dbeafe;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --sidebar-width: 260px;
            --bg-gradient: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 50%, #f0f9ff 100%);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-main); background: var(--bg-gradient); background-attachment: fixed; color: var(--text-dark); min-height: 100vh; display: flex; }

        /* ── Sidebar ── */
        .sidebar { width: var(--sidebar-width); background: rgba(255,255,255,0.95); backdrop-filter: blur(20px); border-right: 1px solid rgba(226,232,240,0.8); display: flex; flex-direction: column; padding: 30px 20px; position: fixed; top: 0; bottom: 0; left: 0; z-index: 100; }
        .sidebar-brand { display: flex; align-items: center; gap: 10px; font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #1b4d3e; text-decoration: none; margin-bottom: 36px; padding-left: 10px; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto; }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .nav-item a { display: flex; align-items: center; gap: 14px; padding: 12px 18px; border-radius: 16px; font-size: 14px; font-weight: 600; color: #64748b; text-decoration: none; transition: all 0.25s ease; }
        .nav-item a:hover { background: #f1f5f9; color: var(--text-dark); }
        .nav-item.active a { background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%); color: #fff; box-shadow: 0 8px 20px rgba(27,94,32,0.25); }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer { margin-top: 20px; display: flex; flex-direction: column; gap: 12px; }
        .logout-btn { display: flex; align-items: center; gap: 10px; background: transparent; border: none; padding: 10px 18px; font-family: var(--font-main); font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer; width: 100%; }
        .logout-btn:hover { color: #ef4444; }

        /* ── Main ── */
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .top-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 48px; min-height: 76px; box-sizing: border-box; background: rgba(255,255,255,0.7); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(226,232,240,0.6); position: sticky; top: 0; z-index: 90; }
        .header-left { font-family: var(--font-heading); font-size: 22px; font-weight: 800; color: #1b4d3e; display: flex; align-items: center; gap: 12px; }
        .header-right { display: flex; align-items: center; gap: 16px; margin-left: auto; }
        .user-badge { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #334155; }

        .content { padding: 32px 48px; display: flex; flex-direction: column; gap: 28px; }

        /* ── Alerts ── */
        .alert { padding: 14px 20px; border-radius: 14px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

        /* ── Stats ── */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 22px; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
        .stat-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .stat-val { font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: var(--text-dark); }
        .stat-lbl { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px; margin-top: 2px; }

        /* ── Clinic Entry Card ── */
        .entry-card { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .entry-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .entry-title-wrap { display: flex; align-items: center; gap: 14px; }
        .entry-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #00c853, #00695c); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; }
        .entry-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: var(--text-dark); }
        .entry-sub { font-size: 13px; color: var(--text-muted); }

        /* Clinic Type Selector Pills */
        .type-selector { display: flex; gap: 10px; background: #f1f5f9; padding: 5px; border-radius: 14px; }
        .type-btn { padding: 9px 20px; border-radius: 10px; border: none; font-family: var(--font-main); font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px; color: var(--text-muted); background: transparent; }
        .type-btn.active.mother { background: var(--mother-purple); color: white; box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
        .type-btn.active.pediatric { background: var(--child-blue); color: white; box-shadow: 0 4px 12px rgba(29,78,216,0.3); }

        /* Clinic Parameters Bar */
        .clinic-bar { display: grid; grid-template-columns: 180px 1.5fr 1fr auto; gap: 16px; align-items: end; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 24px; }
        .form-group label { font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; }
        .form-input, .form-select { width: 100%; padding: 11px 14px; background: white; border: 1.5px solid #e2e8f0; border-radius: 12px; font-family: var(--font-main); font-size: 14px; color: var(--text-dark); outline: none; transition: border-color 0.2s; }
        .form-input:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,200,83,0.12); }

        /* Total Attendance Live Counter Banner */
        .count-banner { display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #f0fdf4, #e0f2fe); border: 1.5px solid #bbf7d0; border-radius: 16px; padding: 16px 24px; margin-bottom: 20px; }
        .count-left { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: #1b4d3e; }
        .count-badge { display: inline-flex; align-items: center; gap: 8px; background: #065f46; color: white; font-family: var(--font-heading); font-size: 20px; font-weight: 800; padding: 6px 18px; border-radius: 12px; box-shadow: 0 4px 12px rgba(6,95,70,0.25); }

        /* Batch Table */
        .batch-table-wrapper { border: 1.5px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 18px; }
        .batch-table { width: 100%; border-collapse: collapse; }
        .batch-table thead th { background: linear-gradient(135deg, #1b5e20, #00695c); color: white; padding: 13px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; text-align: left; }
        .batch-table thead th.mother-theme { background: linear-gradient(135deg, #5b21b6, #7c3aed); }
        .batch-table thead th.pediatric-theme { background: linear-gradient(135deg, #1e3a8a, #2563eb); }
        .batch-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .batch-table tbody tr:hover { background: #f8fafc; }
        .batch-table tbody tr:last-child { border-bottom: none; }
        .batch-table td { padding: 12px 14px; }
        .td-num { text-align: center; font-weight: 700; color: var(--text-muted); font-size: 13px; width: 45px; }
        .td-select, .td-input { width: 100%; padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: var(--font-main); font-size: 13px; outline: none; background: white; transition: border-color 0.2s; }
        .td-select:focus, .td-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,200,83,0.1); }
        .btn-del-row { background: #fee2e2; border: none; color: #dc2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .btn-del-row:hover { background: #fecaca; transform: scale(1.1); }

        /* Actions */
        .table-actions { display: flex; align-items: center; gap: 14px; }
        .btn-add-row { display: flex; align-items: center; gap: 8px; padding: 11px 22px; background: #f0fdf4; border: 1.5px dashed #86efac; border-radius: 12px; color: #16a34a; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; }
        .btn-add-row:hover { background: #dcfce7; border-color: var(--primary); }
        .btn-save { display: flex; align-items: center; gap: 10px; padding: 12px 30px; background: linear-gradient(135deg, #00c853, #00a843); border: none; border-radius: 14px; color: white; font-family: var(--font-heading); font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 20px rgba(0,200,83,0.35); transition: all 0.25s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,200,83,0.45); }

        /* ── History Section ── */
        .section-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .section-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }

        /* Filter bar */
        .filter-bar { display: flex; gap: 12px; align-items: center; background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 20px; margin-bottom: 20px; }
        .filter-bar input, .filter-bar select { padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: var(--font-main); font-size: 13px; outline: none; }
        .filter-bar input:focus, .filter-bar select:focus { border-color: var(--primary); }
        .btn-filter { padding: 9px 18px; background: var(--primary-dark); color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; }

        /* Clinic Session History Cards */
        .session-card { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.04); margin-bottom: 18px; }
        .session-card-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid #f1f5f9; }
        .session-card-head.mother-session { background: linear-gradient(135deg, #faf5ff, #f3e8ff); border-bottom-color: #e9d5ff; }
        .session-card-head.pediatric-session { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-bottom-color: #bfdbfe; }
        .session-card-title { font-family: var(--font-heading); font-size: 17px; font-weight: 800; }
        .session-card-sub { font-size: 13px; color: var(--text-muted); margin-top: 2px; }

        .session-pills { display: flex; align-items: center; gap: 12px; }
        .clinic-type-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 10px; font-size: 12px; font-weight: 700; }
        .clinic-type-badge.mother { background: var(--mother-bg); color: var(--mother-purple); }
        .clinic-type-badge.pediatric { background: var(--child-bg); color: var(--child-blue); }

        .total-count-pill { display: inline-flex; align-items: center; gap: 7px; background: white; border: 1.5px solid #cbd5e1; padding: 5px 14px; border-radius: 10px; font-family: var(--font-heading); font-size: 14px; font-weight: 800; color: #1e293b; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }

        .session-card-body { padding: 20px 24px; }
        .hist-table { width: 100%; border-collapse: collapse; }
        .hist-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--text-muted); padding: 10px 14px; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .hist-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f8fafc; }
        .hist-table tr:last-child td { border-bottom: none; }

        .btn-del-record { background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 14px; padding: 4px 8px; border-radius: 6px; transition: all 0.2s; }
        .btn-del-record:hover { color: #ef4444; background: #fee2e2; }

        .empty-state { text-align: center; padding: 60px 24px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; color: var(--primary); }
        .empty-state p { font-size: 15px; font-weight: 600; }
    </style>
</head>
<body>

<!-- ═══════════════════════════ SIDEBAR ═══════════════════════════ -->
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
        <li class="nav-item"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
        <li class="nav-item"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
        <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>
        <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-box-open"></i><span>Thriposha Book</span></a></li>
        <li class="nav-item active"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
        <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
    </ul>
</aside>

<!-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ -->
<div class="main-wrapper">
    <!-- Header -->
    <header class="top-header">
        <div class="header-left">
            <i class="fa-solid fa-calendar-check" style="color:#00c853;"></i>
            Clinic Attendances Management
        </div>
        <div class="header-right">
            @include('partials.top-header-user')
        </div>
    </header>

    <div class="content">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i>{{ session('error') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Stats Grid --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-calendar-check"></i></div>
                <div>
                    <div class="stat-val">{{ $totalClinicsEver }}</div>
                    <div class="stat-lbl">Clinic Sessions Held</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#e0f2fe; color:#0369a1;"><i class="fa-solid fa-users"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalAttendancesEver) }}</div>
                    <div class="stat-lbl">Total Attendances</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--mother-bg); color:var(--mother-purple);"><i class="fa-solid fa-person-dress"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalMotherAttendances) }}</div>
                    <div class="stat-lbl">Mother Clinic Attendances</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--child-bg); color:var(--child-blue);"><i class="fa-solid fa-baby"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalPediatricAttendances) }}</div>
                    <div class="stat-lbl">Pediatric Clinic Attendances</div>
                </div>
            </div>
        </div>

        {{-- ══════════════ RECORD CLINIC ATTENDANCE FORM ══════════════ --}}
        <div class="entry-card">
            <div class="entry-header">
                <div class="entry-title-wrap">
                    <div class="entry-icon"><i class="fa-solid fa-clipboard-user"></i></div>
                    <div>
                        <div class="entry-title">Record Clinic Attendance</div>
                        <div class="entry-sub">Select the clinic type (Mother or Pediatric), specify the clinic details, and add all participating attendees.</div>
                    </div>
                </div>

                {{-- Clinic Type Toggle --}}
                <div class="type-selector">
                    <button type="button" class="type-btn active mother" id="btn-type-mother" onclick="switchClinicType('mother')">
                        <i class="fa-solid fa-person-dress"></i> Mother Clinic
                    </button>
                    <button type="button" class="type-btn pediatric" id="btn-type-pediatric" onclick="switchClinicType('pediatric')">
                        <i class="fa-solid fa-baby"></i> Pediatric Clinic
                    </button>
                </div>
            </div>

            <form id="attendanceBatchForm" method="POST" action="{{ route('admin.attendances.store.batch') }}">
                @csrf

                {{-- Hidden input for clinic type --}}
                <input type="hidden" name="clinic_type" id="clinic_type_input" value="mother">

                {{-- Clinic Parameters Bar --}}
                <div class="clinic-bar">
                    <div class="form-group">
                        <label for="clinic_date"><i class="fa-solid fa-calendar-day"></i> Clinic Date <span style="color:#ef4444;">*</span></label>
                        <input type="date" id="clinic_date" name="clinic_date" class="form-input"
                               value="{{ old('clinic_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="clinic_name"><i class="fa-solid fa-hospital"></i> Clinic Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="clinic_name" name="clinic_name" class="form-input" list="clinic-suggestions"
                               placeholder="e.g. MOH Maternity Care Clinic" value="{{ old('clinic_name', 'MOH Maternity Care Clinic') }}" required>
                        <datalist id="clinic-suggestions">
                            <option value="MOH Maternity Care Clinic">
                            <option value="Antenatal & Postnatal Care Clinic">
                            <option value="Well Baby & Child Development Clinic">
                            <option value="Pediatric Immunization & Growth Clinic">
                            <option value="Community Child Health Clinic">
                            <option value="Field Health Center Clinic">
                        </datalist>
                    </div>

                    <div class="form-group">
                        <label for="midwife_id"><i class="fa-solid fa-user-nurse"></i> Conducting Midwife</label>
                        <select id="midwife_id" name="midwife_id" class="form-select">
                            <option value="">— Select Midwife / Area —</option>
                            @foreach($midwives as $mw)
                            <option value="{{ $mw->midwife_id }}">
                                {{ $mw->name }} ({{ $mw->area?->area_name ?? 'Area ' . $mw->area_id }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button type="button" class="btn-add-row" onclick="addAttendeeRow()" style="height:45px; margin-top:20px;">
                            <i class="fa-solid fa-plus"></i> Add Attendee
                        </button>
                    </div>
                </div>

                {{-- Total Attendance Live Counter --}}
                <div class="count-banner">
                    <div class="count-left">
                        <i class="fa-solid fa-users-viewfinder" style="font-size:22px; color:#16a34a;"></i>
                        <div>
                            <div style="font-size:15px; font-weight:800; color:#1e293b;" id="counter-heading">
                                Total Attendance for this Mother Clinic:
                            </div>
                            <div style="font-size:12px; color:#64748b; font-weight:600;">
                                Every participant row added below will be counted and saved for this clinic.
                            </div>
                        </div>
                    </div>
                    <div class="count-badge">
                        <span id="live-attendance-count">0</span>
                        <span style="font-size:14px; font-weight:600; opacity:0.9;">Attendees</span>
                    </div>
                </div>

                {{-- Attendees Table --}}
                <div class="batch-table-wrapper">
                    <table class="batch-table">
                        <thead>
                            <tr id="table-head-row">
                                <th style="width:45px;">#</th>
                                <th id="participant-col-heading" style="width:40%;">Participated Mother Name</th>
                                <th>Remarks / Checkup Notes</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="attendeeRows">
                            {{-- Dynamically populated rows --}}
                        </tbody>
                    </table>
                </div>

                <div class="table-actions">
                    <button type="button" class="btn-add-row" onclick="addAttendeeRow()">
                        <i class="fa-solid fa-plus"></i> <span id="add-btn-label">Add Another Mother</span>
                    </button>
                    <div style="flex:1;"></div>
                    <button type="submit" class="btn-save" id="saveAttendanceBtn" disabled>
                        <i class="fa-solid fa-floppy-disk"></i> Save Clinic Attendance (<span id="btn-count">0</span>)
                    </button>
                </div>
            </form>
        </div>

        {{-- ══════════════ CLINIC ATTENDANCE HISTORY ══════════════ --}}
        <div>
            <div class="section-header-row">
                <div class="section-title">
                    <i class="fa-solid fa-clock-rotate-left" style="color:#00c853;"></i>
                    Clinic Attendance Records & Histories
                </div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.attendances.index') }}" class="filter-bar">
                <i class="fa-solid fa-magnifying-glass" style="color:#94a3b8;"></i>
                <input type="text" name="search" placeholder="Search by participant name, clinic name, or notes..." value="{{ $search }}" style="flex:1;">

                <select name="type">
                    <option value="">All Clinic Types</option>
                    <option value="mother" {{ $clinicType === 'mother' ? 'selected' : '' }}>Mother Clinics</option>
                    <option value="pediatric" {{ $clinicType === 'pediatric' ? 'selected' : '' }}>Pediatric Clinics</option>
                </select>

                <input type="date" name="date_from" value="{{ $dateFrom }}" placeholder="From">
                <input type="date" name="date_to" value="{{ $dateTo }}" placeholder="To">

                <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
                @if($search || $clinicType || $dateFrom || $dateTo)
                <a href="{{ route('admin.attendances.index') }}" style="font-size:13px; color:#64748b; font-weight:600; text-decoration:none;">Clear</a>
                @endif
            </form>

            @if($clinicSessions->isEmpty())
            <div class="session-card">
                <div class="empty-state">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <p>No clinic attendance sessions found.</p>
                    <p style="font-size:13px; margin-top:6px;">Use the form above to record attendances for a mother or pediatric clinic.</p>
                </div>
            </div>
            @else
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($clinicSessions as $idx => $session)
                <div class="session-card">
                    {{-- Card Header --}}
                    <div class="session-card-head {{ $session['clinic_type'] === 'mother' ? 'mother-session' : 'pediatric-session' }}" onclick="toggleClinicSession({{ $idx }})">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div style="font-size:24px; color:{{ $session['clinic_type'] === 'mother' ? 'var(--mother-purple)' : 'var(--child-blue)' }};">
                                <i class="fa-solid {{ $session['clinic_type'] === 'mother' ? 'fa-hospital-user' : 'fa-baby-carriage' }}"></i>
                            </div>
                            <div>
                                <div class="session-card-title">{{ $session['clinic_name'] }}</div>
                                <div class="session-card-sub">
                                    <i class="fa-solid fa-calendar-day" style="margin-right:4px;"></i>
                                    {{ $session['clinic_date']->format('l, d F Y') }}
                                    @if($session['midwife'])
                                    &nbsp;•&nbsp; <i class="fa-solid fa-user-nurse"></i> {{ $session['midwife']->name }} ({{ $session['midwife']->area?->area_name ?? 'Area ' . $session['midwife']->area_id }})
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="session-pills">
                            {{-- Clinic Type Badge --}}
                            <span class="clinic-type-badge {{ $session['clinic_type'] === 'mother' ? 'mother' : 'pediatric' }}">
                                <i class="fa-solid {{ $session['clinic_type'] === 'mother' ? 'fa-person-dress' : 'fa-baby' }}"></i>
                                {{ $session['clinic_type'] === 'mother' ? 'Mother Clinic' : 'Pediatric Clinic' }}
                            </span>

                            {{-- Total Attendances Counter (Requirement: "the no of total attendances should be counted for a particular clinic") --}}
                            <span class="total-count-pill" title="Total attendances recorded for this clinic">
                                <i class="fa-solid fa-users" style="color:{{ $session['clinic_type'] === 'mother' ? 'var(--mother-purple)' : 'var(--child-blue)' }};"></i>
                                <span>{{ $session['total_attendance'] }} Attendances</span>
                            </span>

                            <i class="fa-solid fa-chevron-down" id="chevron-session-{{ $idx }}" style="color:#94a3b8; transition:transform 0.3s;"></i>
                        </div>
                    </div>

                    {{-- Card Body (List of Attendees) --}}
                    <div class="session-card-body" id="session-body-{{ $idx }}">
                        <table class="hist-table">
                            <thead>
                                <tr>
                                    <th style="width:40px;">#</th>
                                    <th>{{ $session['clinic_type'] === 'mother' ? 'Mother Name & ID' : 'Child Name & ID' }}</th>
                                    <th>{{ $session['clinic_type'] === 'mother' ? 'Area & Contact' : 'Mother Linked & Area' }}</th>
                                    <th>Checkup Notes / Remarks</th>
                                    <th>Recorded Time</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session['records'] as $j => $att)
                                <tr>
                                    <td style="font-weight:700; color:#94a3b8;">{{ $j + 1 }}</td>
                                    <td style="font-weight:700;">
                                        @if($session['clinic_type'] === 'mother')
                                            <i class="fa-solid fa-person-dress" style="color:var(--mother-purple); margin-right:6px;"></i>
                                            {{ $att->mother?->mother_name ?? 'Mother #' . $att->mother_id }}
                                            <div style="font-size:11px; color:#94a3b8; font-weight:600;">Mother ID: #{{ $att->mother_id }}</div>
                                        @else
                                            <i class="fa-solid fa-baby" style="color:var(--child-blue); margin-right:6px;"></i>
                                            {{ $att->child?->display_name ?? 'Child #' . $att->child_id }}
                                            <div style="font-size:11px; color:#94a3b8; font-weight:600;">Child ID: #{{ $att->child_id }}</div>
                                        @endif
                                    </td>
                                    <td style="color:#475569;">
                                        @if($session['clinic_type'] === 'mother')
                                            <div>{{ $att->mother?->midwife?->area?->area_name ?? 'Area ' . ($att->mother?->area_id ?? 'N/A') }}</div>
                                            <div style="font-size:11px; color:#94a3b8;">{{ $att->mother?->mobile_number ?? $att->mother?->phone_number ?? 'No Phone' }}</div>
                                        @else
                                            <div><strong>Mother:</strong> {{ $att->mother?->mother_name ?? $att->child?->mother?->mother_name ?? '—' }}</div>
                                            <div style="font-size:11px; color:#94a3b8;">{{ $att->child?->midwife?->area?->area_name ?? 'Area ' . ($att->child?->area_id ?? 'N/A') }}</div>
                                        @endif
                                    </td>
                                    <td style="color:#334155;">
                                        @if($att->remarks)
                                            <span style="background:#f1f5f9; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:600;">{{ $att->remarks }}</span>
                                        @else
                                            <span style="color:#94a3b8; font-style:italic;">Routine Attendance</span>
                                        @endif
                                    </td>
                                    <td style="font-size:12px; color:#94a3b8;">
                                        {{ $att->created_at ? $att->created_at->format('h:i A') : '—' }}
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.attendances.destroy', $att->id) }}" onsubmit="return confirm('Remove this attendance record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-del-record" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>{{-- /content --}}
</div>{{-- /main-wrapper --}}

{{-- ═══════════════════════════ JAVASCRIPT ═══════════════════════════ --}}
<script>
const mothersList  = @json($mothersList);
const childrenList = @json($childrenList);

let currentClinicType = 'mother';
let rowCounter = 0;

function switchClinicType(type) {
    currentClinicType = type;
    document.getElementById('clinic_type_input').value = type;

    const btnMother    = document.getElementById('btn-type-mother');
    const btnPediatric = document.getElementById('btn-type-pediatric');
    const colHeading   = document.getElementById('participant-col-heading');
    const counterHead  = document.getElementById('counter-heading');
    const addBtnLbl    = document.getElementById('add-btn-label');
    const clinicNameInp= document.getElementById('clinic_name');

    if (type === 'mother') {
        btnMother.classList.add('active');
        btnPediatric.classList.remove('active');
        colHeading.textContent = 'Participated Mother Name';
        counterHead.textContent = 'Total Attendance for this Mother Clinic:';
        addBtnLbl.textContent = 'Add Another Mother';
        if (!clinicNameInp.value || clinicNameInp.value.includes('Child') || clinicNameInp.value.includes('Pediatric') || clinicNameInp.value.includes('Baby')) {
            clinicNameInp.value = 'MOH Maternity Care Clinic';
        }
    } else {
        btnPediatric.classList.add('active');
        btnMother.classList.remove('active');
        colHeading.textContent = 'Participated Child Name';
        counterHead.textContent = 'Total Attendance for this Pediatric Clinic:';
        addBtnLbl.textContent = 'Add Another Child';
        if (!clinicNameInp.value || clinicNameInp.value.includes('Maternity') || clinicNameInp.value.includes('Mother')) {
            clinicNameInp.value = 'Well Baby & Child Development Clinic';
        }
    }

    // Rebuild existing rows according to the new type
    const tbody = document.getElementById('attendeeRows');
    tbody.innerHTML = '';
    rowCounter = 0;
    addAttendeeRow();
}

function buildMothersOptions(selectedId = '') {
    let opts = '<option value="">— Select Mother —</option>';
    mothersList.forEach(m => {
        opts += `<option value="${m.id}" ${selectedId == m.id ? 'selected' : ''}>#${m.id} — ${m.name} (${m.area})</option>`;
    });
    return opts;
}

function buildChildrenOptions(selectedId = '') {
    let opts = '<option value="">— Select Child —</option>';
    childrenList.forEach(c => {
        opts += `<option value="${c.id}" ${selectedId == c.id ? 'selected' : ''}>#${c.id} — ${c.name} (Mother: ${c.mother_name}) [${c.area}]</option>`;
    });
    return opts;
}

function addAttendeeRow(entityId = '', remarks = '') {
    rowCounter++;
    const idx = rowCounter;
    const tbody = document.getElementById('attendeeRows');
    const tr = document.createElement('tr');
    tr.id = `att-row-${idx}`;

    let selectHtml = '';
    let defaultRemark = '';

    if (currentClinicType === 'mother') {
        selectHtml = `<select class="td-select" name="attendees[${idx}][mother_id]" required onchange="updateLiveCount()">
            ${buildMothersOptions(entityId)}
        </select>`;
        defaultRemark = 'Routine Antenatal Checkup';
    } else {
        selectHtml = `<select class="td-select" name="attendees[${idx}][child_id]" required onchange="updateLiveCount()">
            ${buildChildrenOptions(entityId)}
        </select>`;
        defaultRemark = 'Routine Growth & Pediatric Checkup';
    }

    tr.innerHTML = `
        <td class="td-num">${tbody.rows.length + 1}</td>
        <td>${selectHtml}</td>
        <td>
            <input type="text" class="td-input" name="attendees[${idx}][remarks]"
                   placeholder="e.g. ${defaultRemark}" value="${remarks}">
        </td>
        <td>
            <button type="button" class="btn-del-row" onclick="removeAttendeeRow(${idx})" title="Remove">
                <i class="fa-solid fa-times"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    renumberAttendeeRows();
    updateLiveCount();
}

function removeAttendeeRow(idx) {
    const row = document.getElementById(`att-row-${idx}`);
    if (row) row.remove();
    renumberAttendeeRows();
    updateLiveCount();
}

function renumberAttendeeRows() {
    const rows = document.querySelectorAll('#attendeeRows tr');
    rows.forEach((r, i) => {
        const numCell = r.querySelector('.td-num');
        if (numCell) numCell.textContent = i + 1;
    });
}

function updateLiveCount() {
    const rows = document.querySelectorAll('#attendeeRows tr');
    let validCount = 0;

    rows.forEach(r => {
        const sel = r.querySelector('select');
        if (sel && sel.value) {
            validCount++;
        }
    });

    // Count is total attendees configured
    const totalRows = rows.length;
    document.getElementById('live-attendance-count').textContent = totalRows;
    document.getElementById('btn-count').textContent = totalRows;
    document.getElementById('saveAttendanceBtn').disabled = totalRows === 0;
}

function toggleClinicSession(idx) {
    const body    = document.getElementById(`session-body-${idx}`);
    const chevron = document.getElementById(`chevron-session-${idx}`);
    if (body.style.display === 'none') {
        body.style.display = '';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    addAttendeeRow();
    updateLiveCount();
});
</script>

</body>
</html>

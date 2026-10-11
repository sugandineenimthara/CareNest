<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immunizations - CareNest</title>
    <meta name="description" content="Maternal and Child Immunization Registry with separated tracking for Child EPI schedule and Mother Tetanus toxoid protocol.">

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

        /* Sidebar */
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

        /* Main */
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 48px; background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6); position: sticky; top: 0; z-index: 90;
        }
        .header-left { font-family: var(--font-heading); font-size: 22px; font-weight: 800; color: #1b4d3e; }

        .search-box { position: relative; width: 340px; }
        .search-input {
            width: 100%; padding: 10px 16px 10px 40px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            border-radius: 20px; font-family: var(--font-main); font-size: 13px; color: var(--text-dark);
        }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }

        .content-container { padding: 36px 48px; display: flex; flex-direction: column; gap: 28px; }

        .alert-success {
            padding: 16px 20px; background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 14px; color: #065f46; font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }

        /* Top Bar & Big Tabs Switcher (SEPARATED AS REQUIRED) */
        .tab-switcher-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 6px; display: inline-flex; gap: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            width: fit-content;
        }
        .tab-btn {
            padding: 12px 28px; border-radius: 16px; font-family: var(--font-heading);
            font-size: 15px; font-weight: 800; text-decoration: none; display: flex; align-items: center; gap: 10px;
            transition: all 0.25s ease; color: #64748b;
        }
        .tab-btn.active {
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            color: white; box-shadow: 0 6px 18px rgba(27,94,32,0.25);
        }
        .tab-btn:not(.active):hover {
            background: #f1f5f9; color: var(--text-dark);
        }

        /* Header Row */
        .section-header-row {
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
        }
        .section-title { font-family: var(--font-heading); font-size: 26px; font-weight: 800; color: #1e293b; }
        .section-sub { font-size: 14px; color: var(--text-muted); margin-top: 2px; }

        .btn-record-main {
            padding: 12px 24px; border-radius: 16px; border: none;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            color: white; font-family: var(--font-heading); font-size: 14px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 6px 18px rgba(0,200,83,0.3); transition: all 0.25s ease;
        }
        .btn-record-main:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,200,83,0.4); }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
        .stat-card {
            background: rgba(255,255,255,0.85); backdrop-filter: blur(12px);
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px;
            padding: 20px; display: flex; align-items: center; gap: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; font-size: 20px;
        }
        .stat-val { font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #1b4d3e; }
        .stat-lbl { font-size: 12px; font-weight: 600; color: var(--text-muted); }

        /* Protocol Alert Box for Mothers */
        .protocol-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
            border: 1.5px solid #a7f3d0; border-radius: 20px;
            padding: 20px 24px; display: flex; align-items: flex-start; gap: 16px;
        }
        .protocol-icon {
            width: 44px; height: 44px; border-radius: 12px; background: white;
            color: #059669; display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }
        .protocol-title { font-family: var(--font-heading); font-size: 16px; font-weight: 800; color: #064e3b; }
        .protocol-text { font-size: 13px; color: #334155; line-height: 1.6; margin-top: 4px; }

        /* Table Card */
        .table-card {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 24px;
            padding: 28px; box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            display: flex; flex-direction: column; gap: 20px;
        }
        .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .custom-table th {
            text-align: left; padding: 14px 16px; font-size: 12px; font-weight: 700;
            color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
            border-bottom: 1.5px solid #e2e8f0; background: #f8fafc;
        }
        .custom-table th:first-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        .custom-table th:last-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
        .custom-table td {
            padding: 16px; font-size: 14px; border-bottom: 1px solid #f1f5f9; color: var(--text-dark);
            vertical-align: middle;
        }
        .custom-table tr:hover td { background: #f8fafc; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12px;
            font-weight: 700; padding: 4px 12px; border-radius: 20px;
        }
        .badge-safe { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .badge-vaccinated { background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; }
        .badge-due { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Child Vaccine Schedule Chips */
        .schedule-flow { display: flex; gap: 8px; flex-wrap: wrap; }
        .flow-chip {
            padding: 4px 8px; border-radius: 8px; font-size: 11px; font-weight: 700;
            display: inline-flex; align-items: center; gap: 4px;
        }
        .flow-done { background: #dcfce7; color: #166534; }
        .flow-pending { background: #f1f5f9; color: #64748b; }

        .btn-log-small {
            padding: 6px 14px; border-radius: 10px; background: #00c853; color: white;
            border: none; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-log-small:hover { background: #009624; }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15,23,42,0.6); backdrop-filter: blur(4px);
            z-index: 1000; align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal-card {
            background: white; border-radius: 24px; padding: 32px; width: 100%;
            max-width: 540px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            display: flex; flex-direction: column; gap: 20px;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 14px; border-bottom: 1px solid #f1f5f9;
        }
        .modal-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #1e293b; }
        .close-btn { background: transparent; border: none; font-size: 18px; color: #94a3b8; cursor: pointer; }
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
            <li class="nav-item"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
            <li class="nav-item active"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>

            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">Immunization Management</div>
            <form method="GET" action="{{ route('admin.immunizations.index') }}" class="search-box">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="{{ $tab === 'mothers' ? 'Search mother name, ID...' : 'Search child or mother...' }}" value="{{ $search }}">
            </form>
            <div class="header-right">
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

            <!-- SEPARATED TABS FOR MOTHER AND CHILD VACCINATION (REQUIRED) -->
            <div class="tab-switcher-card">
                <a href="{{ route('admin.immunizations.index', ['tab' => 'children']) }}" class="tab-btn {{ $tab === 'children' ? 'active' : '' }}">
                    <i class="fa-solid fa-baby"></i>
                    <span>Children Immunization</span>
                </a>
                <a href="{{ route('admin.immunizations.index', ['tab' => 'mothers']) }}" class="tab-btn {{ $tab === 'mothers' ? 'active' : '' }}">
                    <i class="fa-solid fa-person-breastfeeding"></i>
                    <span>Mother Vaccination (Tetanus)</span>
                </a>
            </div>

            <!-- TAB 1: CHILDREN VACCINATION -->
            @if($tab === 'children')
                <div class="section-header-row">
                    <div>
                        <h1 class="section-title">Children Immunization Registry</h1>
                        <p class="section-sub">Standard National Schedule: BCG, Penta, Polio, FITV, MMR, JE, DPT, DT</p>
                    </div>
                    <button type="button" class="btn-record-main" onclick="openChildModal('', '')">
                        <i class="fa-solid fa-plus"></i>
                        <span>Record Child Vaccine</span>
                    </button>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-syringe"></i></div>
                        <div>
                            <div class="stat-val">{{ $totalChildVaccinesGiven }}</div>
                            <div class="stat-lbl">Child Doses Recorded</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-shield-virus"></i></div>
                        <div>
                            <div class="stat-val">{{ $childrenWithSchedule->where('bcg_vaccinated_at_birth', true)->count() }}</div>
                            <div class="stat-lbl">BCG Covered at Birth</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <div class="stat-val">{{ $childrenWithSchedule->sum('overdue_vaccines') }}</div>
                            <div class="stat-lbl">Overdue Milestones</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <div class="stat-val">{{ $childrenWithSchedule->sum('due_now_vaccines') }}</div>
                            <div class="stat-lbl">Due for Vaccination Now</div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Table -->
                <div class="table-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h2 style="font-family:var(--font-heading); font-size:18px; font-weight:800; color:#1e293b;">
                            Child Vaccination Tracking List
                        </h2>
                        <span style="font-size:13px; color:var(--text-muted);">{{ $childrenWithSchedule->count() }} Children</span>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Child &amp; Age</th>
                                    <th>Linked Mother</th>
                                    <th>Area / Midwife</th>
                                    <th>Milestones (13 Vaccines)</th>
                                    <th>Progress</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($childrenWithSchedule as $c)
                                    <tr>
                                        <td>
                                            <div style="font-weight:800; color:#1e293b;">
                                                <a href="{{ route('admin.children.show', $c->child_id) }}" style="color:#1b4d3e; text-decoration:none;">
                                                    {{ $c->display_name }}
                                                </a>
                                            </div>
                                            <div style="font-size:12px; color:#64748b;">
                                                ID: #{{ $c->child_id }} • {{ $c->gender }} • {{ $c->age }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($c->mother)
                                                <div style="font-weight:700; color:#14532d;">
                                                    <a href="{{ route('admin.mothers.show', $c->mother_id) }}" style="color:#059669; text-decoration:none;">
                                                        <i class="fa-solid fa-person-breastfeeding"></i> {{ $c->mother->mother_name }}
                                                    </a>
                                                </div>
                                                <div style="font-size:11px; color:#64748b;">Mother ID: #{{ $c->mother_id }}</div>
                                            @else
                                                <span style="color:#94a3b8; font-size:12px;">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight:700;">{{ $c->area ? $c->area->area_name : ($c->midwife?->area?->area_name ?? 'N/A') }}</div>
                                            <div style="font-size:11px; color:#64748b;">Midwife: {{ $c->midwife ? $c->midwife->midwife_name : 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div class="schedule-flow">
                                                @foreach(array_slice($c->schedule_items, 0, 7) as $si)
                                                    <span class="flow-chip {{ $si['status'] === 'Completed' ? 'flow-done' : 'flow-pending' }}" title="{{ $si['vaccine']['vaccine_name'] }} - {{ $si['status'] }}">
                                                        @if($si['status'] === 'Completed')<i class="fa-solid fa-check"></i>@endif
                                                        {{ explode(' ', $si['vaccine']['vaccine_name'])[0] }}
                                                    </span>
                                                @endforeach
                                                @if(count($c->schedule_items) > 7)
                                                    <span class="flow-chip flow-pending">+{{ count($c->schedule_items) - 7 }} more</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <div style="width:70px; height:8px; background:#e2e8f0; border-radius:10px; overflow:hidden;">
                                                    <div style="width:{{ $c->progress_percent }}%; height:100%; background:#00c853;"></div>
                                                </div>
                                                <span style="font-size:12px; font-weight:700;">{{ $c->progress_percent }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="display:flex; gap:8px;">
                                                <button type="button" class="btn-log-small" onclick="openChildModal('{{ $c->child_id }}', '')">
                                                    <i class="fa-solid fa-syringe"></i> Log Dose
                                                </button>
                                                <a href="{{ route('admin.children.show', $c->child_id) }}" style="padding:6px 10px; background:#f1f5f9; border-radius:10px; color:#475569; text-decoration:none; font-size:12px; font-weight:700;">
                                                    Schedule
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:30px; color:#94a3b8;">
                                            No children found. Register a child first to track immunizations.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Logs Table -->
                <div class="table-card">
                    <h3 style="font-family:var(--font-heading); font-size:17px; font-weight:800; color:#1e293b;">
                        <i class="fa-solid fa-clock-rotate-left" style="color:#00c853;"></i> Recently Administered Child Vaccines
                    </h3>
                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Date Vaccinated</th>
                                    <th>Child Name</th>
                                    <th>Vaccine</th>
                                    <th>Batch Number *</th>
                                    <th>Administered By</th>
                                    <th>Mother</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentChildImmunizations as $ri)
                                    <tr>
                                        <td style="font-weight:700;">
                                            {{ \Carbon\Carbon::parse($ri->immunization_date)->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.children.show', $ri->child_id) }}" style="font-weight:700; color:#1b4d3e; text-decoration:none;">
                                                {{ $ri->child ? $ri->child->display_name : ('Child #' . $ri->child_id) }}
                                            </a>
                                        </td>
                                        <td>
                                            <span style="font-weight:800; color:#0369a1;">{{ $ri->vaccine_name }}</span>
                                            @if($ri->dose) <span style="font-size:11px; color:#64748b;">({{ $ri->dose }})</span> @endif
                                        </td>
                                        <td>
                                            <span style="font-family:monospace; font-weight:700; background:#f1f5f9; padding:2px 8px; border-radius:6px;">
                                                {{ $ri->batch_no }}
                                            </span>
                                        </td>
                                        <td>{{ $ri->midwife ? $ri->midwife->midwife_name : 'Clinic Midwife' }}</td>
                                        <td>{{ $ri->child?->mother ? $ri->child->mother->mother_name : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">
                                            No vaccine records yet. Use "Record Child Vaccine" to log doses.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- TAB 2: MOTHER VACCINATION (TETANUS) -->
            @elseif($tab === 'mothers')
                <div class="section-header-row">
                    <div>
                        <h1 class="section-title">Mother Tetanus Immunization Registry</h1>
                        <p class="section-sub">Maternal Tetanus Toxoid (TT) Protocol (1st, 2nd, 3rd, 4th Pregnancies)</p>
                    </div>
                    <button type="button" class="btn-record-main" onclick="openMotherModal('', '')">
                        <i class="fa-solid fa-plus"></i>
                        <span>Record Mother Tetanus Dose</span>
                    </button>
                </div>

                <!-- SRI LANKA MOH TETANUS PROTOCOL ALERT (REQUIRED POLICY) -->
                <div class="protocol-card">
                    <div class="protocol-icon"><i class="fa-solid fa-shield-heart"></i></div>
                    <div>
                        <div class="protocol-title">Maternal Tetanus Vaccination Guidelines</div>
                        <div class="protocol-text">
                            Pregnant mothers are vaccinated against <strong>Tetanus Toxoid (TT)</strong> during their <strong>1st, 2nd, 3rd, and 4th pregnancies</strong> to prevent maternal and neonatal tetanus. 
                            <strong>After the 4th pregnancy</strong> (5th pregnancy onwards), the mother has acquired full lifelong immunity and is <strong>Safe from Tetanus</strong>.
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-person-breastfeeding"></i></div>
                        <div>
                            <div class="stat-val">{{ $allMothers->count() }}</div>
                            <div class="stat-lbl">Registered Mothers</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#dcfce7; color:#16a34a;"><i class="fa-solid fa-shield-check"></i></div>
                        <div>
                            <div class="stat-val">{{ $mothersSafeCount }}</div>
                            <div class="stat-lbl">Safe from Tetanus (&gt;4th Preg)</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <div class="stat-val">{{ $mothersDueCount }}</div>
                            <div class="stat-lbl">Tetanus Dose Due (1st-4th Preg)</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#ecfdf5; color:#059669;"><i class="fa-solid fa-syringe"></i></div>
                        <div>
                            <div class="stat-val">{{ $totalMotherVaccinesGiven }}</div>
                            <div class="stat-lbl">Maternal Doses Administered</div>
                        </div>
                    </div>
                </div>

                <!-- Mothers Table -->
                <div class="table-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h2 style="font-family:var(--font-heading); font-size:18px; font-weight:800; color:#1e293b;">
                            Maternal Tetanus Protection Registry
                        </h2>
                        <span style="font-size:13px; color:var(--text-muted);">{{ $allMothers->count() }} Mothers</span>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Mother Name &amp; ID</th>
                                    <th>Pregnancy Count</th>
                                    <th>Tetanus Protocol Status</th>
                                    <th>Vaccination Record</th>
                                    <th>Assigned Midwife</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allMothers as $mother)
                                    <tr>
                                        <td>
                                            <div style="font-weight:800; color:#1e293b;">
                                                <a href="{{ route('admin.mothers.show', $mother->mother_id) }}" style="color:#1b4d3e; text-decoration:none;">
                                                    {{ $mother->mother_name }}
                                                </a>
                                            </div>
                                            <div style="font-size:12px; color:#64748b;">
                                                ID: #{{ $mother->mother_id }} • Tel: {{ $mother->phone_no ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-weight:800; font-size:14px; color:#334155;">
                                                @if($mother->current_preg_no == 1) 1st Pregnancy
                                                @elseif($mother->current_preg_no == 2) 2nd Pregnancy
                                                @elseif($mother->current_preg_no == 3) 3rd Pregnancy
                                                @elseif($mother->current_preg_no == 4) 4th Pregnancy
                                                @else {{ $mother->current_preg_no }}th Pregnancy
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($mother->is_tetanus_safe)
                                                <span class="badge badge-safe">
                                                    <i class="fa-solid fa-shield-check"></i> Safe from Tetanus (&gt;4th Preg)
                                                </span>
                                            @elseif($mother->tetanus_record)
                                                <span class="badge badge-vaccinated">
                                                    <i class="fa-solid fa-circle-check"></i> Vaccinated
                                                </span>
                                            @else
                                                <span class="badge badge-due">
                                                    <i class="fa-solid fa-triangle-exclamation"></i> Tetanus Dose Required
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($mother->is_tetanus_safe)
                                                <span style="font-size:12px; color:#15803d; font-weight:600;">
                                                    <i class="fa-solid fa-lock"></i> Protected by previous doses
                                                </span>
                                            @elseif($mother->tetanus_record)
                                                <div style="font-size:12px; color:#334155;">
                                                    Batch: <strong style="font-family:monospace;">{{ $mother->tetanus_record->batch_no }}</strong>
                                                </div>
                                                <div style="font-size:11px; color:#64748b;">
                                                    Date: {{ \Carbon\Carbon::parse($mother->tetanus_record->immunization_date)->format('M d, Y') }}
                                                </div>
                                            @else
                                                <span style="font-size:12px; color:#b91c1c; font-weight:600;">
                                                    Pending dose for pregnancy #{{ $mother->current_preg_no }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $mother->midwife ? $mother->midwife->midwife_name : 'N/A' }}</div>
                                            <div style="font-size:11px; color:#64748b;">{{ $mother->midwife?->area?->area_name }}</div>
                                        </td>
                                        <td>
                                            @if(!$mother->is_tetanus_safe)
                                                <button type="button" class="btn-log-small" onclick="openMotherModal('{{ $mother->mother_id }}', '{{ $mother->current_preg_no }}')">
                                                    <i class="fa-solid fa-syringe"></i> Record TT
                                                </button>
                                            @else
                                                <span style="font-size:12px; color:#94a3b8; font-weight:600;">Safe</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:30px; color:#94a3b8;">
                                            No mothers found in database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Maternal Tetanus Vaccinations -->
                <div class="table-card">
                    <h3 style="font-family:var(--font-heading); font-size:17px; font-weight:800; color:#1e293b;">
                        <i class="fa-solid fa-clock-rotate-left" style="color:#00c853;"></i> Recently Administered Maternal Tetanus Vaccines
                    </h3>
                    <div style="overflow-x:auto;">
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>Date Vaccinated</th>
                                    <th>Mother Name</th>
                                    <th>Dose Milestone</th>
                                    <th>Batch Number *</th>
                                    <th>Administered By</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMotherImmunizations as $rm)
                                    <tr>
                                        <td style="font-weight:700;">{{ \Carbon\Carbon::parse($rm->immunization_date)->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.mothers.show', $rm->mother_id) }}" style="font-weight:700; color:#1b4d3e; text-decoration:none;">
                                                {{ $rm->mother ? $rm->mother->mother_name : ('Mother #' . $rm->mother_id) }}
                                            </a>
                                        </td>
                                        <td><span style="font-weight:700; color:#0369a1;">{{ $rm->dose ?? 'Tetanus Toxoid' }}</span></td>
                                        <td>
                                            <span style="font-family:monospace; font-weight:700; background:#f1f5f9; padding:2px 8px; border-radius:6px;">
                                                {{ $rm->batch_no }}
                                            </span>
                                        </td>
                                        <td>{{ $rm->midwife ? $rm->midwife->midwife_name : 'Clinic Midwife' }}</td>
                                        <td style="font-size:12px; color:#64748b;">{{ $rm->remarks ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:20px; color:#94a3b8;">
                                            No maternal tetanus records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal: Record Child Vaccination -->
    <div class="modal-overlay" id="childModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-syringe" style="color:#00c853;"></i> Record Child Vaccine Dose</h3>
                <button type="button" class="close-btn" onclick="closeChildModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('admin.immunizations.child.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="child_id">Select Child *</label>
                    <select name="child_id" id="modal_child_id" class="form-select" required>
                        <option value="">-- Choose Child --</option>
                        @foreach($childrenWithSchedule as $c)
                            <option value="{{ $c->child_id }}">
                                {{ $c->display_name }} (ID: #{{ $c->child_id }}, Mother: {{ $c->mother ? $c->mother->mother_name : 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="vaccine_name">Vaccine *</label>
                    <select name="vaccine_name" id="modal_vac_name" class="form-select" required>
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
                    <label class="form-label" for="batch_no">Vaccine Batch Number *</label>
                    <input type="text" name="batch_no" id="modal_batch_no" class="form-input" placeholder="e.g. VAC-2026-CH01" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="immunization_date">Date Vaccinated *</label>
                    <input type="date" name="immunization_date" id="modal_immunization_date" class="form-input" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="midwife_id">Administering Midwife</label>
                    <select name="midwife_id" class="form-select">
                        @foreach($midwives as $mw)
                            <option value="{{ $mw->midwife_id }}">{{ $mw->midwife_name }} ({{ $mw->area ? $mw->area->area_name : 'No Area' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="remarks">Remarks</label>
                    <input type="text" name="remarks" class="form-input" placeholder="Optional notes...">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                    <button type="button" class="btn-log-small" style="background:#cbd5e1; color:#334155;" onclick="closeChildModal()">Cancel</button>
                    <button type="submit" class="btn-record-main">Save Vaccine Record</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Record Mother Tetanus Dose -->
    <div class="modal-overlay" id="motherModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-person-breastfeeding" style="color:#00c853;"></i> Record Mother Tetanus Vaccine</h3>
                <button type="button" class="close-btn" onclick="closeMotherModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{ route('admin.immunizations.mother.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="mother_id">Select Mother *</label>
                    <select name="mother_id" id="modal_mother_id" class="form-select" required>
                        <option value="">-- Choose Mother --</option>
                        @foreach($allMothers as $m)
                            <option value="{{ $m->mother_id }}" data-preg="{{ $m->current_preg_no }}">
                                Mother #{{ $m->mother_id }} — {{ $m->mother_name }} (Pregnancy #{{ $m->current_preg_no }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label">Vaccine</label>
                    <input type="text" class="form-input" value="Tetanus Toxoid (TT)" readonly style="background:#f1f5f9;">
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="batch_no">Tetanus Vaccine Batch Number *</label>
                    <input type="text" name="batch_no" class="form-input" placeholder="e.g. TT-2026-M455" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="immunization_date">Date Vaccinated *</label>
                    <input type="date" name="immunization_date" class="form-input" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="dose">Dose Milestone</label>
                    <input type="text" name="dose" id="modal_mother_dose" class="form-input" placeholder="e.g. 1st Dose or Pregnancy #1 Dose">
                </div>

                <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="midwife_id">Administering Midwife</label>
                    <select name="midwife_id" class="form-select">
                        @foreach($midwives as $mw)
                            <option value="{{ $mw->midwife_id }}">{{ $mw->midwife_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:20px;">
                    <button type="button" class="btn-log-small" style="background:#cbd5e1; color:#334155;" onclick="closeMotherModal()">Cancel</button>
                    <button type="submit" class="btn-record-main">Save Tetanus Record</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openChildModal(childId, vaccineName) {
            const modal = document.getElementById('childModal');
            if (childId) {
                document.getElementById('modal_child_id').value = childId;
            }
            if (vaccineName) {
                const sel = document.getElementById('modal_vac_name');
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value.toLowerCase().includes(vaccineName.toLowerCase().split(' ')[0])) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
            }
            modal.classList.add('open');
        }

        function closeChildModal() {
            document.getElementById('childModal').classList.remove('open');
        }

        function openMotherModal(motherId, pregNo) {
            const modal = document.getElementById('motherModal');
            if (motherId) {
                document.getElementById('modal_mother_id').value = motherId;
            }
            if (pregNo) {
                document.getElementById('modal_mother_dose').value = `Pregnancy #${pregNo} Tetanus Dose`;
            }
            modal.classList.add('open');
        }

        function closeMotherModal() {
            document.getElementById('motherModal').classList.remove('open');
        }

        document.getElementById('childModal').addEventListener('click', function(e) {
            if (e.target === this) closeChildModal();
        });
        document.getElementById('motherModal').addEventListener('click', function(e) {
            if (e.target === this) closeMotherModal();
        });
    </script>
</body>
</html>

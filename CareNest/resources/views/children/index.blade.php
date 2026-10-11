<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Children Registry - CareNest</title>
    <meta name="description" content="View and manage child records separated by clinic areas, linked with mothers and immunization tracking in CareNest.">

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

        /* ---- Sidebar ---- */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226,232,240,0.8);
            display: flex; flex-direction: column;
            padding: 30px 20px;
            position: fixed; top: 0; bottom: 0; left: 0;
            z-index: 100;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            font-family: var(--font-heading); font-size: 24px; font-weight: 800;
            color: #1b4d3e; text-decoration: none; margin-bottom: 36px; padding-left: 10px;
        }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto; padding-right: 4px; }
        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
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
        .sidebar-footer { margin-top: 20px; display: flex; flex-direction: column; gap: 12px; }
        .btn-schedule {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            border: none; border-radius: 18px; color: #fff;
            font-family: var(--font-heading); font-size: 15px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            gap: 8px; box-shadow: 0 8px 20px rgba(0,200,83,0.3); transition: all 0.25s ease;
            text-decoration: none;
        }
        .btn-schedule:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,200,83,0.4); }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            background: transparent; border: none; padding: 10px 18px;
            font-family: var(--font-main); font-size: 14px; font-weight: 600;
            color: #64748b; cursor: pointer; transition: color 0.2s ease; width: 100%;
        }
        .logout-btn:hover { color: #ef4444; }

        /* ---- Main Wrapper ---- */
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ---- Top Header ---- */
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 48px;
            background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6);
            position: sticky; top: 0; z-index: 90;
        }
        .header-left { font-family: var(--font-heading); font-size: 22px; font-weight: 800; color: #1b4d3e; }
        .search-box { position: relative; width: 340px; }
        .search-input {
            width: 100%; padding: 10px 16px 10px 40px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            border-radius: 20px; font-family: var(--font-main); font-size: 13px; color: var(--text-dark);
        }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .header-right { display: flex; align-items: center; gap: 16px; }
        .user-profile { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, #00e676, #1b5e20);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 16px;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .user-role { font-size: 12px; color: var(--text-muted); }

        /* ---- Content Container ---- */
        .content-container { padding: 36px 48px; display: flex; flex-direction: column; gap: 32px; }

        /* Alert message */
        .alert-success {
            padding: 16px 20px; background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 14px; color: #065f46; font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }

        /* ---- Top Action Bar ---- */
        .action-bar {
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 16px;
        }
        .page-title {
            font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: #1b4d3e;
            display: flex; align-items: center; gap: 12px;
        }
        .page-title-badge {
            font-size: 13px; font-family: var(--font-main); font-weight: 700;
            background: #e0f2fe; color: #0284c7; padding: 4px 12px; border-radius: 20px;
        }
        .btn-register {
            padding: 12px 24px;
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            border: none; border-radius: 16px; color: #fff;
            font-family: var(--font-heading); font-size: 14px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 8px 20px rgba(27,94,32,0.25); text-decoration: none;
            transition: all 0.25s ease;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(27,94,32,0.35); }

        /* ---- Stat Chips ---- */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;
        }
        .stat-card {
            background: rgba(255,255,255,0.85); backdrop-filter: blur(12px);
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px;
            padding: 20px; display: flex; align-items: center; gap: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .stat-val { font-family: var(--font-heading); font-size: 24px; font-weight: 800; color: #1b4d3e; }
        .stat-lbl { font-size: 12px; font-weight: 600; color: var(--text-muted); }

        /* ---- Area Filter Pills ---- */
        .area-pills {
            display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        }
        .pill-label { font-size: 13px; font-weight: 700; color: var(--text-muted); margin-right: 4px; }
        .area-pill {
            padding: 8px 18px; border-radius: 20px; font-size: 13px; font-weight: 700;
            text-decoration: none; color: #64748b; background: white;
            border: 1px solid #e2e8f0; transition: all 0.2s ease;
        }
        .area-pill:hover, .area-pill.active {
            background: #1b4d3e; color: white; border-color: #1b4d3e;
            box-shadow: 0 4px 12px rgba(27,77,62,0.2);
        }

        /* ---- Area Sections ---- */
        .area-section {
            background: rgba(255,255,255,0.85); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 24px;
            padding: 28px; box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            display: flex; flex-direction: column; gap: 20px;
        }
        .area-header {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; gap: 12px;
        }
        .area-info { display: flex; align-items: center; gap: 14px; }
        .area-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: white; display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .area-name { font-family: var(--font-heading); font-size: 19px; font-weight: 800; color: #1e293b; }
        .area-midwives { font-size: 13px; color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .area-count-badge {
            background: #ecfdf5; color: #047857; font-size: 13px; font-weight: 700;
            padding: 6px 14px; border-radius: 20px; border: 1px solid #a7f3d0;
        }

        /* ---- Children Grid ---- */
        .children-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(420px, 1fr)); gap: 20px;
        }
        .child-card {
            background: white; border: 1px solid #e2e8f0; border-radius: 20px;
            padding: 20px; display: flex; flex-direction: column; gap: 16px;
            transition: all 0.25s ease; box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            position: relative;
        }
        .child-card:hover {
            transform: translateY(-3px); box-shadow: 0 12px 24px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }

        .child-header {
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .child-main { display: flex; align-items: center; gap: 12px; }
        .child-gender-avatar {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800;
        }
        .gender-male { background: #e0f2fe; color: #0284c7; }
        .gender-female { background: #fce7f3; color: #db2777; }

        .child-name { font-family: var(--font-heading); font-size: 16px; font-weight: 800; color: #1e293b; }
        .child-meta { font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px; margin-top: 2px; }
        .child-id-pill {
            background: #f1f5f9; color: #475569; font-weight: 700; font-size: 11px;
            padding: 2px 8px; border-radius: 6px;
        }

        /* Measurements grid */
        .measure-chips {
            display: flex; gap: 8px; flex-wrap: wrap;
        }
        .chip {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 6px 12px; font-size: 12px; font-weight: 600; color: #334155;
            display: flex; align-items: center; gap: 6px;
        }
        .chip i { color: #64748b; font-size: 12px; }
        .chip strong { color: #0f172a; }

        /* BCG status pill */
        .bcg-pill {
            display: inline-flex; align-items: center; gap: 6px; font-size: 11px;
            font-weight: 700; padding: 4px 10px; border-radius: 12px;
        }
        .bcg-done { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .bcg-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

        /* ---- Mother Details Sub-Card (LINKED WITH MOTHER) ---- */
        .mother-subcard {
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
            border: 1px solid #d1fae5; border-radius: 14px;
            padding: 12px 14px; display: flex; align-items: center; justify-content: space-between;
            gap: 12px;
        }
        .mother-left { display: flex; align-items: center; gap: 10px; }
        .mother-icon {
            width: 32px; height: 32px; border-radius: 50%;
            background: #dcfce7; color: #166534;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
        }
        .mother-name-title { font-size: 13px; font-weight: 700; color: #14532d; }
        .mother-details-sub { font-size: 11px; color: #4b5563; display: flex; align-items: center; gap: 8px; }
        .mother-view-link {
            font-size: 12px; font-weight: 700; color: #059669; text-decoration: none;
            padding: 4px 10px; background: white; border: 1px solid #a7f3d0; border-radius: 8px;
            display: flex; align-items: center; gap: 4px; white-space: nowrap;
        }
        .mother-view-link:hover { background: #ecfdf5; color: #047857; }

        /* Card footer action */
        .child-footer {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 12px; border-top: 1px solid #f1f5f9;
        }
        .btn-view-child {
            font-size: 13px; font-weight: 700; color: #1b4d3e; text-decoration: none;
            display: flex; align-items: center; gap: 6px; transition: gap 0.2s ease;
        }
        .btn-view-child:hover { gap: 10px; color: #00c853; }
        .btn-edit-child {
            font-size: 12px; font-weight: 600; color: #64748b; text-decoration: none;
            display: flex; align-items: center; gap: 4px;
        }
        .btn-edit-child:hover { color: #1e293b; }

        /* Empty state */
        .empty-area {
            text-align: center; padding: 32px; color: #94a3b8; font-size: 14px;
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
            <li class="nav-item">
                <a href="{{ route('admin.mothers.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Mothers</span>
                </a>
            </li>
            <li class="nav-item active">
                <a href="{{ route('admin.children.index') }}">
                    <i class="fa-solid fa-baby"></i>
                    <span>Children</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.immunizations.index') }}">
                    <i class="fa-solid fa-syringe"></i>
                    <span>Immunizations</span>
                </a>
            </li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>
            <li class="nav-item"><a href="#lab-tests"><i class="fa-solid fa-vial-circle-check"></i><span>Lab Tests</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="{{ route('admin.children.create') }}" class="btn-schedule">
                <i class="fa-solid fa-plus"></i>
                <span>Register Child</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">Children Management</div>
            <form method="GET" action="{{ route('admin.children.index') }}" class="search-box">
                @if(request('area_id'))
                    <input type="hidden" name="area_id" value="{{ request('area_id') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Search child name, ID, or mother..." value="{{ $search }}">
            </form>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <!-- Content Area -->
        <main class="content-container">
            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Action Bar -->
            <div class="action-bar">
                <div>
                    <h1 class="page-title">
                        <span>Children Registry</span>
                        <span class="page-title-badge">Separated by Clinic Areas</span>
                    </h1>
                </div>
                <div style="display:flex; gap:12px;">
                    <a href="{{ route('admin.immunizations.index') }}" class="btn-register" style="background:white; color:#1b4d3e; border:1px solid #cbd5e1; box-shadow:none;">
                        <i class="fa-solid fa-syringe" style="color:#00c853;"></i>
                        <span>Immunization Schedule</span>
                    </a>
                    <a href="{{ route('admin.children.create') }}" class="btn-register">
                        <i class="fa-solid fa-plus"></i>
                        <span>Register New Child</span>
                    </a>
                </div>
            </div>

            <!-- Stats Chips -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-baby"></i></div>
                    <div>
                        <div class="stat-val">{{ $totalChildren }}</div>
                        <div class="stat-lbl">Total Registered Children</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fa-solid fa-hospital"></i></div>
                    <div>
                        <div class="stat-val">{{ $totalAreas }}</div>
                        <div class="stat-lbl">Clinic Areas</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fce7f3; color:#db2777;"><i class="fa-solid fa-venus-mars"></i></div>
                    <div>
                        <div class="stat-val">{{ $maleChildren }}M / {{ $femaleChildren }}F</div>
                        <div class="stat-lbl">Gender Distribution</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ecfdf5; color:#059669;"><i class="fa-solid fa-shield-virus"></i></div>
                    <div>
                        <div class="stat-val">{{ $bcgVaccinatedCount }}</div>
                        <div class="stat-lbl">BCG Vaccinated (&lt;24hrs)</div>
                    </div>
                </div>
            </div>

            <!-- Area Filter Pills -->
            <div class="area-pills">
                <span class="pill-label"><i class="fa-solid fa-filter"></i> Filter Area:</span>
                <a href="{{ route('admin.children.index', array_filter(['search' => $search])) }}" class="area-pill {{ empty($areaFilter) ? 'active' : '' }}">
                    All Areas ({{ $totalChildren }})
                </a>
                @foreach($allAreas as $a)
                    <a href="{{ route('admin.children.index', array_filter(['area_id' => $a->area_id, 'search' => $search])) }}" class="area-pill {{ $areaFilter == $a->area_id ? 'active' : '' }}">
                        {{ $a->area_name }}
                    </a>
                @endforeach
            </div>

            @if($search)
                <div style="font-size:14px; color:#64748b;">
                    Showing search results for "<strong>{{ $search }}</strong>" —
                    <a href="{{ route('admin.children.index') }}" style="color:#00c853; font-weight:700;">Clear search</a>
                </div>
            @endif

            <!-- Area Groups -->
            @foreach($areas as $area)
                <div class="area-section">
                    <div class="area-header">
                        <div class="area-info">
                            <div class="area-avatar"><i class="fa-solid fa-hospital"></i></div>
                            <div>
                                <h2 class="area-name">{{ $area->area_name }}</h2>
                                <div class="area-midwives">
                                    <i class="fa-solid fa-user-nurse" style="color:#00c853;"></i>
                                    <span>
                                        Assigned Midwives: 
                                        @if($area->midwives->count() > 0)
                                            {{ $area->midwives->pluck('midwife_name')->join(', ') }}
                                        @else
                                            None Assigned
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="area-count-badge">
                            <i class="fa-solid fa-baby"></i> {{ $area->filtered_children->count() }} Children
                        </div>
                    </div>

                    @if($area->filtered_children->count() > 0)
                        <div class="children-grid">
                            @foreach($area->filtered_children as $child)
                                <div class="child-card">
                                    <!-- Child Header -->
                                    <div class="child-header">
                                        <div class="child-main">
                                            <div class="child-gender-avatar {{ $child->gender === 'Female' ? 'gender-female' : 'gender-male' }}">
                                                <i class="fa-solid {{ $child->gender === 'Female' ? 'fa-venus' : 'fa-mars' }}"></i>
                                            </div>
                                            <div>
                                                <h3 class="child-name">{{ $child->display_name }}</h3>
                                                <div class="child-meta">
                                                    <span class="child-id-pill">ID: #{{ $child->child_id }}</span>
                                                    <span>•</span>
                                                    <span>{{ $child->age ?? 'Age N/A' }}</span>
                                                    <span>•</span>
                                                    <span>DOB: {{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- BCG Badge -->
                                        <div>
                                            @if($child->bcg_vaccinated_at_birth)
                                                <span class="bcg-pill bcg-done" title="BCG vaccinated before 24hrs from birth">
                                                    <i class="fa-solid fa-check"></i> BCG Vaccinated
                                                </span>
                                            @else
                                                <span class="bcg-pill bcg-pending" title="BCG not yet recorded">
                                                    <i class="fa-solid fa-clock"></i> BCG Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Measurements -->
                                    <div class="measure-chips">
                                        <div class="chip">
                                            <i class="fa-solid fa-weight-scale"></i>
                                            <span>Birth Wt: <strong>{{ $child->birth_weight ? $child->birth_weight . ' kg' : 'N/A' }}</strong></span>
                                        </div>
                                        <div class="chip">
                                            <i class="fa-solid fa-ruler-vertical"></i>
                                            <span>Birth Lg: <strong>{{ $child->birth_length ? $child->birth_length . ' cm' : 'N/A' }}</strong></span>
                                        </div>
                                        <div class="chip">
                                            <i class="fa-solid fa-syringe"></i>
                                            <span>Vaccines: <strong>{{ $child->immunizations->count() }} doses</strong></span>
                                        </div>
                                    </div>

                                    <!-- LINKED MOTHER DETAILS (Required) -->
                                    <div class="mother-subcard">
                                        <div class="mother-left">
                                            <div class="mother-icon"><i class="fa-solid fa-person-breastfeeding"></i></div>
                                            <div>
                                                <div class="mother-name-title">
                                                    Mother: {{ $child->mother ? $child->mother->mother_name : 'Not Assigned' }}
                                                </div>
                                                <div class="mother-details-sub">
                                                    <span>Mother ID: <strong>#{{ $child->mother_id }}</strong></span>
                                                    @if($child->mother && $child->mother->phone_no)
                                                        <span>• <i class="fa-solid fa-phone" style="font-size:10px;"></i> {{ $child->mother->phone_no }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @if($child->mother)
                                            <a href="{{ route('admin.mothers.show', $child->mother_id) }}" class="mother-view-link" title="View Mother Profile">
                                                <span>Profile</span> <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Footer Actions -->
                                    <div class="child-footer">
                                        <a href="{{ route('admin.children.edit', $child->child_id) }}" class="btn-edit-child">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <a href="{{ route('admin.children.show', $child->child_id) }}" class="btn-view-child">
                                            <span>View Immunization Schedule</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-area">
                            <i class="fa-solid fa-baby-carriage" style="font-size:32px; color:#cbd5e1; margin-bottom:8px; display:block;"></i>
                            No children currently registered in {{ $area->area_name }}.
                            <div style="margin-top:10px;">
                                <a href="{{ route('admin.children.create', ['area_id' => $area->area_id]) }}" style="color:#00c853; font-weight:700; text-decoration:none;">
                                    + Register first child in this area
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </main>
    </div>
</body>
</html>

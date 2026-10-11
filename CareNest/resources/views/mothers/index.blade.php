<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mothers - CareNest</title>
    <meta name="description" content="View and manage all registered mothers grouped by assigned midwife in CareNest maternal care system.">

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
        .search-box { position: relative; width: 320px; }
        .search-input {
            width: 100%; padding: 10px 16px 10px 40px;
            background: #f1f5f9; border: 1px solid #e2e8f0;
            border-radius: 20px; font-family: var(--font-main); font-size: 13px; color: var(--text-dark);
            outline: none; transition: border-color 0.2s;
        }
        .search-input:focus { border-color: #00c853; }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
        .header-right { display: flex; align-items: center; gap: 20px; }
        .icon-btn {
            background: #f1f5f9; border: none; width: 38px; height: 38px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; color: #64748b;
            cursor: pointer; transition: all 0.2s ease;
        }
        .icon-btn:hover { background: #e2e8f0; color: var(--text-dark); }
        .user-badge-container { display: flex; align-items: center; gap: 10px; padding-left: 12px; border-left: 1px solid #e2e8f0; }
        .user-role-label { font-size: 13px; font-weight: 700; color: #334155; }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 16px; }

        /* ---- Content ---- */
        .content-container { padding: 36px 48px; max-width: 1280px; width: 100%; margin: 0 auto; flex: 1; }

        .page-header {
            display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px;
        }
        .page-title { font-family: var(--font-heading); font-size: 36px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .page-sub { font-size: 15px; color: var(--text-muted); }

        .btn-register {
            display: flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            color: #fff; border: none; padding: 13px 24px;
            border-radius: 16px; font-family: var(--font-heading); font-size: 15px; font-weight: 700;
            cursor: pointer; text-decoration: none; box-shadow: 0 8px 20px rgba(27,94,32,0.25);
            transition: all 0.25s ease; white-space: nowrap;
        }
        .btn-register:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(27,94,32,0.35); }

        /* Stats Row */
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 36px; }
        .stat-chip {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border-radius: 24px; padding: 24px;
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            display: flex; align-items: center; gap: 16px;
        }
        .stat-chip-icon {
            width: 52px; height: 52px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center; font-size: 22px;
            flex-shrink: 0;
        }
        .icon-teal { background: #e0f2f1; color: #00695c; }
        .icon-purple { background: #ede7f6; color: #6d28d9; }
        .icon-rose { background: #fff1f2; color: #e11d48; }
        .stat-chip-info {}
        .stat-chip-num { font-family: var(--font-heading); font-size: 30px; font-weight: 800; color: #0f172a; line-height: 1; }
        .stat-chip-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-top: 4px; }

        /* Flash Message */
        .flash-success {
            background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;
            padding: 14px 20px; border-radius: 16px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px;
        }

        /* Midwife Group Cards */
        .midwife-group { margin-bottom: 36px; }
        .midwife-group-header {
            display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
            padding: 20px 24px;
            background: rgba(255,255,255,0.85); backdrop-filter: blur(16px);
            border-radius: 20px 20px 0 0;
            border: 1px solid rgba(226,232,240,0.6);
            border-bottom: 2px solid #00c853;
        }
        .midwife-avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: linear-gradient(135deg, #1b5e20, #00695c);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 17px;
        }
        .midwife-name { font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #0f172a; }
        .midwife-area { font-size: 12px; color: #64748b; font-weight: 600; margin-top: 1px; }
        .midwife-count-badge {
            margin-left: auto;
            background: #e8f5e9; color: #2e7d32;
            border-radius: 20px; padding: 5px 14px;
            font-size: 13px; font-weight: 700;
        }

        /* Mother Cards Grid */
        .mothers-grid {
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
            padding: 20px;
            background: rgba(255,255,255,0.6); backdrop-filter: blur(10px);
            border-radius: 0 0 20px 20px;
            border: 1px solid rgba(226,232,240,0.6);
            border-top: none;
        }

        .mother-card {
            background: #fff; border-radius: 18px;
            padding: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            transition: all 0.25s ease;
            text-decoration: none; color: inherit; display: block;
        }
        .mother-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
            border-color: #bbf7d0;
        }
        .mother-card-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
        .mother-initials {
            width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
            background: linear-gradient(135deg, #e8f5e9, #e0f2f1);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-heading); font-size: 16px; font-weight: 800;
            color: #1b5e20;
        }
        .mother-card-info { flex: 1; min-width: 0; }
        .mother-card-name { font-family: var(--font-heading); font-size: 15px; font-weight: 800; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .mother-card-phone { font-size: 12px; color: #64748b; margin-top: 2px; }
        .mother-card-id { font-size: 11px; color: #94a3b8; font-weight: 700; }

        .mother-card-details { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 14px; }
        .detail-tag {
            display: flex; align-items: center; gap: 5px;
            background: #f8fafc; border-radius: 8px; padding: 4px 10px;
            font-size: 11px; font-weight: 600; color: #475569;
        }
        .detail-tag i { color: #94a3b8; font-size: 10px; }

        .mother-card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 12px; }
        .edd-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .edd-value { font-size: 13px; font-weight: 700; color: #0f172a; margin-top: 1px; }
        .view-btn {
            display: flex; align-items: center; gap: 5px;
            background: #e8f5e9; color: #2e7d32;
            border: none; border-radius: 10px; padding: 6px 12px;
            font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none;
            transition: all 0.2s ease;
        }
        .view-btn:hover { background: #c8e6c9; }

        .empty-group {
            padding: 40px; text-align: center;
            background: rgba(255,255,255,0.6);
            border-radius: 0 0 20px 20px;
            border: 1px solid rgba(226,232,240,0.6); border-top: none;
            color: #94a3b8; font-size: 14px;
        }

        .no-results {
            text-align: center; padding: 80px 20px;
        }
        .no-results i { font-size: 48px; color: #cbd5e1; margin-bottom: 16px; }
        .no-results p { font-size: 16px; color: #94a3b8; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 20px 10px; }
            .sidebar-brand span, .nav-item span, .btn-schedule span { display: none; }
            .main-wrapper { margin-left: 80px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
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
            <li class="nav-item">
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
            <li class="nav-item">
                <a href="{{ route('admin.alerts.index') }}">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>High-Risk Alerts</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.triposha.index') }}">
                    <i class="fa-solid fa-book-medical"></i>
                    <span>Triposha Book</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.attendances.index') }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>Clinic Attendances</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#reports">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span>Vaccine Reports</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <a href="{{ route('admin.mothers.create') }}" class="btn-schedule" style="text-decoration:none;">
                <i class="fa-solid fa-user-plus"></i>
                <span>Register Mother</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">Mothers Registry</div>

            <form method="GET" action="{{ route('admin.mothers.index') }}" class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="motherSearch" name="search" class="search-input"
                    placeholder="Search by name, phone, address..."
                    value="{{ $search ?? '' }}">
            </form>

            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <!-- Content -->
        <main class="content-container">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Mothers Registry</h1>
                    <p class="page-sub">All registered mothers grouped by assigned midwife</p>
                </div>
                <a href="{{ route('admin.mothers.create') }}" class="btn-register">
                    <i class="fa-solid fa-user-plus"></i>
                    Register New Mother
                </a>
            </div>

            <!-- Stats -->
            <div class="stats-row">
                <div class="stat-chip">
                    <div class="stat-chip-icon icon-teal">
                        <i class="fa-solid fa-user-nurse"></i>
                    </div>
                    <div class="stat-chip-info">
                        <div class="stat-chip-num">{{ $totalMothers > 0 ? $totalMothers : '--' }}</div>
                        <div class="stat-chip-label">Total Mothers</div>
                    </div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-icon icon-purple">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <div class="stat-chip-info">
                        <div class="stat-chip-num">{{ $totalMidwives > 0 ? $totalMidwives : '--' }}</div>
                        <div class="stat-chip-label">Midwives Assigned</div>
                    </div>
                </div>
                <div class="stat-chip">
                    <div class="stat-chip-icon icon-rose">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <div class="stat-chip-info">
                        <div class="stat-chip-num">
                            {{ $midwives->sum(fn($m) => $m->mothers->count()) > 0 ? number_format($midwives->avg(fn($m) => $m->mothers->count()), 1) : '--' }}
                        </div>
                        <div class="stat-chip-label">Avg. Mothers / Midwife</div>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($search && $midwives->every(fn($m) => $m->mothers->isEmpty()))
                <div class="no-results">
                    <i class="fa-solid fa-magnifying-glass" style="display:block;"></i>
                    <p>No mothers found matching <strong>"{{ $search }}"</strong></p>
                    <a href="{{ route('admin.mothers.index') }}" style="color:#00c853; font-weight:700; font-size:14px; margin-top:10px; display:inline-block;">Clear search</a>
                </div>
            @else
                @forelse($midwives as $midwife)
                    @if(!$search || $midwife->mothers->isNotEmpty())
                        <div class="midwife-group">
                            <div class="midwife-group-header">
                                <div class="midwife-avatar">
                                    {{ strtoupper(substr($midwife->midwife_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="midwife-name">{{ $midwife->midwife_name }}</div>
                                    <div class="midwife-area">
                                        <i class="fa-solid fa-location-dot"></i>
                                        {{ $midwife->area->area_name ?? 'No Area Assigned' }}
                                    </div>
                                </div>
                                <span class="midwife-count-badge">
                                    <i class="fa-solid fa-users"></i>
                                    {{ $midwife->mothers->count() }} {{ Str::plural('mother', $midwife->mothers->count()) }}
                                </span>
                            </div>

                            @if($midwife->mothers->isEmpty())
                                <div class="empty-group">
                                    <i class="fa-regular fa-folder-open" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                    No mothers assigned to this midwife yet.
                                </div>
                            @else
                                <div class="mothers-grid">
                                    @foreach($midwife->mothers as $mother)
                                        @php
                                            $initials = collect(explode(' ', $mother->mother_name))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('');
                                            $latestPregnancy = $mother->pregnancyHistories->last();
                                        @endphp
                                        <a href="{{ route('admin.mothers.show', $mother->mother_id) }}" class="mother-card">
                                            <div class="mother-card-top">
                                                <div class="mother-initials">{{ $initials }}</div>
                                                <div class="mother-card-info">
                                                    <div class="mother-card-name">{{ $mother->mother_name }}</div>
                                                    <div class="mother-card-phone">
                                                        <i class="fa-solid fa-phone" style="font-size:10px;"></i>
                                                        {{ $mother->phone_no ?? 'No phone' }}
                                                    </div>
                                                    <div class="mother-card-id">ID #{{ $mother->mother_id }}</div>
                                                </div>
                                            </div>

                                            <div class="mother-card-details">
                                                @if($mother->age)
                                                    <div class="detail-tag">
                                                        <i class="fa-solid fa-cake-candles"></i>
                                                        Age {{ $mother->age }}
                                                    </div>
                                                @endif
                                                @if($mother->bmi)
                                                    <div class="detail-tag">
                                                        <i class="fa-solid fa-weight-scale"></i>
                                                        BMI {{ $mother->bmi }}
                                                    </div>
                                                @endif
                                                @if($mother->husband_name)
                                                    <div class="detail-tag">
                                                        <i class="fa-solid fa-ring"></i>
                                                        {{ Str::limit($mother->husband_name, 14) }}
                                                    </div>
                                                @endif
                                                @if($mother->address)
                                                    <div class="detail-tag" style="width:100%;">
                                                        <i class="fa-solid fa-map-pin"></i>
                                                        {{ Str::limit($mother->address, 36) }}
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="mother-card-footer">
                                                <div>
                                                    <div class="edd-label">Expected Delivery</div>
                                                    <div class="edd-value">
                                                        @if($latestPregnancy && $latestPregnancy->expected_date_of_delivery)
                                                            {{ \Carbon\Carbon::parse($latestPregnancy->expected_date_of_delivery)->format('d M Y') }}
                                                        @else
                                                            <span style="color:#94a3b8;">Not recorded</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="view-btn">
                                                    View <i class="fa-solid fa-arrow-right"></i>
                                                </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="no-results">
                        <i class="fa-solid fa-stethoscope" style="display:block;"></i>
                        <p>No midwives found. Please add midwives first.</p>
                    </div>
                @endforelse
            @endif
        </main>
    </div>
</body>
</html>

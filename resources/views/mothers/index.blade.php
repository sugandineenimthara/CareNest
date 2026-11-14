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

        
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        
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

        
        .flash-success {
            background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46;
            padding: 14px 20px; border-radius: 16px; margin-bottom: 24px;
            display: flex; align-items: center; gap: 10px; font-weight: 600; font-size: 14px;
        }

        
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

        
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 0 0 20px 20px;
            border: 1px solid rgba(226,232,240,0.6);
            border-top: none;
        }
        .mothers-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .mothers-table th {
            text-align: left;
            padding: 14px 18px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
            border-bottom: 1.5px solid #e2e8f0;
            white-space: nowrap;
        }
        .mothers-table td {
            padding: 14px 18px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .mothers-table tr:hover td {
            background: #f8fafc;
        }
        .mothers-table tr:last-child td {
            border-bottom: none;
        }
        .row-name a {
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .row-name a:hover {
            color: #00c853;
        }
        .row-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-view-row {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e8f5e9;
            color: #2e7d32;
            border-radius: 10px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-view-row:hover {
            background: #c8e6c9;
            color: #1b5e20;
        }

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

        
        .area-pills {
            display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 24px;
        }
        .area-pill {
            padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 700;
            text-decoration: none; border: 1.5px solid #cbd5e1; color: #475569; background: white;
            transition: all 0.2s ease;
        }
        .area-pill:hover, .area-pill.active {
            background: #1b4d3e; color: white; border-color: #1b4d3e;
        }
        .pill-label {
            font-size: 13px; font-weight: 700; color: #64748b; margin-right: 4px;
        }

        
        .search-box {
            position: relative;
        }
        .search-suggestions-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            z-index: 999;
            max-height: 380px;
            overflow-y: auto;
            display: none;
            padding: 8px;
        }
        .suggestion-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 12px;
            text-decoration: none;
            color: #1e293b;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .suggestion-item:hover, .suggestion-item.active {
            background: #f0fdf4;
            color: #15803d;
        }
        .suggestion-item-main {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .suggestion-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
        }
        .suggestion-name {
            font-weight: 700;
            font-size: 14px;
        }
        .suggestion-name mark {
            background: #fef08a;
            color: #854d0e;
            padding: 0 2px;
            border-radius: 2px;
            font-weight: 800;
        }
        .suggestion-sub {
            font-size: 12px;
            color: #64748b;
        }
        .suggestion-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
        }
        .suggestions-count {
            padding: 8px 12px 6px;
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 20px 10px; }
            .sidebar-brand span, .nav-item span, .btn-schedule span { display: none; }
            .main-wrapper { margin-left: 80px; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    
    @include('partials.sidebar')

    
    <div class="main-wrapper">
        
        <header class="top-header">
            <div class="header-left">Mothers Registry</div>

            @php
                $role = auth()->user()->role ?? 'admin';
                $prefix = $role === 'midwife' ? 'midwife.' : 'admin.';
            @endphp

            <form method="GET" action="{{ route($prefix . 'mothers.index') }}" class="search-box" id="motherSearchForm" autocomplete="off">
                @if(request('area_id'))
                    <input type="hidden" name="area_id" value="{{ request('area_id') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="motherSearch" name="search" class="search-input"
                    placeholder="Search by name, phone, address..."
                    value="{{ $search ?? '' }}" autocomplete="off">
                <div class="search-suggestions-dropdown" id="motherSuggestions"></div>
            </form>

            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        
        <main class="content-container">
            <div class="page-header">
                <div>
                    <h1 class="page-title">Mothers Registry</h1>
                    <p class="page-sub">All registered mothers grouped by assigned midwife</p>
                </div>
                <a href="{{ route($prefix . 'mothers.create') }}" class="btn-register">
                    <i class="fa-solid fa-user-plus"></i>
                    Register New Mother
                </a>
            </div>

            
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
                            {{ $totalMidwives > 0 ? number_format($totalMothers / $totalMidwives, 1) : '--' }}
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

            <div class="area-pills">
                <span class="pill-label"><i class="fa-solid fa-filter"></i> Filter Area:</span>
                <a href="{{ route($prefix . 'mothers.index', array_filter(['search' => $search])) }}" class="area-pill {{ empty($areaFilter) ? 'active' : '' }}">
                    All Areas ({{ $totalMothers }})
                </a>
                @foreach($allAreas as $a)
                    <a href="{{ route($prefix . 'mothers.index', array_filter(['area_id' => $a->area_id, 'search' => $search])) }}" class="area-pill {{ $areaFilter == $a->area_id ? 'active' : '' }}">
                        {{ $a->area_name }}
                    </a>
                @endforeach
            </div>

            @if($search || $areaFilter)
                <div style="font-size:14px; color:#64748b; margin-bottom: 20px;">
                    Showing results
                    @if($search) for "<strong>{{ $search }}</strong>"@endif
                    @if($areaFilter && $allAreas->firstWhere('area_id', $areaFilter)) in area "<strong>{{ $allAreas->firstWhere('area_id', $areaFilter)->area_name }}</strong>"@endif
                    — <a href="{{ route($prefix . 'mothers.index') }}" style="color:#00c853; font-weight:700;">Clear filters</a>
                </div>
            @endif

            @php
                $hasAnyMothers = $areas->some(fn($a) => $a->filtered_mothers->isNotEmpty()) || ($unassignedMothers && $unassignedMothers->isNotEmpty());
            @endphp

            @if($search && !$hasAnyMothers)
                <div class="no-results">
                    <i class="fa-solid fa-magnifying-glass" style="display:block;"></i>
                    <p>No mothers found matching <strong>"{{ $search }}"</strong></p>
                    <a href="{{ route($prefix . 'mothers.index') }}" style="color:#00c853; font-weight:700; font-size:14px; margin-top:10px; display:inline-block;">Clear search</a>
                </div>
            @else
                @forelse($areas as $area)
                    @if(!$search || $area->filtered_mothers->isNotEmpty())
                        <div class="midwife-group">
                            <div class="midwife-group-header">
                                <div class="midwife-avatar">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div>
                                    <div class="midwife-name">{{ $area->area_name }}</div>
                                    <div class="midwife-area">
                                        <i class="fa-solid fa-user-nurse" style="color: #00c853;"></i>
                                        Assigned Midwives: 
                                        @if($area->midwives->count() > 0)
                                            <strong>{{ $area->midwives->pluck('midwife_name')->filter()->unique()->join(', ') }}</strong>
                                        @else
                                            <span style="color:#94a3b8;">None Assigned</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="midwife-count-badge">
                                    <i class="fa-solid fa-users"></i>
                                    {{ $area->filtered_mothers->count() }} {{ Str::plural('mother', $area->filtered_mothers->count()) }}
                                </span>
                            </div>

                            @if($area->filtered_mothers->isEmpty())
                                <div class="empty-group">
                                    <i class="fa-regular fa-folder-open" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                                    No mothers registered in {{ $area->area_name }} yet.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="mothers-table">
                                        <thead>
                                            <tr>
                                                <th>Mother Name &amp; ID</th>
                                                <th>Age / DOB</th>
                                                <th>Clinical Metrics</th>
                                                <th>Husband</th>
                                                <th>Address</th>
                                                <th>Pregnancy / EDD</th>
                                                <th style="text-align:right;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($area->filtered_mothers as $mother)
                                                @php
                                                    $latestPregnancy = $mother->pregnancyHistories->last();
                                                @endphp
                                                <tr class="mother-row">
                                                    <td>
                                                        <div class="row-name">
                                                            <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}">{{ $mother->mother_name }}</a>
                                                        </div>
                                                        <div class="row-sub">
                                                            ID #{{ $mother->mother_id }}
                                                            @if($mother->phone_no) &bull; <i class="fa-solid fa-phone" style="font-size:10px;"></i> {{ $mother->phone_no }} @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div style="font-weight:700; color:#1e293b;">{{ $mother->age ? $mother->age . ' yrs' : '—' }}</div>
                                                        <div style="font-size:11px; color:#64748b;">{{ $mother->date_of_birth ? $mother->date_of_birth->format('d M Y') : '—' }}</div>
                                                    </td>
                                                    <td>
                                                        @if($mother->blood_pressure)
                                                            <span style="display:inline-flex; align-items:center; gap:5px; font-weight:700; font-size:12px; {{ $mother->isHighBloodPressure() ? 'color:#dc2626;' : 'color:#166534;' }}">
                                                                <i class="fa-solid fa-heart-pulse"></i> {{ $mother->blood_pressure }}
                                                                @if($mother->isHighBloodPressure())
                                                                    <span style="font-size:9px; background:#dc2626; color:white; border-radius:4px; padding:1px 5px;">High Risk</span>
                                                                @endif
                                                            </span>
                                                        @else
                                                            <span style="color:#94a3b8;">—</span>
                                                        @endif
                                                        @if($mother->bmi)
                                                            <div style="font-size:11px; color:#64748b; margin-top:2px;">BMI: <strong>{{ $mother->bmi }}</strong></div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div style="font-weight:600; color:#1e293b;">{{ $mother->husband_name ?? '—' }}</div>
                                                        @if($mother->husband_occupation)
                                                            <div style="font-size:11px; color:#64748b;">{{ $mother->husband_occupation }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div style="font-size:12px; color:#475569; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                            {{ $mother->address ?? '—' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($latestPregnancy && $latestPregnancy->expected_date_of_delivery)
                                                            <div style="font-size:12px; font-weight:700; color:#1b5e20;">
                                                                EDD: {{ \Carbon\Carbon::parse($latestPregnancy->expected_date_of_delivery)->format('d M Y') }}
                                                            </div>
                                                            <div style="font-size:11px; color:#64748b;">
                                                                {{ $latestPregnancy->is_delivered ? 'Delivered' : 'Active Pregnancy' }}
                                                            </div>
                                                        @else
                                                            <span style="color:#94a3b8; font-size:12px;">Not recorded</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align:right;">
                                                        <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}" class="btn-view-row">
                                                            <span>View Profile</span>
                                                            <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                @empty
                    <div class="no-results">
                        <i class="fa-solid fa-hospital" style="display:block;"></i>
                        <p>No clinic areas found in the system.</p>
                    </div>
                @endforelse

                @if(isset($unassignedMothers) && $unassignedMothers->isNotEmpty())
                    <div class="midwife-group">
                        <div class="midwife-group-header">
                            <div class="midwife-avatar" style="background:#f1f5f9; color:#64748b;">
                                <i class="fa-solid fa-question"></i>
                            </div>
                            <div>
                                <div class="midwife-name">Unassigned Clinic Area</div>
                                <div class="midwife-area" style="color:#e11d48;">
                                    Mothers requiring area/midwife assignment
                                </div>
                            </div>
                            <span class="midwife-count-badge">
                                <i class="fa-solid fa-users"></i>
                                {{ $unassignedMothers->count() }} {{ Str::plural('mother', $unassignedMothers->count()) }}
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="mothers-table">
                                <thead>
                                    <tr>
                                        <th>Mother Name &amp; ID</th>
                                        <th>Phone</th>
                                        <th>Husband</th>
                                        <th>Address</th>
                                        <th style="text-align:right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unassignedMothers as $mother)
                                        <tr class="mother-row">
                                            <td>
                                                <div class="row-name">
                                                    <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}">{{ $mother->mother_name }}</a>
                                                </div>
                                                <div class="row-sub">ID #{{ $mother->mother_id }}</div>
                                            </td>
                                            <td>{{ $mother->phone_no ?? '—' }}</td>
                                            <td>{{ $mother->husband_name ?? '—' }}</td>
                                            <td>{{ $mother->address ?? '—' }}</td>
                                            <td style="text-align:right;">
                                                <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}" class="btn-view-row">
                                                    <span>View Profile</span>
                                                    <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </main>
    </div>

    
    <script>
        const allMothers = @json($allMothersList ?? []);
        const searchInput = document.getElementById('motherSearch');
        const suggestionsBox = document.getElementById('motherSuggestions');

        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text || '';
            return div.innerHTML;
        }

        function renderSuggestions(query) {
            if (!query || query.trim().length === 0) {
                suggestionsBox.style.display = 'none';
                suggestionsBox.innerHTML = '';
                filterCards('');
                return;
            }

            const q = query.trim().toLowerCase();
            const matched = allMothers.filter(m => {
                return (m.name && m.name.toLowerCase().includes(q)) ||
                       (m.phone && m.phone.toLowerCase().includes(q)) ||
                       (String(m.id) === q) ||
                       (m.area && m.area.toLowerCase().includes(q));
            });

            if (matched.length === 0) {
                suggestionsBox.innerHTML = `
                    <div style="padding: 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <i class="fa-solid fa-circle-question" style="margin-bottom: 6px; font-size: 18px; display: block;"></i>
                        No records found for "<strong>${escapeHtml(query)}</strong>"
                    </div>`;
                suggestionsBox.style.display = 'block';
                filterCards(q);
                return;
            }

            const regex = new RegExp(`(${escapeRegex(query.trim())})`, 'gi');
            let html = `<div class="suggestions-count">${matched.length} ${matched.length === 1 ? 'Record Found' : 'Records Found'}</div>`;
            
            matched.slice(0, 10).forEach(item => {
                const highlightedName = escapeHtml(item.name).replace(regex, '<mark>$1</mark>');
                const initial = item.name ? item.name.charAt(0).toUpperCase() : 'M';
                html += `
                    <a href="${item.url}" class="suggestion-item">
                        <div class="suggestion-item-main">
                            <div class="suggestion-avatar">${initial}</div>
                            <div>
                                <div class="suggestion-name">${highlightedName}</div>
                                <div class="suggestion-sub">${item.phone ? escapeHtml(item.phone) + ' • ' : ''}${escapeHtml(item.area)}</div>
                            </div>
                        </div>
                        <span class="suggestion-badge">ID #${item.id}</span>
                    </a>
                `;
            });

            suggestionsBox.innerHTML = html;
            suggestionsBox.style.display = 'block';
            filterCards(q);
        }

        function filterCards(q) {
            const groups = document.querySelectorAll('.midwife-group');
            if (!groups.length) return;

            groups.forEach(group => {
                const rows = group.querySelectorAll('.mother-row');
                let visibleInGroup = 0;
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (!q || text.includes(q)) {
                        row.style.display = '';
                        visibleInGroup++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                if (!q || visibleInGroup > 0) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        }

        if (searchInput && suggestionsBox) {
            searchInput.addEventListener('input', function(e) {
                renderSuggestions(e.target.value);
            });

            searchInput.addEventListener('focus', function(e) {
                if (e.target.value.trim().length > 0) {
                    renderSuggestions(e.target.value);
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                    suggestionsBox.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

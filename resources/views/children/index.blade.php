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

        
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        
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

        
        .content-container { padding: 36px 48px; display: flex; flex-direction: column; gap: 32px; }

        
        .alert-success {
            padding: 16px 20px; background: #ecfdf5; border: 1px solid #a7f3d0;
            border-radius: 14px; color: #065f46; font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }

        
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

        
        .table-responsive {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .children-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .children-table th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
            border-bottom: 1.5px solid #e2e8f0;
            white-space: nowrap;
        }
        .children-table td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }
        .children-table tr:hover td {
            background: #f8fafc;
        }
        .children-table tr:last-child td {
            border-bottom: none;
        }
        .row-name a {
            font-family: var(--font-heading);
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        .row-name a:hover {
            color: #0284c7;
        }
        .row-sub {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-view-row {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-view-row:hover {
            background: #bae6fd;
            color: #0369a1;
        }
        .btn-edit-row {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-edit-row:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .bcg-pill {
            display: inline-flex; align-items: center; gap: 6px; font-size: 11px;
            font-weight: 700; padding: 3px 8px; border-radius: 10px;
            white-space: nowrap;
        }
        .bcg-done { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .bcg-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .empty-area {
            text-align: center; padding: 32px; color: #94a3b8; font-size: 14px;
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
    </style>
</head>
<body>
    
    @include('partials.sidebar')

    @php
        $role = auth()->user()->role ?? 'admin';
        $prefix = $role === 'midwife' ? 'midwife.' : ($role === 'mother' ? 'mother.' : 'admin.');
    @endphp
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">{{ $role === 'mother' ? 'My Children' : 'Children Management' }}</div>
            <form method="GET" action="{{ route($prefix . 'children.index') }}" class="search-box" id="childSearchForm" autocomplete="off">
                @if(request('area_id'))
                    <input type="hidden" name="area_id" value="{{ request('area_id') }}">
                @endif
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="childSearch" name="search" class="search-input" placeholder="{{ $role === 'mother' ? 'Search among your children...' : 'Search child name, ID, or mother...' }}" value="{{ $search }}" autocomplete="off">
                <div class="search-suggestions-dropdown" id="childSuggestions"></div>
            </form>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <main class="content-container">
            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="action-bar">
                <div>
                    <h1 class="page-title">
                        <span>{{ $role === 'mother' ? 'My Children' : 'Children Registry' }}</span>
                        <span class="page-title-badge">{{ $role === 'mother' ? 'Family Records' : 'Separated by Clinic Areas' }}</span>
                    </h1>
                </div>
                <div style="display:flex; gap:12px;">
                    <a href="{{ route($prefix . 'immunizations.index') }}" class="btn-register" style="background:white; color:#1b4d3e; border:1px solid #cbd5e1; box-shadow:none;">
                        <i class="fa-solid fa-syringe" style="color:#00c853;"></i>
                        <span>Immunization Schedule</span>
                    </a>
                    @if(in_array($role, ['admin', 'provider', 'midwife']))
                    <a href="{{ route($prefix . 'children.create') }}" class="btn-register">
                        <i class="fa-solid fa-plus"></i>
                        <span>Register New Child</span>
                    </a>
                    @endif
                </div>
            </div>

            @if($role === 'mother')
                @if($search)
                    <div style="font-size:14px; color:#64748b;">
                        Showing search results for "<strong>{{ $search }}</strong>" —
                        <a href="{{ route('mother.children.index') }}" style="color:#00c853; font-weight:700;">Clear search</a>
                    </div>
                @endif

                @if($myChildren->count() > 0)
                    <div class="table-responsive">
                        <table class="children-table">
                            <thead>
                                <tr>
                                    <th>Child Name &amp; ID</th>
                                    <th>Gender</th>
                                    <th>Age / DOB</th>
                                    <th>Growth Metrics</th>
                                    <th>BCG Status</th>
                                    <th>Assigned Midwife &amp; Area</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($myChildren as $child)
                                    @php
                                        $idxBmi = $child->bmi ?: $child->calculateBmi();
                                    @endphp
                                    <tr class="child-row child-card">
                                        <td>
                                            <div class="row-name">
                                                <a href="{{ route('mother.immunizations.index') }}">{{ $child->display_name }}</a>
                                            </div>
                                            <div class="row-sub">
                                                ID #{{ $child->child_id }}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="display:inline-flex; align-items:center; gap:5px; font-weight:700; font-size:12px; {{ $child->gender === 'Female' ? 'color:#db2777;' : 'color:#0284c7;' }}">
                                                <i class="fa-solid {{ $child->gender === 'Female' ? 'fa-venus' : 'fa-mars' }}"></i>
                                                {{ $child->gender }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight:700; color:#1e293b;">{{ $child->age ?? 'Age N/A' }}</div>
                                            <div style="font-size:11px; color:#64748b;">DOB: {{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</div>
                                        </td>
                                        <td>
                                            <div style="font-size:12px; color:#334155; display:flex; flex-direction:column; gap:2px;">
                                                <span>Weight: <strong>{{ $child->weight ? $child->weight . ' kg' : ($child->birth_weight ? $child->birth_weight . ' kg' : '—') }}</strong></span>
                                                <span>Height: <strong>{{ $child->height ? $child->height . ' cm' : ($child->birth_length ? $child->birth_length . ' cm' : '—') }}</strong></span>
                                                @if($idxBmi)
                                                    <span style="color:#0369a1; font-weight:700;">BMI: {{ $idxBmi }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($child->bcg_vaccinated_at_birth)
                                                <span class="bcg-pill bcg-done" title="BCG vaccinated before 24hrs from birth">
                                                    <i class="fa-solid fa-check"></i> BCG Vaccinated
                                                </span>
                                            @else
                                                <span class="bcg-pill bcg-pending" title="BCG not yet recorded">
                                                    <i class="fa-solid fa-clock"></i> BCG Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size:12px; color:#1e293b;">
                                                <i class="fa-solid fa-user-nurse" style="color:#00c853;"></i>
                                                {{ $child->midwife->midwife_name ?? ($child->mother?->midwife?->midwife_name ?? 'Assigned Midwife') }}
                                            </div>
                                            <div style="font-size:11px; color:#64748b;">
                                                <i class="fa-solid fa-location-dot" style="color:#0284c7;"></i>
                                                {{ $child->area->area_name ?? ($child->midwife?->area?->area_name ?? 'Clinic Area') }}
                                            </div>
                                        </td>
                                        <td style="text-align:right;">
                                            <a href="{{ route('mother.immunizations.index') }}" class="btn-view-row">
                                                <i class="fa-solid fa-syringe"></i>
                                                <span>Immunizations</span>
                                                <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="background:white; border-radius:24px; padding:48px 24px; text-align:center; border:1px solid #e2e8f0; box-shadow:0 4px 16px rgba(0,0,0,0.03);">
                        <i class="fa-solid fa-baby-carriage" style="font-size:44px; color:#cbd5e1; display:block; margin-bottom:14px;"></i>
                        <h3 style="font-family:var(--font-heading); font-size:18px; font-weight:800; color:#1e293b; margin-bottom:6px;">No Children Registered Yet</h3>
                        <p style="color:#64748b; font-size:14px; max-width:480px; margin:0 auto;">
                            Your registered children will appear here once recorded by your area midwife. You can track their growth and immunizations here.
                        </p>
                    </div>
                @endif
            @else
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

                <div class="area-pills">
                    <span class="pill-label"><i class="fa-solid fa-filter"></i> Filter Area:</span>
                    <a href="{{ route($prefix . 'children.index', array_filter(['search' => $search])) }}" class="area-pill {{ empty($areaFilter) ? 'active' : '' }}">
                        All Areas ({{ $totalChildren }})
                    </a>
                    @foreach($allAreas as $a)
                        <a href="{{ route($prefix . 'children.index', array_filter(['area_id' => $a->area_id, 'search' => $search])) }}" class="area-pill {{ $areaFilter == $a->area_id ? 'active' : '' }}">
                            {{ $a->area_name }}
                        </a>
                    @endforeach
                </div>

                @if($search)
                    <div style="font-size:14px; color:#64748b;">
                        Showing search results for "<strong>{{ $search }}</strong>" —
                        <a href="{{ route($prefix . 'children.index') }}" style="color:#00c853; font-weight:700;">Clear search</a>
                    </div>
                @endif

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
                            <div class="table-responsive">
                                <table class="children-table">
                                    <thead>
                                        <tr>
                                            <th>Child Name &amp; ID</th>
                                            <th>Gender</th>
                                            <th>Age / DOB</th>
                                            <th>Mother Details</th>
                                            <th>Growth Metrics</th>
                                            <th>BCG Status</th>
                                            <th style="text-align:right;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($area->filtered_children as $child)
                                            @php
                                                $idxBmi = $child->bmi ?: $child->calculateBmi();
                                            @endphp
                                            <tr class="child-row child-card">
                                                <td>
                                                    <div class="row-name">
                                                        <a href="{{ route($prefix . 'children.show', $child->child_id) }}">{{ $child->display_name }}</a>
                                                    </div>
                                                    <div class="row-sub">
                                                        ID #{{ $child->child_id }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span style="display:inline-flex; align-items:center; gap:5px; font-weight:700; font-size:12px; {{ $child->gender === 'Female' ? 'color:#db2777;' : 'color:#0284c7;' }}">
                                                        <i class="fa-solid {{ $child->gender === 'Female' ? 'fa-venus' : 'fa-mars' }}"></i>
                                                        {{ $child->gender }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="font-weight:700; color:#1e293b;">{{ $child->age ?? 'Age N/A' }}</div>
                                                    <div style="font-size:11px; color:#64748b;">DOB: {{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</div>
                                                </td>
                                                <td>
                                                    @if($child->mother)
                                                        <div style="font-weight:700; font-size:13px;">
                                                            <a href="{{ route($prefix . 'mothers.show', $child->mother_id) }}" style="color:#059669; text-decoration:none;">
                                                                {{ $child->mother->mother_name }}
                                                            </a>
                                                        </div>
                                                        <div style="font-size:11px; color:#64748b;">
                                                            ID #{{ $child->mother_id }}
                                                            @if($child->mother->phone_no)
                                                                &bull; <i class="fa-solid fa-phone" style="font-size:10px;"></i> {{ $child->mother->phone_no }}
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span style="color:#94a3b8;">Not Assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div style="font-size:12px; color:#334155; display:flex; flex-direction:column; gap:2px;">
                                                        <span>Wt: <strong>{{ $child->weight ? $child->weight . ' kg' : ($child->birth_weight ? $child->birth_weight . ' kg' : '—') }}</strong></span>
                                                        <span>Ht: <strong>{{ $child->height ? $child->height . ' cm' : ($child->birth_length ? $child->birth_length . ' cm' : '—') }}</strong></span>
                                                        @if($idxBmi)
                                                            <span style="color:#0369a1; font-weight:700;">BMI: {{ $idxBmi }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($child->bcg_vaccinated_at_birth)
                                                        <span class="bcg-pill bcg-done" title="BCG vaccinated before 24hrs from birth">
                                                            <i class="fa-solid fa-check"></i> BCG Vaccinated
                                                        </span>
                                                    @else
                                                        <span class="bcg-pill bcg-pending" title="BCG not yet recorded">
                                                            <i class="fa-solid fa-clock"></i> BCG Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="text-align:right;">
                                                    <div style="display:inline-flex; align-items:center; gap:6px; justify-content:flex-end;">
                                                        @if(in_array($role, ['admin', 'provider', 'midwife']))
                                                            <a href="{{ route($prefix . 'children.edit', $child->child_id) }}" class="btn-edit-row" title="Edit child">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                                <span>Edit</span>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route($prefix . 'children.show', $child->child_id) }}" class="btn-view-row" title="View Schedule">
                                                            <span>Schedule</span>
                                                            <i class="fa-solid fa-arrow-right" style="font-size:10px;"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-area">
                                <i class="fa-solid fa-baby-carriage" style="font-size:32px; color:#cbd5e1; margin-bottom:8px; display:block;"></i>
                                No children currently registered in {{ $area->area_name }}.
                                @if(in_array($role, ['admin', 'provider', 'midwife']))
                                <div style="margin-top:10px;">
                                    <a href="{{ route($prefix . 'children.create', ['area_id' => $area->area_id]) }}" style="color:#00c853; font-weight:700; text-decoration:none;">
                                        + Register first child in this area
                                    </a>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </main>
    </div>

    
    <script>
        const allChildren = @json($allChildrenList ?? []);
        const childSearchInput = document.getElementById('childSearch');
        const childSuggestionsBox = document.getElementById('childSuggestions');

        function escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.innerText = text || '';
            return div.innerHTML;
        }

        function renderChildSuggestions(query) {
            if (!query || query.trim().length === 0) {
                childSuggestionsBox.style.display = 'none';
                childSuggestionsBox.innerHTML = '';
                filterChildCards('');
                return;
            }

            const q = query.trim().toLowerCase();
            const matched = allChildren.filter(c => {
                return (c.name && c.name.toLowerCase().includes(q)) ||
                       (c.mother && c.mother.toLowerCase().includes(q)) ||
                       (String(c.id) === q) ||
                       (c.area && c.area.toLowerCase().includes(q));
            });

            if (matched.length === 0) {
                childSuggestionsBox.innerHTML = `
                    <div style="padding: 16px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <i class="fa-solid fa-circle-question" style="margin-bottom: 6px; font-size: 18px; display: block;"></i>
                        No records found for "<strong>${escapeHtml(query)}</strong>"
                    </div>`;
                childSuggestionsBox.style.display = 'block';
                filterChildCards(q);
                return;
            }

            const regex = new RegExp(`(${escapeRegex(query.trim())})`, 'gi');
            let html = `<div class="suggestions-count">${matched.length} ${matched.length === 1 ? 'Record Found' : 'Records Found'}</div>`;
            
            matched.slice(0, 10).forEach(item => {
                const highlightedName = escapeHtml(item.name).replace(regex, '<mark>$1</mark>');
                const initial = item.name ? item.name.charAt(0).toUpperCase() : 'C';
                html += `
                    <a href="${item.url}" class="suggestion-item">
                        <div class="suggestion-item-main">
                            <div class="suggestion-avatar">${initial}</div>
                            <div>
                                <div class="suggestion-name">${highlightedName}</div>
                                <div class="suggestion-sub">${item.mother ? 'Mother: ' + escapeHtml(item.mother) + ' • ' : ''}${escapeHtml(item.area)}</div>
                            </div>
                        </div>
                        <span class="suggestion-badge">ID #${item.id}</span>
                    </a>
                `;
            });

            childSuggestionsBox.innerHTML = html;
            childSuggestionsBox.style.display = 'block';
            filterChildCards(q);
        }

        function filterChildCards(q) {
            const sections = document.querySelectorAll('.area-section');
            if (!sections.length) return;

            sections.forEach(section => {
                const rows = section.querySelectorAll('.child-row');
                let visibleCount = 0;
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (!q || text.includes(q)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                if (!q || visibleCount > 0) {
                    section.style.display = '';
                } else {
                    section.style.display = 'none';
                }
            });
        }

        if (childSearchInput && childSuggestionsBox) {
            childSearchInput.addEventListener('input', function(e) {
                renderChildSuggestions(e.target.value);
            });

            childSearchInput.addEventListener('focus', function(e) {
                if (e.target.value.trim().length > 0) {
                    renderChildSuggestions(e.target.value);
                }
            });

            document.addEventListener('click', function(e) {
                if (!childSearchInput.contains(e.target) && !childSuggestionsBox.contains(e.target)) {
                    childSuggestionsBox.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thriposha Book - CareNest</title>
    <meta name="description" content="Manage Thriposha packet distribution for mothers and children. Record batch distributions with stock tracking.">

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
            --accent: #f59e0b;
            --accent-light: #fef3c7;
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
        .header-left { font-family: var(--font-heading); font-size: 22px; font-weight: 800; color: #1b4d3e; }
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
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .stat-val { font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: var(--text-dark); }
        .stat-lbl { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px; margin-top: 2px; }

        /* ── Distribution Form Card ── */
        .dist-card { background: white; border: 1px solid #e2e8f0; border-radius: 24px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .dist-card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
        .dist-card-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; color: white; }
        .dist-card-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: var(--text-dark); }
        .dist-card-sub { font-size: 13px; color: var(--text-muted); }

        /* Session top row */
        .session-bar { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 16px; align-items: end; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 24px; }
        .form-group label { font-size: 12px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; }
        .form-input, .form-select { width: 100%; padding: 11px 14px; background: white; border: 1.5px solid #e2e8f0; border-radius: 12px; font-family: var(--font-main); font-size: 14px; color: var(--text-dark); outline: none; transition: border-color 0.2s; }
        .form-input:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,200,83,0.12); }

        /* Stock tracker */
        .stock-tracker { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .stock-box { border-radius: 14px; padding: 16px 20px; text-align: center; }
        .stock-box.opening { background: #e0f2fe; border: 1px solid #7dd3fc; }
        .stock-box.distributed { background: #fef3c7; border: 1px solid #fcd34d; }
        .stock-box.remaining { background: #dcfce7; border: 1px solid #86efac; }
        .stock-box.low { background: #fee2e2; border: 1px solid #fca5a5; }
        .stock-num { font-family: var(--font-heading); font-size: 32px; font-weight: 800; }
        .stock-box.opening .stock-num { color: #0369a1; }
        .stock-box.distributed .stock-num { color: #d97706; }
        .stock-box.remaining .stock-num { color: #16a34a; }
        .stock-box.low .stock-num { color: #dc2626; }
        .stock-lbl { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; margin-top: 4px; color: var(--text-muted); }

        /* Batch table */
        .batch-table-wrapper { border: 1.5px solid #e2e8f0; border-radius: 16px; overflow: hidden; margin-bottom: 18px; }
        .batch-table { width: 100%; border-collapse: collapse; }
        .batch-table thead th { background: linear-gradient(135deg, #1b5e20, #00695c); color: white; padding: 13px 16px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; text-align: left; }
        .batch-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .batch-table tbody tr:hover { background: #f8fafc; }
        .batch-table tbody tr:last-child { border-bottom: none; }
        .batch-table td { padding: 10px 12px; }
        .td-num { text-align: center; font-weight: 700; color: var(--text-muted); font-size: 13px; }
        .td-input { width: 100%; padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: var(--font-main); font-size: 13px; outline: none; transition: border-color 0.2s; background: white; }
        .td-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,200,83,0.1); }
        .td-select { width: 100%; padding: 8px 10px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: var(--font-main); font-size: 13px; outline: none; background: white; transition: border-color 0.2s; }
        .td-select:focus { border-color: var(--primary); }
        .btn-del-row { background: #fee2e2; border: none; color: #dc2626; width: 30px; height: 30px; border-radius: 8px; cursor: pointer; font-size: 14px; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .btn-del-row:hover { background: #fecaca; transform: scale(1.1); }

        /* Action buttons */
        .table-actions { display: flex; align-items: center; gap: 12px; }
        .btn-add-row { display: flex; align-items: center; gap: 8px; padding: 11px 20px; background: #f0fdf4; border: 1.5px dashed #86efac; border-radius: 12px; color: #16a34a; font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.2s; }
        .btn-add-row:hover { background: #dcfce7; border-color: var(--primary); }
        .btn-save { display: flex; align-items: center; gap: 8px; padding: 12px 28px; background: linear-gradient(135deg, #00c853, #00a843); border: none; border-radius: 14px; color: white; font-family: var(--font-heading); font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 6px 20px rgba(0,200,83,0.35); transition: all 0.25s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,200,83,0.45); }

        /* ── History Section ── */
        .section-header-row { display: flex; justify-content: space-between; align-items: center; }
        .section-title { font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: var(--text-dark); }

        /* Filter bar */
        .filter-bar { display: flex; gap: 12px; align-items: center; background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 14px 20px; }
        .filter-bar input { padding: 9px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-family: var(--font-main); font-size: 13px; outline: none; }
        .filter-bar input:focus { border-color: var(--primary); }
        .btn-filter { padding: 9px 18px; background: var(--primary-dark); color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 13px; cursor: pointer; }

        /* Session block */
        .session-block { background: white; border: 1px solid #e2e8f0; border-radius: 20px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.04); }
        .session-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border-bottom: 1px solid #d1fae5; cursor: pointer; }
        .session-date { font-family: var(--font-heading); font-size: 17px; font-weight: 800; color: #065f46; }
        .session-label-badge { background: #d1fae5; color: #065f46; border-radius: 8px; padding: 4px 12px; font-size: 12px; font-weight: 700; }
        .session-meta { display: flex; gap: 16px; align-items: center; }
        .meta-pill { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; }
        .meta-pill .dot { width: 8px; height: 8px; border-radius: 50%; }
        .session-body { padding: 16px 24px; }
        .hist-table { width: 100%; border-collapse: collapse; }
        .hist-table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: var(--text-muted); padding: 10px 12px; border-bottom: 1px solid #f1f5f9; text-align: left; }
        .hist-table td { padding: 11px 12px; font-size: 14px; border-bottom: 1px solid #f8fafc; }
        .hist-table tr:last-child td { border-bottom: none; }
        .type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 8px; font-size: 12px; font-weight: 700; }
        .type-mother { background: #ede9fe; color: #7c3aed; }
        .type-child  { background: #dbeafe; color: #1d4ed8; }
        .pkt-badge { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; border-radius: 8px; padding: 3px 10px; font-size: 13px; font-weight: 700; }
        .stock-summary-bar { display: flex; gap: 14px; margin-top: 14px; padding: 14px; background: #f8fafc; border-radius: 12px; }
        .ssb-item { flex: 1; text-align: center; }
        .ssb-val { font-family: var(--font-heading); font-size: 22px; font-weight: 800; }
        .ssb-lbl { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-del-record { background: none; border: none; color: #cbd5e1; cursor: pointer; font-size: 14px; padding: 4px 8px; border-radius: 6px; transition: all 0.2s; }
        .btn-del-record:hover { color: #ef4444; background: #fee2e2; }
        .empty-state { text-align: center; padding: 60px 24px; color: var(--text-muted); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.3; color: var(--accent); }
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
        <li class="nav-item active"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-box-open"></i><span>Thriposha Book</span></a></li>
        <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
        <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
    </ul>
</aside>

<!-- ═══════════════════════════ MAIN ═══════════════════════════════ -->
<div class="main-wrapper">
    <!-- Header -->
    <header class="top-header">
        <div class="header-left">
            <i class="fa-solid fa-box-open" style="color:#f59e0b; margin-right:10px;"></i>
            Thriposha Distribution Book
        </div>
        <div class="header-right">
            @include('partials.top-header-user')
        </div>
    </header>

    <!-- Content -->
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

        {{-- Stats Row --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="fa-solid fa-box-open"></i></div>
                <div>
                    <div class="stat-val">{{ number_format($totalPacketsEver) }}</div>
                    <div class="stat-lbl">Total Pkts Distributed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#ede9fe; color:#7c3aed;"><i class="fa-solid fa-calendar-days"></i></div>
                <div>
                    <div class="stat-val">{{ $totalSessionsEver }}</div>
                    <div class="stat-lbl">Distribution Sessions</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#fce7f3; color:#be185d;"><i class="fa-solid fa-person-dress"></i></div>
                <div>
                    <div class="stat-val">{{ $mothersServed }}</div>
                    <div class="stat-lbl">Mothers Served</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#dbeafe; color:#1d4ed8;"><i class="fa-solid fa-baby"></i></div>
                <div>
                    <div class="stat-val">{{ $childrenServed }}</div>
                    <div class="stat-lbl">Children Served</div>
                </div>
            </div>
        </div>

        {{-- ═══════════ NEW DISTRIBUTION FORM ═══════════ --}}
        <div class="dist-card">
            <div class="dist-card-header">
                <div class="dist-card-icon"><i class="fa-solid fa-plus"></i></div>
                <div>
                    <div class="dist-card-title">Record New Distribution Session</div>
                    <div class="dist-card-sub">Set the date, enter opening stock, then add recipients row by row — save everything at once</div>
                </div>
            </div>

            <form id="batchForm" method="POST" action="{{ route('admin.triposha.store.batch') }}">
                @csrf

                {{-- Session Details --}}
                <div class="session-bar">
                    <div class="form-group">
                        <label for="issuing_date"><i class="fa-solid fa-calendar"></i> Distribution Date <span style="color:#e11d48;">*</span></label>
                        <input type="date" id="issuing_date" name="issuing_date" class="form-input"
                               value="{{ old('issuing_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="session_label"><i class="fa-solid fa-tag"></i> Session Name</label>
                        <input type="text" id="session_label" name="session_label" class="form-input"
                               placeholder="e.g. Morning Session" value="{{ old('session_label', 'Distribution Session') }}">
                    </div>
                    <div class="form-group">
                        <label for="opening_stock"><i class="fa-solid fa-warehouse"></i> Opening Stock (Pkts in Storage)</label>
                        <input type="number" id="opening_stock" name="opening_stock" class="form-input"
                               placeholder="e.g. 200" min="0" value="{{ old('opening_stock') }}" oninput="updateStockTracker()">
                    </div>
                    <div>
                        <button type="button" class="btn-add-row" onclick="addRow()" style="height:45px; margin-top:20px;">
                            <i class="fa-solid fa-plus"></i> Add Row
                        </button>
                    </div>
                </div>

                {{-- Stock Tracker --}}
                <div class="stock-tracker">
                    <div class="stock-box opening">
                        <div class="stock-num" id="tracker-opening">—</div>
                        <div class="stock-lbl"><i class="fa-solid fa-warehouse"></i> Opening Stock</div>
                    </div>
                    <div class="stock-box distributed">
                        <div class="stock-num" id="tracker-distributed">0</div>
                        <div class="stock-lbl"><i class="fa-solid fa-arrow-right"></i> Distributed Today</div>
                    </div>
                    <div class="stock-box remaining" id="remaining-box">
                        <div class="stock-num" id="tracker-remaining">—</div>
                        <div class="stock-lbl"><i class="fa-solid fa-boxes-stacked"></i> Remaining in Storage</div>
                    </div>
                </div>

                {{-- Batch Table --}}
                <div class="batch-table-wrapper">
                    <table class="batch-table" id="batchTable">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th style="width:120px;">Recipient</th>
                                <th>Name (Mother / Child)</th>
                                <th style="width:140px;">No. of Packets</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="batchRows">
                            {{-- Rows added dynamically --}}
                        </tbody>
                    </table>
                </div>

                <div class="table-actions">
                    <button type="button" class="btn-add-row" onclick="addRow()">
                        <i class="fa-solid fa-plus"></i> Add Another Row
                    </button>
                    <div style="flex:1;"></div>
                    <div style="font-size:13px; color:var(--text-muted); font-weight:600;">
                        <span id="row-count">0</span> recipient(s) added
                    </div>
                    <button type="submit" class="btn-save" id="saveBtn" disabled>
                        <i class="fa-solid fa-floppy-disk"></i> Save Distribution
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══════════ DISTRIBUTION HISTORY ═══════════ --}}
        <div>
            <div class="section-header-row" style="margin-bottom:16px;">
                <div class="section-title"><i class="fa-solid fa-clock-rotate-left" style="color:#f59e0b;"></i> Distribution History</div>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.triposha.index') }}" class="filter-bar" style="margin-bottom:20px;">
                <i class="fa-solid fa-magnifying-glass" style="color:#94a3b8;"></i>
                <input type="text" name="search" placeholder="Search by name..." value="{{ $search }}" style="flex:1;">
                <input type="date" name="date_from" value="{{ $dateFrom }}" placeholder="From">
                <input type="date" name="date_to" value="{{ $dateTo }}" placeholder="To">
                <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
                @if($search || $dateFrom || $dateTo)
                <a href="{{ route('admin.triposha.index') }}" style="font-size:13px; color:#64748b; font-weight:600; text-decoration:none;">Clear</a>
                @endif
            </form>

            @if($sessions->isEmpty())
            <div class="session-block">
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <p>No distribution records found.</p>
                    <p style="font-size:13px; margin-top:6px;">Use the form above to record a new Thriposha distribution session.</p>
                </div>
            </div>
            @else
            <div style="display:flex; flex-direction:column; gap:16px;">
                @foreach($sessions as $i => $session)
                <div class="session-block">
                    {{-- Session Header --}}
                    <div class="session-head" onclick="toggleSession({{ $i }})">
                        <div style="display:flex; align-items:center; gap:14px;">
                            <i class="fa-solid fa-calendar-day" style="color:#065f46; font-size:20px;"></i>
                            <div>
                                <div class="session-date">{{ $session['date']->format('l, d F Y') }}</div>
                                <div style="font-size:13px; color:#059669; margin-top:2px;">{{ $session['label'] }}</div>
                            </div>
                        </div>
                        <div class="session-meta">
                            <div class="meta-pill">
                                <div class="dot" style="background:#7c3aed;"></div>
                                <span>{{ $session['mother_count'] }} Mothers</span>
                            </div>
                            <div class="meta-pill">
                                <div class="dot" style="background:#1d4ed8;"></div>
                                <span>{{ $session['child_count'] }} Children</span>
                            </div>
                            <div class="meta-pill" style="background:#fef3c7; padding:4px 12px; border-radius:8px; color:#d97706;">
                                <i class="fa-solid fa-box-open"></i>
                                <span style="font-weight:800;">{{ $session['total_distributed'] }} pkts</span>
                            </div>
                            <i class="fa-solid fa-chevron-down" id="chevron-{{ $i }}" style="color:#94a3b8; transition:transform 0.3s;"></i>
                        </div>
                    </div>

                    {{-- Session Body --}}
                    <div class="session-body" id="session-body-{{ $i }}">
                        {{-- Stock Summary --}}
                        @if($session['opening_stock'] !== null)
                        <div class="stock-summary-bar">
                            <div class="ssb-item">
                                <div class="ssb-val" style="color:#0369a1;">{{ $session['opening_stock'] }}</div>
                                <div class="ssb-lbl">Opening Stock</div>
                            </div>
                            <div class="ssb-item">
                                <div class="ssb-val" style="color:#d97706;">{{ $session['total_distributed'] }}</div>
                                <div class="ssb-lbl">Distributed</div>
                            </div>
                            <div class="ssb-item">
                                @php $rem = $session['remaining_stock']; @endphp
                                <div class="ssb-val" style="color:{{ $rem !== null && $rem < 20 ? '#dc2626' : '#16a34a' }};">
                                    {{ $rem ?? '—' }}
                                </div>
                                <div class="ssb-lbl">Remaining</div>
                            </div>
                        </div>
                        @endif

                        {{-- Records Table --}}
                        <table class="hist-table" style="margin-top:14px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Recipient Name</th>
                                    <th>Mother Linked</th>
                                    <th>Packets</th>
                                    <th>Area</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($session['records'] as $j => $record)
                                <tr>
                                    <td style="font-weight:700; color:#94a3b8; font-size:12px;">{{ $j + 1 }}</td>
                                    <td>
                                        <span class="type-badge {{ $record->recipient_type === 'Mother' ? 'type-mother' : 'type-child' }}">
                                            <i class="fa-solid {{ $record->recipient_type === 'Mother' ? 'fa-person-dress' : 'fa-baby' }}"></i>
                                            {{ $record->recipient_type }}
                                        </span>
                                    </td>
                                    <td style="font-weight:600;">
                                        {{ $record->recipient_type === 'Child' ? ($record->name_of_child ?? $record->child?->display_name ?? '—') : ($record->name_of_mother ?? $record->mother?->mother_name ?? '—') }}
                                        @if($record->recipient_type === 'Child' && $record->child)
                                        <div style="font-size:11px; color:#94a3b8;">Child ID: #{{ $record->child_id }}</div>
                                        @elseif($record->mother)
                                        <div style="font-size:11px; color:#94a3b8;">Mother ID: #{{ $record->mother_id }}</div>
                                        @endif
                                    </td>
                                    <td style="font-size:13px; color:#64748b;">
                                        {{ $record->name_of_mother ?? $record->mother?->mother_name ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="pkt-badge">{{ $record->no_of_packets }} pkts</span>
                                    </td>
                                    <td style="font-size:13px; color:#64748b;">
                                        {{ $record->midwife?->area?->area_name ?? '—' }}
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.triposha.destroy', $record->serial_no) }}"
                                              onsubmit="return confirm('Delete this record?')">
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

{{-- ═══════════════════════════ SCRIPTS ═══════════════════════════ --}}
<script>
// Pass PHP data to JS
const mothers  = @json($mothersList);
const children = @json($childrenList);

let rowCount = 0;

function buildMothersOptions(selectedId = '') {
    let opts = '<option value="">— Select Mother —</option>';
    mothers.forEach(m => {
        opts += `<option value="${m.id}" ${selectedId == m.id ? 'selected' : ''}>#${m.id} — ${m.name} (${m.area})</option>`;
    });
    return opts;
}

function buildChildrenOptions(selectedId = '') {
    let opts = '<option value="">— Select Child —</option>';
    children.forEach(c => {
        opts += `<option value="${c.id}" ${selectedId == c.id ? 'selected' : ''}>#${c.id} — ${c.name}${c.mother ? ' (Mother: ' + c.mother + ')' : ''}</option>`;
    });
    return opts;
}

function addRow(type = 'Mother', entityId = '', packets = 1) {
    rowCount++;
    const idx = rowCount;
    const tbody = document.getElementById('batchRows');
    const tr = document.createElement('tr');
    tr.id = `row-${idx}`;
    tr.innerHTML = `
        <td class="td-num">${tbody.rows.length + 1}</td>
        <td>
            <select class="td-select" name="rows[${idx}][recipient_type]" onchange="handleTypeChange(${idx})" required>
                <option value="Mother" ${type === 'Mother' ? 'selected' : ''}>Mother</option>
                <option value="Child"  ${type === 'Child'  ? 'selected' : ''}>Child</option>
            </select>
        </td>
        <td id="name-cell-${idx}">
            <select class="td-select" name="rows[${idx}][mother_id]" id="entity-select-${idx}" required>
                ${buildMothersOptions(entityId)}
            </select>
        </td>
        <td>
            <input type="number" class="td-input" name="rows[${idx}][no_of_packets]"
                   value="${packets}" min="1" max="500" required
                   oninput="updateStockTracker()" style="text-align:center; font-weight:700;">
        </td>
        <td>
            <button type="button" class="btn-del-row" onclick="removeRow(${idx})">
                <i class="fa-solid fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    renumberRows();
    updateStockTracker();
    updateSaveBtn();
}

function handleTypeChange(idx) {
    const sel  = document.querySelector(`#row-${idx} select[name="rows[${idx}][recipient_type]"]`);
    const cell = document.getElementById(`name-cell-${idx}`);
    if (sel.value === 'Mother') {
        cell.innerHTML = `<select class="td-select" name="rows[${idx}][mother_id]" id="entity-select-${idx}" required>${buildMothersOptions()}</select>`;
    } else {
        cell.innerHTML = `<select class="td-select" name="rows[${idx}][child_id]" id="entity-select-${idx}" required>${buildChildrenOptions()}</select>`;
    }
}

function removeRow(idx) {
    const row = document.getElementById(`row-${idx}`);
    if (row) row.remove();
    renumberRows();
    updateStockTracker();
    updateSaveBtn();
}

function renumberRows() {
    const rows = document.querySelectorAll('#batchRows tr');
    rows.forEach((r, i) => {
        const numCell = r.querySelector('.td-num');
        if (numCell) numCell.textContent = i + 1;
    });
    document.getElementById('row-count').textContent = rows.length;
}

function updateStockTracker() {
    const opening = parseInt(document.getElementById('opening_stock').value) || null;

    // Sum all packet inputs
    let total = 0;
    document.querySelectorAll('#batchRows input[type="number"]').forEach(inp => {
        total += parseInt(inp.value) || 0;
    });

    document.getElementById('tracker-opening').textContent    = opening !== null ? opening : '—';
    document.getElementById('tracker-distributed').textContent = total;

    const remBox = document.getElementById('remaining-box');
    if (opening !== null) {
        const rem = opening - total;
        document.getElementById('tracker-remaining').textContent = rem;
        if (rem < 0) {
            remBox.className = 'stock-box low';
        } else if (rem < 20) {
            remBox.className = 'stock-box low';
        } else {
            remBox.className = 'stock-box remaining';
        }
    } else {
        document.getElementById('tracker-remaining').textContent = '—';
        remBox.className = 'stock-box remaining';
    }
}

function updateSaveBtn() {
    const rowCount = document.querySelectorAll('#batchRows tr').length;
    document.getElementById('saveBtn').disabled = rowCount === 0;
}

function toggleSession(idx) {
    const body    = document.getElementById(`session-body-${idx}`);
    const chevron = document.getElementById(`chevron-${idx}`);
    if (body.style.display === 'none') {
        body.style.display = '';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        body.style.display = 'none';
        chevron.style.transform = 'rotate(-90deg)';
    }
}

// Add first row on load
document.addEventListener('DOMContentLoaded', function() {
    addRow();
    updateStockTracker();
});
</script>

</body>
</html>

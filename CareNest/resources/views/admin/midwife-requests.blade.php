<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Midwife Requests - CareNest</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            overflow-y: scroll;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
        }

        /* ---- Sidebar ---- */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226,232,240,0.8);
            display: flex;
            flex-direction: column;
            padding: 30px 20px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 800;
            color: #1b4d3e;
            text-decoration: none;
            margin-bottom: 36px;
            padding-left: 10px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            overflow-y: auto;
            padding-right: 4px;
        }

        .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .nav-item a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .nav-item a:hover {
            background: #f1f5f9;
            color: var(--text-dark);
        }

        .nav-item.active a {
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(27,94,32,0.25);
        }

        .nav-item i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background: transparent;
            border: none;
            padding: 10px 18px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: color 0.2s ease;
            width: 100%;
        }

        .logout-btn:hover { color: #ef4444; }

        /* ---- Main Wrapper ---- */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .header-left {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: #1b4d3e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-badge-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-role-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 20px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 16px;
        }

        /* Content Container */
        .content-container {
            padding: 36px 48px;
            max-width: 1250px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
            position: relative;
        }

        .page-title {
            font-family: var(--font-heading);
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .page-subtitle {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Table Card */
        .table-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 36px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.04);
            margin-bottom: 60px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            text-align: left;
            padding: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        td {
            padding: 20px 16px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
        }

        .name-cell {
            font-weight: 700;
            color: #0f172a;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .status-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .status-approved { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .status-rejected { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .action-form {
            display: inline-block;
            margin-right: 8px;
        }

        .btn-action {
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }
        .btn-approve:hover { background: #059669; transform: translateY(-1px); }

        .btn-reject {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }
        .btn-reject:hover { background: #ffe4e6; }

        @media (max-width: 1024px) {
            .sidebar { width: 80px; padding: 20px 10px; }
            .sidebar-brand span, .nav-item span, .logout-btn span { display: none; }
            .main-wrapper { margin-left: 80px; }
            .content-container { padding: 30px 20px; }
            .top-header { padding: 16px 20px; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-leaf" style="color: #00c853;"></i>
            <span>CareNest</span>
        </a>

        <ul class="sidebar-menu">
            <li class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if(Auth::check() && (Auth::user()->role === 'provider' || Auth::user()->role === 'admin'))
            <li class="nav-item active">
                <a href="{{ route('admin.midwife-requests') }}">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Midwife Requests</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.midwives.*') ? 'active' : '' }}">
                <a href="{{ route('admin.midwives.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Manage Midwives</span>
                </a>
            </li>
            @endif
            <li class="nav-item {{ request()->routeIs('mothers.*') ? 'active' : '' }}">
                <a href="{{ route('admin.mothers.index') }}">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Mothers</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('children.*') ? 'active' : '' }}">
                <a href="{{ route('admin.children.index') }}">
                    <i class="fa-solid fa-baby"></i>
                    <span>Children</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('immunizations.*') ? 'active' : '' }}">
                <a href="{{ route('admin.immunizations.index') }}">
                    <i class="fa-solid fa-syringe"></i>
                    <span>Immunizations</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('alerts.*') ? 'active' : '' }}">
                <a href="{{ route('admin.alerts.index') }}">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>High-Risk Alerts</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('triposha.*') ? 'active' : '' }}">
                <a href="{{ route('admin.triposha.index') }}">
                    <i class="fa-solid fa-book-medical"></i>
                    <span>Triposha Book</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
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
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <span>Midwife Requests</span>
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-container">
            <h1 class="page-title">Midwife Registrations</h1>
            <p class="page-subtitle">Review and approve new midwife registration requests for the clinic.</p>

            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Midwife Name</th>
                            <th>Email Address</th>
                            <th>Assigned Area</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr>
                                <td class="name-cell">{{ $request->name }}</td>
                                <td>{{ $request->email }}</td>
                                <td>
                                    <i class="fa-solid fa-location-dot" style="color: #00c853; margin-right: 6px;"></i>
                                    {{ $request->midwife->area->area_name ?? 'N/A' }}
                                </td>
                                <td>{{ $request->created_at->format('M d, Y') }}<br><span style="color:#94a3b8; font-size: 12px;">{{ $request->created_at->format('h:i A') }}</span></td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($request->status) }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($request->status === 'pending')
                                        <form action="{{ route('admin.midwife-requests.approve', $request->id) }}" method="POST" class="action-form">
                                            @csrf
                                            <button type="submit" class="btn-action btn-approve">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.midwife-requests.reject', $request->id) }}" method="POST" class="action-form">
                                            @csrf
                                            <button type="submit" class="btn-action btn-reject" onclick="return confirm('Are you sure you want to reject this request?');">
                                                <i class="fa-solid fa-xmark"></i> Reject
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #94a3b8; font-size: 13px; font-weight: 600;">
                                            <i class="fa-solid fa-clock-rotate-left"></i> Processed
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                                    <i class="fa-regular fa-folder-open" style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                                    No midwife registration requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>

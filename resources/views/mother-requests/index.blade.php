<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mother Requests - CareNest</title>
    
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
        }

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
            color: #1e293b;
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
        }

        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6);
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
            margin-left: auto;
        }

        .content {
            padding: 40px 48px;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .hero-banner {
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            border-radius: 24px;
            padding: 32px 40px;
            color: white;
            box-shadow: 0 12px 30px rgba(27,94,32,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hero-title {
            font-family: var(--font-heading);
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .hero-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.85);
        }

        .hero-stats {
            display: flex;
            gap: 20px;
        }

        .hero-stat-card {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 16px;
            padding: 14px 20px;
            text-align: center;
            min-width: 100px;
        }

        .hero-stat-num {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: white;
        }

        .hero-stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: rgba(255,255,255,0.8);
            margin-top: 2px;
        }

        .alert-success {
            padding: 16px 20px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 16px;
            color: #065f46;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            padding: 16px 20px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 16px;
            color: #991b1b;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-card {
            background: #ffffff;
            border: 1px solid rgba(226,232,240,0.8);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: #f8fafc;
            padding: 16px 20px;
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 18px 20px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafbff;
        }

        .mother-id-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 10px;
            background: #e8f5e9;
            color: #1b5e20;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 13px;
            border: 1px solid #c8e6c9;
        }

        .name-cell {
            font-weight: 700;
            color: #0f172a;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .status-approved {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .status-rejected {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .action-btns {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 10px;
            font-family: var(--font-main);
            font-size: 13px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-approve {
            background: #00c853;
            color: #ffffff;
        }

        .btn-approve:hover {
            background: #00b34a;
            box-shadow: 0 4px 12px rgba(0,200,83,0.3);
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-reject:hover {
            background: #dc2626;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(220,38,38,0.25);
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .hero-banner {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

@include('partials.sidebar')

<div class="main-wrapper">
    <header class="top-header">
        <div class="header-left">
            <i class="fa-solid fa-user-clock" style="color: #00c853;"></i>
            <span>Mother Registration Requests</span>
        </div>
        <div class="header-right">
            @include('partials.top-header-user')
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @php
            $totalCount = $requests->count();
            $pendingCount = $requests->where('status', 'pending')->count();
            $approvedCount = $requests->where('status', 'approved')->count();
        @endphp

        <div class="hero-banner">
            <div>
                <h1 class="hero-title">Mother Account Verification</h1>
                <p class="hero-subtitle">
                    @if($role === 'midwife')
                        Review and approve portal login requests for registered mothers in your clinic area.
                    @else
                        Review and manage mother registration requests across all clinic areas.
                    @endif
                </p>
            </div>
            <div class="hero-stats">
                <div class="hero-stat-card">
                    <div class="hero-stat-num">{{ $pendingCount }}</div>
                    <div class="hero-stat-label">Pending</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-num">{{ $approvedCount }}</div>
                    <div class="hero-stat-label">Approved</div>
                </div>
                <div class="hero-stat-card">
                    <div class="hero-stat-num">{{ $totalCount }}</div>
                    <div class="hero-stat-label">Total</div>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Mother ID</th>
                            <th>Mother Name</th>
                            <th>Email Address</th>
                            <th>Contact</th>
                            <th>Clinic Area / Midwife</th>
                            <th>Requested On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td>
                                    @if($req->mother)
                                        <span class="mother-id-tag">
                                            <i class="fa-solid fa-id-badge"></i>
                                            #{{ $req->mother->mother_id }}
                                        </span>
                                    @else
                                        <span style="color:#94a3b8; font-weight:600;">Unlinked</span>
                                    @endif
                                </td>
                                <td class="name-cell">
                                    {{ $req->mother->mother_name ?? $req->name }}
                                </td>
                                <td>{{ $req->email }}</td>
                                <td>
                                    {{ $req->mother->phone_no ?? '—' }}
                                </td>
                                <td>
                                    @if($req->mother && $req->mother->midwife)
                                        <span style="font-weight: 700; color:#1b4d3e;">
                                            <i class="fa-solid fa-location-dot" style="color:#00c853; margin-right:4px;"></i>
                                            {{ $req->mother->midwife->area->area_name ?? 'Area' }}
                                        </span>
                                        <div style="font-size:12px; color:#64748b; margin-top:2px;">
                                            {{ $req->mother->midwife->midwife_name }}
                                        </div>
                                    @else
                                        <span style="color:#94a3b8;">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $req->created_at->format('M d, Y') }}
                                    <div style="color:#94a3b8; font-size:12px;">{{ $req->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($req->status) }}">
                                        @if($req->status === 'pending')
                                            <i class="fa-solid fa-clock"></i>
                                        @elseif($req->status === 'approved')
                                            <i class="fa-solid fa-circle-check"></i>
                                        @else
                                            <i class="fa-solid fa-circle-xmark"></i>
                                        @endif
                                        {{ $req->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($req->status === 'pending')
                                        <div class="action-btns">
                                            <form action="{{ route($prefix . 'mother-requests.approve', $req->id) }}" method="POST" style="margin:0;">
                                                @csrf
                                                <button type="submit" class="btn-action btn-approve" title="Approve Mother Request">
                                                    <i class="fa-solid fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route($prefix . 'mother-requests.reject', $req->id) }}" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to reject this registration request?');">
                                                @csrf
                                                <button type="submit" class="btn-action btn-reject" title="Reject Request">
                                                    <i class="fa-solid fa-xmark"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($req->status === 'approved')
                                        <span style="color:#047857; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:5px;">
                                            <i class="fa-solid fa-check-double"></i> Approved
                                        </span>
                                    @else
                                        <span style="color:#b91c1c; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:5px;">
                                            <i class="fa-solid fa-ban"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 48px; color: #64748b;">
                                    <i class="fa-solid fa-clipboard-check" style="font-size: 36px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                                    No mother registration requests found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>

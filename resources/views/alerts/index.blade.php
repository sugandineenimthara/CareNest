<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High-Risk Alerts - CareNest</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-heading: 'Outfit', sans-serif;
            --sidebar-width: 260px;
            --text-dark: #1e293b;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 48px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
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

        
        .content-container {
            padding: 40px 48px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
        }

        .page-title {
            font-family: var(--font-heading);
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 30px;
        }

        
        .table-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.04);
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
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: #0f172a;
        }

        .area-icon-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        
        .bg-danger { background: #fee2e2; color: #ef4444; }
        .bg-warning { background: #fef3c7; color: #d97706; }
        
        .pill-danger { color: #ef4444; background: #fef2f2; border: 1px solid #fecaca; }
        .pill-warning { color: #d97706; background: #fffbeb; border: 1px solid #fde68a; }

        .condition-pill {
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .action-arrow-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 14px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .action-arrow-btn:hover {
            color: #00c853;
        }

    </style>
</head>
<body>

    
    @include('partials.sidebar')

    @php
        $role = auth()->user()->role ?? 'admin';
    @endphp
    <div class="main-wrapper">
    <header class="top-header">
        <div class="header-left">
            <span>{{ $role === 'mother' ? 'My Notifications' : 'High-Risk Alerts' }}</span>
        </div>
        <div class="header-right">
            @include('partials.top-header-user')
        </div>
    </header>

    <main class="content-container">
        @if($role === 'mother')
            <h1 class="page-title">My Notifications</h1>
            <p class="page-subtitle">Advance notifications and clinic schedule reminders for you and your children.</p>

            @forelse($motherNotifications as $notification)
                <div style="background:white; border-radius:20px; padding:24px 28px; border:1px solid {{ $notification->is_within_week ? '#fde68a' : '#e2e8f0' }}; margin-bottom:16px; box-shadow:0 4px 16px rgba(0,0,0,0.03); display:flex; align-items:flex-start; gap:20px; {{ $notification->is_within_week ? 'background:linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);' : '' }}">
                    <div style="width:48px; height:48px; border-radius:14px; background:{{ $notification->is_within_week ? '#d97706' : ($notification->severity === 'danger' ? '#ef4444' : '#00c853') }}; color:white; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                        <i class="fa-solid {{ $notification->icon }}"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
                            <div>
                                <h3 style="font-family:var(--font-heading); font-size:18px; font-weight:800; color:#1e293b; margin-bottom:4px;">
                                    {{ $notification->title }}
                                </h3>
                                <div style="font-size:13px; color:#64748b;">
                                    For: <strong style="color:#0f172a;">{{ $notification->beneficiary }}</strong>
                                </div>
                            </div>
                            <div>
                                @if($notification->is_within_week)
                                    <span class="condition-pill" style="background:#fef3c7; color:#92400e; border:1px solid #fde68a; font-weight:800;">
                                        <i class="fa-solid fa-bell"></i> Upcoming within 1 week
                                    </span>
                                @else
                                    <span class="condition-pill pill-{{ $notification->severity }}">
                                        {{ $notification->status_label }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div style="margin-top:14px; background:#f8fafc; border-radius:12px; padding:12px 18px; display:flex; flex-wrap:wrap; gap:20px; font-size:13px; color:#334155; border:1px solid #f1f5f9;">
                            <div>
                                <span style="color:#94a3b8; font-weight:700; text-transform:uppercase; font-size:11px; display:block;">Date</span>
                                <strong style="color:#1b4d3e; font-size:14px;">{{ $notification->date }}</strong>
                            </div>
                            <div>
                                <span style="color:#94a3b8; font-weight:700; text-transform:uppercase; font-size:11px; display:block;">Time</span>
                                <strong style="color:#0f172a; font-size:14px;">{{ $notification->time }}</strong>
                            </div>
                            <div>
                                <span style="color:#94a3b8; font-weight:700; text-transform:uppercase; font-size:11px; display:block;">Venue</span>
                                <strong style="color:#0284c7; font-size:14px;"><i class="fa-solid fa-hospital" style="margin-right:4px;"></i> {{ $notification->venue }}</strong>
                            </div>
                        </div>

                        <p style="margin-top:12px; font-size:14px; color:#475569; line-height:1.5;">
                            {{ $notification->message }}
                        </p>
                    </div>
                </div>
            @empty
                <div style="background:white; border-radius:24px; padding:48px 24px; text-align:center; border:1px solid #e2e8f0; box-shadow:0 4px 16px rgba(0,0,0,0.03);">
                    <i class="fa-solid fa-bell-slash" style="font-size:44px; color:#cbd5e1; display:block; margin-bottom:14px;"></i>
                    <h3 style="font-family:var(--font-heading); font-size:18px; font-weight:800; color:#1e293b; margin-bottom:6px;">No Notifications</h3>
                    <p style="color:#64748b; font-size:14px; max-width:480px; margin:0 auto;">
                        You have no upcoming immunization alerts at this time. When a clinic date approaches within 1 week, you will receive notifications here.
                    </p>
                </div>
            @endforelse
        @else
            <h1 class="page-title">High-Risk Medical Alerts</h1>
            <p class="page-subtitle">A comprehensive list of mothers requiring immediate medical attention based on clinic tests and health history.</p>

            <div class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th>MOTHER'S NAME</th>
                            <th>ALERT TYPE</th>
                            <th>CONDITION / VALUE</th>
                            <th>CLINIC AREA</th>
                            <th>MIDWIFE IN-CHARGE</th>
                            <th>CONTACT NO</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allAlerts as $alert)
                            <tr>
                                <td>
                                    <div class="name-cell">
                                        <div class="area-icon-dot bg-{{ $alert->severity }}">
                                            <i class="fa-solid {{ $alert->icon }}"></i>
                                        </div>
                                        <span>{{ $alert->mother_name }}</span>
                                    </div>
                                </td>
                                <td style="font-weight: 600;">{{ $alert->type }}</td>
                                <td>
                                    <span class="condition-pill pill-{{ $alert->severity }}">
                                        {{ $alert->condition_text }}
                                    </span>
                                </td>
                                <td>{{ $alert->area }}</td>
                                <td>{{ $alert->midwife }}</td>
                                <td>{{ $alert->contact_no }}</td>
                                <td>
                                    <button class="action-arrow-btn" title="View Details">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 40px;">
                                    <i class="fa-solid fa-shield-heart" style="font-size: 32px; color: #e2e8f0; display: block; margin-bottom: 12px;"></i>
                                    No high-risk medical alerts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </main>
    </div>

</body>
</html>

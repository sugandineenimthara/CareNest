<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CareNest</title>

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

        body {
            font-family: var(--font-main);
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
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

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

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
            box-shadow: 0 8px 20px rgba(27, 94, 32, 0.25);
        }

        .nav-item i {
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        /* Sidebar Footer Buttons */
        .sidebar-footer {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-schedule {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            border: none;
            border-radius: 18px;
            color: #ffffff;
            font-family: var(--font-heading);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(0, 200, 83, 0.3);
            transition: all 0.25s ease;
        }

        .btn-schedule:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(0, 200, 83, 0.4);
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

        .logout-btn:hover {
            color: #ef4444;
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Top Header Bar */
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
            gap: 8px;
        }

        .search-box {
            position: relative;
            width: 320px;
        }

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            font-family: var(--font-main);
            font-size: 13px;
            color: var(--text-dark);
        }

        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .icon-btn {
            background: #f1f5f9;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .icon-btn:hover {
            background: #e2e8f0;
            color: var(--text-dark);
        }

        .user-badge-container {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 12px;
            border-left: 1px solid #e2e8f0;
        }

        .user-role-label {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
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

        /* Body Container */
        .content-container {
            padding: 36px 48px;
            max-width: 1250px;
            width: 100%;
            margin: 0 auto;
            flex: 1;
            position: relative;
        }

        .welcome-title {
            font-family: var(--font-heading);
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .welcome-sub {
            font-size: 15px;
            color: var(--text-muted);
            margin-bottom: 32px;
        }

        /* 4 Stat Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 36px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 28px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
        }

        .stat-card.card-green { border-left: 6px solid #00c853; }
        .stat-card.card-blue { border-left: 6px solid #5c6bc0; }
        .stat-card.card-red { border-left: 6px solid #e53935; }
        .stat-card.card-brown { border-left: 6px solid #8d6e63; }

        .stat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #475569;
            max-width: 120px;
            line-height: 1.3;
        }

        .stat-card-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .icon-green { background: #e8f5e9; color: #2e7d32; }
        .icon-blue { background: #e8eaf6; color: #3f51b5; }
        .icon-red { background: #ffebee; color: #c62828; }
        .icon-brown { background: #efebe9; color: #4e342e; }

        .stat-value {
            font-family: var(--font-heading);
            font-size: 36px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 12px;
        }

        /* Clinic Areas Table Card */
        .table-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 36px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.04);
            margin-bottom: 60px;
        }

        .table-header {
            margin-bottom: 24px;
        }

        .table-title {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .table-subtitle {
            font-size: 13px;
            color: var(--text-muted);
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table th {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            text-align: left;
            padding: 14px 16px;
            border-bottom: 1.5px solid #f1f5f9;
        }

        .custom-table td {
            padding: 18px 16px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #f8fafc;
            vertical-align: middle;
        }

        .area-name-cell {
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
            background: #e8f5e9;
            color: #00c853;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .progress-bar-bg {
            width: 100px;
            height: 6px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #94a3b8;
            border-radius: 4px;
        }

        .alert-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e2e8f0;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
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

        /* Bottom Center Badge & Floating FAB */
        .bottom-badge-center {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .badge-pill-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            padding: 8px 24px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #1b4d3e;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.9);
        }

        .fab-btn {
            position: fixed;
            bottom: 30px;
            right: 40px;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            background: #00e676;
            border: none;
            color: #ffffff;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(0, 230, 118, 0.4);
            transition: all 0.3s ease;
            z-index: 99;
        }

        .fab-btn:hover {
            transform: scale(1.1);
        }

        /* Simple Visit Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 28px;
            padding: 36px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.15);
        }

        .modal-title {
            font-family: var(--font-heading);
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 20px;
        }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .sidebar { width: 80px; padding: 20px 10px; }
            .sidebar-brand span, .nav-item span, .btn-schedule span { display: none; }
            .main-wrapper { margin-left: 80px; }
        }
    </style>
</head>
<body>

    <!-- Left Taskbar Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <i class="fa-solid fa-leaf" style="color: #00c853;"></i>
            <span>CareNest</span>
        </a>

        <!-- Taskbar Sections mapped to Database entities -->
        <ul class="sidebar-menu">
            <li class="nav-item active">
                <a href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#mothers">
                    <i class="fa-solid fa-user-nurse"></i>
                    <span>Mothers</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#children">
                    <i class="fa-solid fa-baby"></i>
                    <span>Children</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#immunizations">
                    <i class="fa-solid fa-syringe"></i>
                    <span>Immunizations</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#alerts">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>High-Risk Alerts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#areas">
                    <i class="fa-solid fa-hospital"></i>
                    <span>Clinic Areas</span>
                </a>
            </li>

            <!-- Additional Sections mapped to 13 database tables -->
            <li class="nav-item">
                <a href="#pregnancy-history">
                    <i class="fa-solid fa-notes-medical"></i>
                    <span>Pregnancy History</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#lab-tests">
                    <i class="fa-solid fa-vial-circle-check"></i>
                    <span>Lab Tests</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#triposha">
                    <i class="fa-solid fa-book-medical"></i>
                    <span>Triposha Book</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#attendances">
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

        <!-- Sidebar Bottom Footer -->
        <div class="sidebar-footer">
            <button type="button" class="btn-schedule" onclick="openVisitModal()">
                <i class="fa-regular fa-calendar-plus"></i>
                <span>Schedule Visit</span>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <!-- Top Navigation Header -->
        <header class="top-header">
            <div class="header-left">
                CareNest
            </div>

            <!-- Search Bar -->
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" class="search-input" placeholder="Search records...">
            </div>

            <!-- Right Profile Badge & Settings -->
            <div class="header-right">
                <button class="icon-btn" title="Settings">
                    <i class="fa-solid fa-gear"></i>
                </button>

                <div class="user-badge-container">
                    <span class="user-role-label">{{ ucfirst($user->role ?? 'Admin') }}</span>
                    <div class="user-avatar">
                        <i class="fa-regular fa-user"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Dashboard Content -->
        <main class="content-container">
            <h1 class="welcome-title">Welcome Back!</h1>
            <p class="welcome-sub">Overview of your clinic's maternal and pediatric care status today.</p>

            <!-- 4 Top Stat Cards -->
            <div class="stats-grid">
                <!-- Card 1: Registered Mothers -->
                <div class="stat-card card-green">
                    <div class="stat-card-header">
                        <span class="stat-card-title">REGISTERED MOTHERS</span>
                        <div class="stat-card-icon icon-green">
                            <i class="fa-solid fa-user-nurse"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $registeredMothersCount > 0 ? $registeredMothersCount : '--' }}</div>
                </div>

                <!-- Card 2: Children Enrolled -->
                <div class="stat-card card-blue">
                    <div class="stat-card-header">
                        <span class="stat-card-title">CHILDREN ENROLLED</span>
                        <div class="stat-card-icon icon-blue">
                            <i class="fa-regular fa-face-smile"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $childrenEnrolledCount > 0 ? $childrenEnrolledCount : '--' }}</div>
                </div>

                <!-- Card 3: High-Risk Cases -->
                <div class="stat-card card-red">
                    <div class="stat-card-header">
                        <span class="stat-card-title">HIGH-RISK CASES</span>
                        <div class="stat-card-icon icon-red">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $highRiskCount > 0 ? $highRiskCount : '--' }}</div>
                </div>

                <!-- Card 4: Immunizations Due -->
                <div class="stat-card card-brown">
                    <div class="stat-card-header">
                        <span class="stat-card-title">IMMUNIZATIONS DUE</span>
                        <div class="stat-card-icon icon-brown">
                            <i class="fa-solid fa-syringe"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $immunizationsDueCount > 0 ? $immunizationsDueCount : '--' }}</div>
                </div>
            </div>

            <!-- Clinic Areas Overview Table Card -->
            <div class="table-card">
                <div class="table-header">
                    <h2 class="table-title">Clinic Areas Overview</h2>
                    <p class="table-subtitle">Performance and occupancy by department</p>
                </div>

                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>CLINIC AREA</th>
                            <th>PATIENT COUNT</th>
                            <th>CAPACITY</th>
                            <th>ACTIVE ALERTS</th>
                            <th>STAFF ON DUTY</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clinicAreas as $area)
                            <tr>
                                <td>
                                    <div class="area-name-cell">
                                        <div class="area-icon-dot">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <span>{{ $area->area_name }}</span>
                                    </div>
                                </td>
                                <td>{{ $area->patient_count ?? '-' }}</td>
                                <td>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill"></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="alert-pill">{{ $area->active_alerts ?? '-' }}</span>
                                </td>
                                <td>{{ $area->staff_on_duty ?? '-' }}</td>
                                <td>
                                    <button class="action-arrow-btn">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #94a3b8;">No clinic areas available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Bottom Pill Badge -->
            <div class="bottom-badge-center">
                <div class="badge-pill-light">
                    CARING FOR MOTHERS & CHILDREN
                </div>
            </div>
        </main>
    </div>

    <!-- Bottom Right Floating Action Button -->
    <button class="fab-btn" onclick="openVisitModal()" title="Quick Action">
        <i class="fa-solid fa-plus"></i>
    </button>

    <!-- Schedule Visit Modal -->
    <div class="modal-overlay" id="visitModal">
        <div class="modal-content">
            <h3 class="modal-title">Schedule Clinic Visit</h3>
            <form action="{{ route('dashboard') }}" method="GET" onsubmit="alert('Clinic Visit Scheduled Successfully!'); closeModal(); return false;">
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px;">CLINIC NAME</label>
                    <input type="text" class="search-input" style="width: 100%; padding: 12px;" placeholder="e.g. Colombo North Antenatal Clinic" required>
                </div>
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 12px; font-weight: 700; margin-bottom: 6px;">VISIT DATE</label>
                    <input type="date" class="search-input" style="width: 100%; padding: 12px;" required>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="button" class="logout-btn" onclick="closeModal()" style="width: 50%; justify-content: center; background: #f1f5f9; border-radius: 14px;">Cancel</button>
                    <button type="submit" class="btn-schedule" style="width: 50%;">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openVisitModal() {
            document.getElementById('visitModal').style.display = 'flex';
        }
        function closeModal() {
            document.getElementById('visitModal').style.display = 'none';
        }
    </script>
</body>
</html>

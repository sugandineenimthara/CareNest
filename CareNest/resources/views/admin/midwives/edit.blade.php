<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Midwife - CareNest</title>
    
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

        .back-btn {
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: #e2e8f0;
            color: #1b5e20;
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
            padding: 40px 48px;
            max-width: 800px;
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

        /* Form Card */
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.04);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 14px;
            font-family: var(--font-main);
            color: #1e293b;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: #00c853;
            box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #1b5e20, #00695c);
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 8px 20px rgba(27, 94, 32, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(27, 94, 32, 0.35);
        }
        
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
        }

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
            <li class="nav-item {{ request()->routeIs('admin.midwife-requests*') ? 'active' : '' }}">
                <a href="{{ route('admin.midwife-requests') }}">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Midwife Requests</span>
                </a>
            </li>
            <li class="nav-item active">
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
                <a href="{{ route('admin.midwives.index') }}" class="back-btn" title="Back to Midwives">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span>Edit Midwife</span>
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <!-- Main Content -->
        <main class="content-container">
            <h1 class="page-title">Edit Midwife</h1>
            <p class="page-subtitle">Update information for {{ $user->name }}</p>

            @if($errors->any())
                <div class="alert-error">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="form-card">
                <form action="{{ route('admin.midwives.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Assigned Clinic Area</label>
                        <select name="area_id" class="form-control" required>
                            <option value="">Select an Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->area_id }}" {{ (old('area_id', $user->midwife->area_id ?? '') == $area->area_id) ? 'selected' : '' }}>
                                    {{ $area->area_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>

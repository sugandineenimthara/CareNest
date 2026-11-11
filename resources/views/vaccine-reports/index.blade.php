<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccine Reports | CareNest</title>
    <meta name="description" content="Vaccine report showing stock received from storage, vaccines used in clinic from immunization records, and remaining vaccines.">

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
            --secondary: #00695c;
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
        .nav-item a:hover { background: #f1f5f9; color: var(--text-dark); }
        .nav-item.active a {
            background: linear-gradient(135deg, #1b5e20 0%, #00695c 100%);
            color: #fff;
            box-shadow: 0 8px 20px rgba(27, 94, 32, 0.25);
        }
        .nav-item i { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer { margin-top: 20px; }

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
            min-height: 76px;
            box-sizing: border-box;
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
            margin-left: auto;
        }

        .content {
            padding: 32px 48px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .alert {
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }

        .filter-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 24px 28px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .filter-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .filter-title {
            font-family: var(--font-heading);
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-record-stock {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(27, 94, 32, 0.25);
        }
        .btn-record-stock:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(27, 94, 32, 0.35);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 11px 14px;
            background: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 14px;
            color: var(--text-dark);
            outline: none;
            transition: border-color 0.2s;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 200, 83, 0.12);
        }
        .filter-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-apply {
            padding: 11px 24px;
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-apply:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(27, 94, 32, 0.3);
        }
        .btn-reset {
            padding: 11px 20px;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            border-radius: 12px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: background 0.2s;
        }
        .btn-reset:hover { background: #e2e8f0; color: var(--text-dark); }

        .filter-status-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            font-size: 13px;
            color: #166534;
            font-weight: 600;
        }

        .report-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
        }
        .report-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 28px;
            background: linear-gradient(135deg, #1b5e20, #00695c);
            color: white;
        }
        .report-card-head-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .report-card-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .report-card-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 800;
        }
        .report-card-sub {
            font-size: 13px;
            opacity: 0.85;
            margin-top: 2px;
        }

        .report-table-wrap { overflow-x: auto; }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }
        .report-table thead th {
            background: #f8fafc;
            color: #64748b;
            padding: 14px 24px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: left;
            border-bottom: 1.5px solid #e2e8f0;
        }
        .report-table thead th.center { text-align: center; }
        .report-table tbody td {
            padding: 16px 24px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            vertical-align: middle;
        }
        .report-table tbody tr:last-child td { border-bottom: none; }
        .report-table tbody tr:hover { background: #fafbff; }

        .vaccine-name {
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
        }
        .vaccine-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
            flex-shrink: 0;
        }

        .pill {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 10px;
            font-family: var(--font-heading);
            font-size: 14px;
            font-weight: 800;
            text-align: center;
            min-width: 60px;
        }
        .pill-storage { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .pill-used    { background: #f3e8ff; color: #7c3aed; border: 1px solid #e9d5ff; }
        .pill-left-ok { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }
        .pill-left-0  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal-card {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: modalFadeIn 0.25s ease;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-head {
            background: linear-gradient(135deg, #1b5e20, #00695c);
            color: white;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-close {
            background: transparent;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .modal-close:hover { opacity: 1; }
        .modal-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .modal-foot {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        @media (max-width: 768px) {
            .main-wrapper { margin-left: 0; }
            .content { padding: 20px; }
            .top-header { padding: 16px 20px; }
        }
    </style>
</head>
<body>

@php
    $role = auth()->user()->role ?? 'admin';
    $prefix = $role === 'midwife' ? 'midwife.' : ($role === 'mother' ? 'mother.' : 'admin.');
@endphp

@include('partials.sidebar')

<div class="main-wrapper">

    <header class="top-header">
        <div class="header-left">
            <i class="fa-solid fa-file-invoice" style="color:#00c853;"></i>
            Vaccine Reports
        </div>
        <div class="header-right">
            @include('partials.top-header-user')
        </div>
    </header>

    <div class="content">

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="filter-card">
            <div class="filter-top-row">
                <div class="filter-title">
                    <i class="fa-solid fa-filter" style="color:#00c853;"></i>
                    Filter Vaccine Report
                </div>
                @if(in_array($role, ['admin', 'provider', 'midwife']))
                    <button type="button" class="btn-record-stock" onclick="openRecordModal()">
                        <i class="fa-solid fa-plus-circle"></i>
                        Record Storage Stock
                    </button>
                @endif
            </div>

            <form method="GET" action="{{ route($prefix . 'vaccine-reports.index') }}">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="clinic_type">Clinic</label>
                        <select id="clinic_type" name="clinic_type" class="form-select">
                            <option value="">All Clinics</option>
                            <option value="pediatric" @selected(($clinicType ?? '') === 'pediatric')>Pediatric Clinic</option>
                            <option value="mother" @selected(($clinicType ?? '') === 'mother')>Mother Clinic</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="from_date">Date From</label>
                        <input type="date" id="from_date" name="from_date" class="form-input"
                               value="{{ $fromDate ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label for="to_date">Date To</label>
                        <input type="date" id="to_date" name="to_date" class="form-input"
                               value="{{ $toDate ?? '' }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply">
                        <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
                    </button>
                    <a href="{{ route($prefix . 'vaccine-reports.index') }}" class="btn-reset">
                        <i class="fa-solid fa-rotate-left"></i>&nbsp; Reset
                    </a>
                </div>
            </form>

            @if($clinicType || $fromDate || $toDate)
                <div style="margin-top: 18px;">
                    <div class="filter-status-row">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Active Filter:</span>
                        @if($clinicType === 'pediatric')
                            <strong>Pediatric Clinic</strong>
                        @elseif($clinicType === 'mother')
                            <strong>Mother Clinic</strong>
                        @else
                            <strong>All Clinics</strong>
                        @endif
                        @if($fromDate && $toDate)
                            <span>· Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
                        @elseif($fromDate)
                            <span>· From: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</span>
                        @elseif($toDate)
                            <span>· Up to: {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="report-card">
            <div class="report-card-head">
                <div class="report-card-head-left">
                    <div class="report-card-icon">
                        <i class="fa-solid fa-syringe"></i>
                    </div>
                    <div>
                        <div class="report-card-title">Clinic Vaccination Stock & Usage Report</div>
                        <div class="report-card-sub">
                            Vaccines got from storage · Vaccines used in clinic (from immunization records) · Vaccines left
                        </div>
                    </div>
                </div>
            </div>

            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Vaccination Type</th>
                            <th class="center">Vaccines Got from Storage</th>
                            <th class="center">No. of Vaccines Used for Clinic</th>
                            <th class="center">No. of Vaccines Left</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vaccineRows as $row)
                            @php
                                $left = $row['vaccines_left'];
                            @endphp
                            <tr>
                                <td>
                                    <div class="vaccine-name">
                                        <span class="vaccine-dot"></span>
                                        {{ $row['vaccine_type'] }}
                                    </div>
                                </td>
                                <td class="center">
                                    <span class="pill pill-storage">
                                        {{ $row['got_from_storage'] }}
                                    </span>
                                </td>
                                <td class="center">
                                    <span class="pill pill-used">
                                        {{ $row['used_in_clinic'] }}
                                    </span>
                                </td>
                                <td class="center">
                                    <span class="pill {{ $left > 0 ? 'pill-left-ok' : 'pill-left-0' }}">
                                        {{ $left }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center; padding:48px 20px; color:#64748b;">
                                    <i class="fa-solid fa-file-circle-xmark" style="font-size:36px; opacity:0.3; margin-bottom:12px; display:block;"></i>
                                    No vaccination records found for the selected clinic or date filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div id="recordStockModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <div class="modal-title">
                <i class="fa-solid fa-boxes-stacked"></i>
                Record Vaccines from Storage
            </div>
            <button type="button" class="modal-close" onclick="closeRecordModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route($prefix . 'vaccine-reports.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="modal_clinic_type">Clinic <span style="color:#ef4444;">*</span></label>
                    <select id="modal_clinic_type" name="clinic_type" class="form-select" required onchange="handleClinicTypeChange(this.value)">
                        <option value="">Select Clinic</option>
                        <option value="pediatric" @selected(($clinicType ?? '') === 'pediatric')>Pediatric Clinic</option>
                        <option value="mother" @selected(($clinicType ?? '') === 'mother')>Mother Clinic</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_date">Clinic Date <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="modal_date" name="date" class="form-input"
                           value="{{ $fromDate ?? date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="modal_vaccine_name">Vaccination Type <span style="color:#ef4444;">*</span></label>
                    <select id="modal_vaccine_name" name="vaccine_name" class="form-select" required>
                        <option value="">Select Vaccine Type</option>
                        @foreach($allVaccineNames as $vName)
                            <option value="{{ $vName }}" class="vac-opt" data-type="{{ stripos($vName, 'Tetanus') !== false ? 'mother' : 'pediatric' }}">{{ $vName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="modal_stock_received">Vaccines Got from Storage (Doses) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="modal_stock_received" name="stock_received" class="form-input"
                           min="1" placeholder="e.g. 50" required>
                </div>

                <div class="form-group">
                    <label for="modal_batch_no">Batch Number (Optional)</label>
                    <input type="text" id="modal_batch_no" name="batch_no" class="form-input"
                           placeholder="e.g. BCG-2026-B1">
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-reset" onclick="closeRecordModal()">Cancel</button>
                <button type="submit" class="btn-apply">Save Record</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRecordModal() {
        document.getElementById('recordStockModal').classList.add('open');
        const ct = document.getElementById('modal_clinic_type').value;
        if (ct) {
            handleClinicTypeChange(ct);
        }
    }
    function closeRecordModal() {
        document.getElementById('recordStockModal').classList.remove('open');
    }
    document.getElementById('recordStockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRecordModal();
        }
    });
    function handleClinicTypeChange(type) {
        const opts = document.querySelectorAll('#modal_vaccine_name .vac-opt');
        opts.forEach(opt => {
            if (!type || opt.getAttribute('data-type') === type) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        const currentSel = document.getElementById('modal_vaccine_name');
        const selectedOpt = currentSel.options[currentSel.selectedIndex];
        if (selectedOpt && selectedOpt.style.display === 'none') {
            currentSel.value = '';
        }
    }
</script>

</body>
</html>

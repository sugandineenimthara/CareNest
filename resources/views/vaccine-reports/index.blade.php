
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccine Reports | CareNest</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 26px;
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f4f7fc;
            color: #1e293b;
        }

        .container { max-width: 1400px; margin: auto; }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding: 27px;
            margin-bottom: 24px;
            color: white;
            background: linear-gradient(120deg, #1d4ed8, #6366f1);
            border-radius: 18px;
            box-shadow: 0 8px 24px #2563eb20;
        }

        h1 { margin: 0 0 8px; font-size: 30px; }
        .page-header p { margin: 0; color: #e0e7ff; }

        .back-button, .apply-button, .reset-button {
            display: inline-block;
            padding: 11px 17px;
            border: 0;
            border-radius: 9px;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }

        .back-button { background: white; color: #1d4ed8; }
        .apply-button { background: #2563eb; color: white; }
        .reset-button { background: #e2e8f0; color: #334155; }

        .filter-panel, .section {
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 15px;
            margin-bottom: 23px;
            box-shadow: 0 5px 18px #0f172a08;
        }

        .filter-panel { padding: 22px; }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
            align-items: end;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 13px;
            color: #475569;
        }

        .field input, .field select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: white;
            color: #1e293b;
            font-size: 14px;
        }

        .filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 23px;
            background: white;
            border-radius: 15px;
            border: 1px solid #e8edf5;
            box-shadow: 0 5px 18px #0f172a08;
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .stat-label { color: #64748b; font-size: 14px; font-weight: 600; }
        .stat-number { margin-top: 15px; font-size: 32px; font-weight: 800; }
        .stat-icon { font-size: 27px; }

        .blue { border-top: 4px solid #3b82f6; }
        .green { border-top: 4px solid #10b981; }
        .purple { border-top: 4px solid #a855f7; }
        .orange { border-top: 4px solid #f97316; }

        .section { overflow: hidden; }

        .section-header {
            padding: 19px 22px;
            display: flex;
            align-items: center;
            gap: 13px;
        }

        .section-header h2 { margin: 0 0 5px; font-size: 19px; }
        .section-header p { margin: 0; font-size: 13px; opacity: .8; }

        .section-icon {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: #ffffffb8;
            font-size: 23px;
        }

        .blue-header { background: #dbeafe; color: #1d4ed8; }
        .green-header { background: #d1fae5; color: #047857; }
        .purple-header { background: #f3e8ff; color: #7e22ce; }
        .orange-header { background: #ffedd5; color: #c2410c; }

        .section-body { padding: 22px; }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
            align-items: start;
        }

        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; min-width: 280px; }

        th {
            background: #f8fafc;
            color: #64748b;
            padding: 13px;
            font-size: 12px;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }

        td {
            padding: 14px 13px;
            border-bottom: 1px solid #edf1f7;
            font-size: 14px;
        }

        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #f8fafc; }

        .badge {
            display: inline-block;
            min-width: 38px;
            padding: 6px 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: 800;
        }

        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-green { background: #d1fae5; color: #047857; }
        .badge-purple { background: #f3e8ff; color: #7e22ce; }
        .badge-orange { background: #ffedd5; color: #c2410c; }

        .empty-state {
            text-align: center;
            color: #64748b;
            padding: 28px 12px;
        }

        .chart-list { display: grid; gap: 17px; }

        .chart-item {
            display: grid;
            grid-template-columns: minmax(85px, 130px) minmax(40px, 1fr) 42px;
            gap: 12px;
            align-items: center;
        }

        .chart-label {
            font-size: 13px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .bar-track {
            width: 100%;
            height: 13px;
            background: #eaf0f8;
            border-radius: 20px;
            overflow: hidden;
        }

        .bar {
            height: 100%;
            min-width: 0;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            border-radius: 20px;
        }

        .bar-value { font-size: 13px; font-weight: 800; text-align: right; }

        .progress-track {
            height: 8px;
            margin-top: 8px;
            background: #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: #10b981;
            border-radius: 20px;
        }

        .footer {
            text-align: center;
            padding: 8px 0 2px;
            color: #94a3b8;
            font-size: 13px;
        }

        @media (max-width: 850px) {
            .report-grid { grid-template-columns: 1fr; }
            .filter-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 580px) {
            body { padding: 12px; }
            .page-header { padding: 20px; }
            h1 { font-size: 25px; }
            .cards { grid-template-columns: 1fr; gap: 12px; }
            .filter-grid { grid-template-columns: 1fr; }
            .section-body { padding: 13px; }
            .section-header { padding: 16px; }
            .chart-item { grid-template-columns: 85px minmax(30px, 1fr) 30px; gap: 7px; }
        }
    </style>
</head>

<body>
<div class="container">

    <header class="page-header">
        <div>
            <h1>Vaccine Reports</h1>
            <p>CareNest · Vaccination trends and community health summaries</p>
        </div>

        <a class="back-button" href="{{ route($dashboardRoute) }}">
            ← Back to Dashboard
        </a>
    </header>

    {{-- Date and midwife filters --}}
    <section class="filter-panel">
        <form method="GET" action="{{ url()->current() }}">
            <div class="filter-grid">
                <div class="field">
                    <label for="from_date">From Date</label>
                    <input
                        type="date"
                        id="from_date"
                        name="from_date"
                        value="{{ $fromDate ?? '' }}"
                    >
                </div>

                <div class="field">
                    <label for="to_date">To Date</label>
                    <input
                        type="date"
                        id="to_date"
                        name="to_date"
                        value="{{ $toDate ?? '' }}"
                    >
                </div>

                @if(in_array($role, ['admin', 'provider'], true))
                    <div class="field">
                        <label for="midwife_id">Select Midwife</label>
                        <select id="midwife_id" name="midwife_id">
                            <option value="">All Midwives</option>
                            @foreach($midwives as $midwife)
                                <option
                                    value="{{ $midwife->midwife_id }}"
                                    @selected((string)($selectedMidwifeId ?? '') === (string)$midwife->midwife_id)
                                >
                                    {{ $midwife->midwife_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="filter-actions">
                <button class="apply-button" type="submit">Apply Filters</button>
                <a class="reset-button" href="{{ url()->current() }}">Reset</a>
            </div>
        </form>
    </section>

    @if($selectedMidwife)
        <section class="section">
            <div class="section-body">
                <strong>Selected Midwife:</strong>
                {{ $selectedMidwife->midwife_name }}
                <p style="margin-bottom:0;color:#64748b;font-size:13px">
                    The summaries below show vaccination records associated with this midwife
                    for the selected date range.
                </p>
            </div>
        </section>
    @endif

    {{-- Summary cards --}}
    <div class="cards">
        <div class="stat-card blue">
            <div class="stat-top">
                <span class="stat-label">Total Vaccinations</span>
                <span class="stat-icon">💉</span>
            </div>
            <div class="stat-number">{{ number_format($totalVaccinations) }}</div>
        </div>

        <div class="stat-card green">
            <div class="stat-top">
                <span class="stat-label">Mothers with Records</span>
                <span class="stat-icon">👩</span>
            </div>
            <div class="stat-number">{{ number_format($totalMothers) }}</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-top">
                <span class="stat-label">Children with Records</span>
                <span class="stat-icon">👶</span>
            </div>
            <div class="stat-number">{{ number_format($totalChildren) }}</div>
        </div>
    </div>

    {{-- Monthly trend chart --}}
    <section class="section">
        <div class="section-header blue-header">
            <div class="section-icon">📈</div>
            <div>
                <h2>Vaccination Trend</h2>
                <p>Number of vaccination records by month</p>
            </div>
        </div>

        <div class="section-body">
            @php
                $maxMonthly = max(1, (int) ($monthlySummary->max('total') ?? 0));
            @endphp

            @forelse($monthlySummary as $item)
                <div class="chart-item" style="margin-bottom:17px">
                    <div class="chart-label">{{ $item['label'] }}</div>
                    <div class="bar-track">
                        <div
                            class="bar"
                            style="width:{{ min(100, ($item['total'] / $maxMonthly) * 100) }}%"
                        ></div>
                    </div>
                    <div class="bar-value">{{ $item['total'] }}</div>
                </div>
            @empty
                <div class="empty-state">
                    📊 No vaccination records found for the selected period.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Mother-wise and child-wise summaries --}}
    <div class="report-grid">
        <section class="section">
            <div class="section-header green-header">
                <div class="section-icon">👩</div>
                <div>
                    <h2>Mother-wise Summary</h2>
                    <p>Vaccination records associated with each mother</p>
                </div>
            </div>

            <div class="section-body">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>Mother</th><th>Records</th></tr>
                        </thead>
                        <tbody>
                        @forelse($motherSummary as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td><span class="badge badge-green">{{ $item['total'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No mother summaries available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-header purple-header">
                <div class="section-icon">👶</div>
                <div>
                    <h2>Child-wise Summary</h2>
                    <p>Vaccination records associated with each child</p>
                </div>
            </div>

            <div class="section-body">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>Child</th><th>Records</th></tr>
                        </thead>
                        <tbody>
                        @forelse($childSummary as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td><span class="badge badge-purple">{{ $item['total'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No child summaries available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    {{-- Midwife-wise and area-wise summaries --}}
    <div class="report-grid">
        <section class="section">
            <div class="section-header green-header">
                <div class="section-icon">👩‍⚕️</div>
                <div>
                    <h2>Midwife-wise Summary</h2>
                    <p>Vaccination records by midwife</p>
                </div>
            </div>

            <div class="section-body">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>Midwife</th><th>Records</th></tr>
                        </thead>
                        <tbody>
                        @forelse($midwifeSummary as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td><span class="badge badge-green">{{ $item['total'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No midwife summaries available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="section-header orange-header">
                <div class="section-icon">📍</div>
                <div>
                    <h2>Area-wise Summary</h2>
                    <p>Vaccination records by area</p>
                </div>
            </div>

            <div class="section-body">
                <div class="table-wrap">
                    <table>
                        <thead>
                        <tr><th>Area</th><th>Records</th></tr>
                        </thead>
                        <tbody>
                        @forelse($areaSummary as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td><span class="badge badge-orange">{{ $item['total'] }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="empty-state">No area summaries available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        CareNest · Vaccine Reports
    </footer>

</div>
</body>
</html>

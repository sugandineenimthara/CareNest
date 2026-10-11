<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Child - CareNest</title>
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
            font-family: var(--font-main); background: var(--bg-gradient);
            background-attachment: fixed; color: var(--text-dark); min-height: 100vh; display: flex;
        }
        .sidebar {
            width: var(--sidebar-width); background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px); border-right: 1px solid rgba(226,232,240,0.8);
            display: flex; flex-direction: column; padding: 30px 20px;
            position: fixed; top: 0; bottom: 0; left: 0; z-index: 100;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px; font-family: var(--font-heading);
            font-size: 24px; font-weight: 800; color: #1b4d3e; text-decoration: none; margin-bottom: 36px; padding-left: 10px;
        }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 6px; flex: 1; overflow-y: auto; }
        .nav-item a {
            display: flex; align-items: center; gap: 14px; padding: 12px 18px;
            border-radius: 16px; font-size: 14px; font-weight: 600; color: #64748b; text-decoration: none;
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
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 48px; background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6); position: sticky; top: 0; z-index: 90;
        }
        .header-left { display: flex; align-items: center; gap: 14px; font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #1b4d3e; }
        .back-btn {
            width: 38px; height: 38px; border-radius: 50%; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            color: #475569; text-decoration: none;
        }
        .back-btn:hover { background: #e2e8f0; color: #1e293b; }

        .form-container { padding: 36px 48px; max-width: 900px; margin: 0 auto; width: 100%; }
        .form-card {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 24px;
            padding: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            display: flex; flex-direction: column; gap: 28px;
        }
        .section-header { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }
        .section-title { font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #1e293b; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 13px; font-weight: 700; color: #334155; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 12px 16px; background: #f8fafc; border: 1.5px solid #e2e8f0;
            border-radius: 14px; font-family: var(--font-main); font-size: 14px; color: var(--text-dark);
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            background: white; border-color: #00e676; outline: none; box-shadow: 0 0 0 4px rgba(0,230,118,0.15);
        }
        .form-actions { display: flex; justify-content: flex-end; align-items: center; gap: 14px; margin-top: 10px; }
        .btn-cancel {
            padding: 13px 26px; border-radius: 14px; border: 1.5px solid #cbd5e1;
            background: white; color: #475569; font-family: var(--font-heading); font-size: 14px; font-weight: 700; text-decoration: none;
        }
        .btn-submit {
            padding: 14px 32px; border-radius: 14px; border: none;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            color: white; font-family: var(--font-heading); font-size: 15px; font-weight: 700; cursor: pointer;
            box-shadow: 0 8px 20px rgba(0,200,83,0.3);
        }
    </style>
</head>
<body>
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
            <li class="nav-item active"><a href="{{ route('admin.children.index') }}"><i class="fa-solid fa-baby"></i><span>Children</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.immunizations.index') }}"><i class="fa-solid fa-syringe"></i><span>Immunizations</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.alerts.index') }}"><i class="fa-solid fa-triangle-exclamation"></i><span>High-Risk Alerts</span></a></li>
            <li class="nav-item"><a href="#lab-tests"><i class="fa-solid fa-vial-circle-check"></i><span>Lab Tests</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.triposha.index') }}"><i class="fa-solid fa-book-medical"></i><span>Triposha Book</span></a></li>
            <li class="nav-item"><a href="{{ route('admin.attendances.index') }}"><i class="fa-solid fa-calendar-check"></i><span>Clinic Attendances</span></a></li>
            <li class="nav-item"><a href="#reports"><i class="fa-solid fa-file-invoice"></i><span>Vaccine Reports</span></a></li>
        </ul>
    </aside>

    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">
                <a href="{{ route('admin.children.show', $child->child_id) }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                <span>Edit Child Record #{{ $child->child_id }}</span>
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <div class="form-container">
            <form action="{{ route('admin.children.update', $child->child_id) }}" method="POST" class="form-card">
                @csrf
                @method('PUT')

                <div>
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-person-breastfeeding" style="color:#00c853;"></i> Mother &amp; Demographics</h2>
                    </div>
                    <div class="form-grid" style="margin-top:16px;">
                        <div class="form-group">
                            <label class="form-label" for="mother_id">Linked Mother *</label>
                            <select name="mother_id" id="mother_id" class="form-select" required>
                                @foreach($mothers as $m)
                                    <option value="{{ $m->mother_id }}" {{ old('mother_id', $child->mother_id) == $m->mother_id ? 'selected' : '' }}>
                                        Mother #{{ $m->mother_id }} — {{ $m->mother_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="child_name">Child Name</label>
                            <input type="text" name="child_name" id="child_name" class="form-input" value="{{ old('child_name', $child->child_name) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="gender">Gender *</label>
                            <select name="gender" id="gender" class="form-select" required>
                                <option value="Male" {{ old('gender', $child->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $child->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth *</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" value="{{ old('date_of_birth', \Carbon\Carbon::parse($child->date_of_birth)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="birth_weight">Birth Weight (kg) *</label>
                            <input type="number" step="0.01" name="birth_weight" id="birth_weight" class="form-input" value="{{ old('birth_weight', $child->birth_weight) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="birth_length">Birth Length (cm)</label>
                            <input type="number" step="0.1" name="birth_length" id="birth_length" class="form-input" value="{{ old('birth_length', $child->birth_length) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="area_id">Clinic Area</label>
                            <select name="area_id" id="area_id" class="form-select">
                                @foreach($areas as $area)
                                    <option value="{{ $area->area_id }}" {{ old('area_id', $child->area_id) == $area->area_id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="midwife_id">Assigned Midwife</label>
                            <select name="midwife_id" id="midwife_id" class="form-select">
                                @foreach($midwives as $mw)
                                    <option value="{{ $mw->midwife_id }}" {{ old('midwife_id', $child->midwife_id) == $mw->midwife_id ? 'selected' : '' }}>
                                        {{ $mw->midwife_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-shield-virus" style="color:#b45309;"></i> BCG Vaccination</h2>
                    </div>
                    <div class="form-grid" style="margin-top:16px;">
                        <div class="form-group" style="grid-column:span 2; display:flex; align-items:center; gap:10px;">
                            <input type="checkbox" name="bcg_vaccinated_at_birth" id="bcg_vaccinated_at_birth" value="1" style="width:20px; height:20px;" {{ old('bcg_vaccinated_at_birth', $child->bcg_vaccinated_at_birth) ? 'checked' : '' }}>
                            <label for="bcg_vaccinated_at_birth" style="font-size:14px; font-weight:700; cursor:pointer;">
                                BCG Vaccinated within 24 hours of birth
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bcg_batch_no">BCG Batch No</label>
                            <input type="text" name="bcg_batch_no" id="bcg_batch_no" class="form-input" value="{{ old('bcg_batch_no', $child->bcg_batch_no) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="bcg_vaccinated_date">BCG Date Vaccinated</label>
                            <input type="date" name="bcg_vaccinated_date" id="bcg_vaccinated_date" class="form-input" value="{{ old('bcg_vaccinated_date', $child->bcg_vaccinated_date ? \Carbon\Carbon::parse($child->bcg_vaccinated_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-notes-medical" style="color:#0284c7;"></i> Clinical Observations</h2>
                    </div>
                    <div style="margin-top:16px;">
                        <textarea name="health_details" id="health_details" rows="3" class="form-textarea">{{ old('health_details', $child->health_details) }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.children.show', $child->child_id) }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

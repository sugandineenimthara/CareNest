<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit {{ $mother->mother_name }} - CareNest</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root { --font-main:'Plus Jakarta Sans',sans-serif; --font-heading:'Outfit',sans-serif; --sidebar-width:260px; --bg-gradient:linear-gradient(135deg,#eef2ff 0%,#f0fdf4 50%,#f0f9ff 100%); --text-dark:#1e293b; --text-muted:#64748b; }
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:var(--font-main); background:var(--bg-gradient); background-attachment:fixed; color:var(--text-dark); min-height:100vh; display:flex; }
        .sidebar { width:var(--sidebar-width); background:rgba(255,255,255,0.95); backdrop-filter:blur(20px); border-right:1px solid rgba(226,232,240,0.8); display:flex; flex-direction:column; padding:30px 20px; position:fixed; top:0; bottom:0; left:0; z-index:100; }
        .sidebar-brand { display:flex; align-items:center; gap:10px; font-family:var(--font-heading); font-size:24px; font-weight:800; color:#1b4d3e; text-decoration:none; margin-bottom:36px; padding-left:10px; }
        .sidebar-menu { list-style:none; display:flex; flex-direction:column; gap:6px; flex:1; overflow-y:auto; padding-right:4px; }
        .nav-item a { display:flex; align-items:center; gap:14px; padding:12px 18px; border-radius:16px; font-size:14px; font-weight:600; color:#64748b; text-decoration:none; transition:all 0.25s ease; }
        .nav-item a:hover { background:#f1f5f9; }
        .nav-item.active a { background:linear-gradient(135deg,#1b5e20 0%,#00695c 100%); color:#fff; }
        .nav-item i { font-size:16px; width:20px; text-align:center; }
        .sidebar-footer { margin-top:20px; }
        .logout-btn { display:flex; align-items:center; gap:10px; background:transparent; border:none; padding:10px 18px; font-family:var(--font-main); font-size:14px; font-weight:600; color:#64748b; cursor:pointer; transition:color 0.2s; width:100%; }
        .logout-btn:hover { color:#ef4444; }
        .main-wrapper { margin-left:var(--sidebar-width); flex:1; display:flex; flex-direction:column; min-height:100vh; }
        .top-header { display:flex; justify-content:space-between; align-items:center; padding:20px 48px; background:rgba(255,255,255,0.7); backdrop-filter:blur(16px); border-bottom:1px solid rgba(226,232,240,0.6); position:sticky; top:0; z-index:90; }
        .header-left { font-family:var(--font-heading); font-size:22px; font-weight:800; color:#1b4d3e; display:flex; align-items:center; gap:12px; }
        .back-btn { background:#f1f5f9; border:none; width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#64748b; cursor:pointer; text-decoration:none; transition:all 0.2s; }
        .back-btn:hover { background:#e2e8f0; color:#1b5e20; }
        .user-badge-container { display:flex; align-items:center; gap:10px; border-left:1px solid #e2e8f0; padding-left:12px; }
        .user-role-label { font-size:13px; font-weight:700; color:#334155; }
        .user-avatar { width:38px; height:38px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; color:#475569; }
        .content-container { padding:40px 48px; max-width:960px; width:100%; margin:0 auto; }
        .page-title { font-family:var(--font-heading); font-size:30px; font-weight:800; color:#0f172a; margin-bottom:4px; }
        .page-sub { font-size:14px; color:#64748b; margin-bottom:32px; }
        .form-section { background:rgba(255,255,255,0.92); backdrop-filter:blur(16px); border-radius:28px; padding:36px; border:1px solid rgba(255,255,255,0.8); box-shadow:0 12px 36px rgba(0,0,0,0.05); margin-bottom:24px; }
        .section-header { display:flex; align-items:center; gap:14px; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid #f1f5f9; }
        .section-icon { width:44px; height:44px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
        .sec-green { background:#e8f5e9; color:#2e7d32; }
        .section-title { font-family:var(--font-heading); font-size:18px; font-weight:800; color:#0f172a; }
        .form-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        .span-2 { grid-column:span 2; }
        .form-group { display:flex; flex-direction:column; gap:6px; }
        .form-label { font-size:12px; font-weight:800; color:#475569; text-transform:uppercase; letter-spacing:0.8px; }
        .form-label .req { color:#e11d48; }
        .form-control { padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:14px; font-family:var(--font-main); font-size:14px; color:var(--text-dark); background:#fafafa; outline:none; transition:all 0.2s; width:100%; }
        .form-control:focus { border-color:#00c853; background:#fff; box-shadow:0 0 0 3px rgba(0,200,83,0.1); }
        .form-control.is-invalid { border-color:#ef4444; }
        select.form-control { cursor:pointer; }
        textarea.form-control { resize:vertical; min-height:80px; }
        .field-error { font-size:11px; color:#ef4444; font-weight:600; }
        .form-actions { display:flex; justify-content:flex-end; gap:14px; }
        .btn-cancel { padding:13px 24px; border-radius:14px; border:1.5px solid #e2e8f0; background:#fff; font-family:var(--font-heading); font-size:15px; font-weight:700; color:#64748b; cursor:pointer; text-decoration:none; }
        .btn-submit { padding:13px 32px; border-radius:14px; border:none; background:linear-gradient(135deg,#1b5e20,#00695c); font-family:var(--font-heading); font-size:15px; font-weight:700; color:#fff; cursor:pointer; box-shadow:0 8px 20px rgba(27,94,32,0.25); transition:all 0.25s; display:flex; align-items:center; gap:8px; }
        .btn-submit:hover { transform:translateY(-2px); }
        .error-box { background:#fef2f2; border:1px solid #fca5a5; border-radius:16px; padding:16px 20px; margin-bottom:24px; }
        .error-box-title { font-size:14px; font-weight:800; color:#dc2626; margin-bottom:8px; }
        .error-box li { font-size:13px; color:#b91c1c; list-style:none; }
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
                <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}" class="back-btn"><i class="fa-solid fa-arrow-left"></i></a>
                Edit Mother Record
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <main class="content-container">
            <h1 class="page-title">Edit: {{ $mother->mother_name }}</h1>
            <p class="page-sub">Update the mother's personal details. ID #{{ $mother->mother_id }}</p>

            @if($errors->any())
                <div class="error-box">
                    <div class="error-box-title"><i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:</div>
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>• {{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route($prefix . 'mothers.update', $mother->mother_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon sec-green"><i class="fa-solid fa-id-card"></i></div>
                        <div>
                            <div class="section-title">Personal Information</div>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="mother_name">Mother's Full Name <span class="req">*</span></label>
                            <input type="text" id="mother_name" name="mother_name" class="form-control @error('mother_name') is-invalid @enderror"
                                value="{{ old('mother_name', $mother->mother_name) }}" required>
                            @error('mother_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone_no">Phone Number</label>
                            <input type="tel" id="phone_no" name="phone_no" class="form-control @error('phone_no') is-invalid @enderror"
                                value="{{ old('phone_no', $mother->phone_no) }}">
                            @error('phone_no')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror"
                                value="{{ old('date_of_birth', $mother->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="midwife_id">Assigned Midwife / Area <span class="req">*</span></label>
                            <select id="midwife_id" name="midwife_id" class="form-control @error('midwife_id') is-invalid @enderror" required>
                                <option value="">— Select Area & Midwife —</option>
                                @foreach($areas as $area)
                                    @php
                                        $midwivesList = $area->midwives->pluck('midwife_name')->filter()->unique()->join(', ');
                                        $primaryMidwife = $area->midwives->first();
                                        $isSelected = ($mother->midwife && $mother->midwife->area_id == $area->area_id)
                                            || (old('midwife_id') && $area->midwives->pluck('midwife_id')->contains(old('midwife_id')));
                                        $optionValue = ($mother->midwife && $mother->midwife->area_id == $area->area_id)
                                            ? $mother->midwife_id
                                            : ($primaryMidwife ? $primaryMidwife->midwife_id : '');
                                    @endphp
                                    @if($primaryMidwife)
                                        <option value="{{ $optionValue }}" {{ $isSelected ? 'selected' : '' }}>
                                            {{ $area->area_name }} ({{ $midwivesList ?: 'No Midwife Assigned' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('midwife_id')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="height">Height (cm)</label>
                            <input type="number" id="height" name="height" step="0.01" min="50" max="250"
                                class="form-control @error('height') is-invalid @enderror"
                                value="{{ old('height', $mother->height) }}">
                            @error('height')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="weight">Weight (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.01" min="20" max="300"
                                class="form-control @error('weight') is-invalid @enderror"
                                value="{{ old('weight', $mother->weight) }}">
                            @error('weight')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="husband_name">Husband's Name</label>
                            <input type="text" id="husband_name" name="husband_name" class="form-control @error('husband_name') is-invalid @enderror"
                                value="{{ old('husband_name', $mother->husband_name) }}">
                            @error('husband_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="husband_occupation">Husband's Occupation</label>
                            <input type="text" id="husband_occupation" name="husband_occupation" class="form-control @error('husband_occupation') is-invalid @enderror"
                                value="{{ old('husband_occupation', $mother->husband_occupation) }}">
                            @error('husband_occupation')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group span-2">
                            <label class="form-label" for="address">Home Address</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $mother->address) }}</textarea>
                            @error('address')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fa-solid fa-baby-carriage"></i></div>
                        <div>
                            <div class="section-title">Current Pregnancy Details</div>
                            <div style="font-size:12px; color:#64748b;">Record or update current pregnancy information</div>
                        </div>
                    </div>

                    @if($activePregnancy)
                        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:14px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
                            <div>
                                <span style="font-weight:800; color:#166534; font-size:13px;"><i class="fa-solid fa-circle-check"></i> Active Pregnancy in Progress</span>
                                <div style="font-size:12px; color:#475569; margin-top:2px;">
                                    EDD: {{ $activePregnancy->expected_date_of_delivery ? \Carbon\Carbon::parse($activePregnancy->expected_date_of_delivery)->format('d M Y') : 'Not set' }}
                                </div>
                            </div>
                            <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#1b5e20; cursor:pointer;">
                                <input type="checkbox" name="is_new_pregnancy" id="is_new_pregnancy" value="1" style="width:16px; height:16px; accent-color:#16a34a;">
                                Record as New Pregnancy (Archive previous to history)
                            </label>
                        </div>
                    @else
                        <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px; padding:14px 18px; margin-bottom:20px;">
                            <span style="font-weight:800; color:#1e40af; font-size:13px;"><i class="fa-solid fa-circle-info"></i> Record Next Pregnancy</span>
                            <div style="font-size:12px; color:#475569; margin-top:2px;">
                                Previous pregnancy delivered. Enter details below to record her new current pregnancy. Previous pregnancy records remain preserved in her profile.
                            </div>
                        </div>
                    @endif

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label" for="no_living_children">Number of Living Children</label>
                            <input type="number" id="no_living_children" name="no_living_children" min="0" class="form-control @error('no_living_children') is-invalid @enderror"
                                value="{{ old('no_living_children', $activePregnancy?->no_living_children ?? $mother->children->count()) }}">
                            @error('no_living_children')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="age_of_youngest_child">Age of Youngest Child (Years)</label>
                            <input type="number" id="age_of_youngest_child" name="age_of_youngest_child" min="0" class="form-control @error('age_of_youngest_child') is-invalid @enderror"
                                value="{{ old('age_of_youngest_child', $activePregnancy?->age_of_youngest_child ?? '') }}">
                            @error('age_of_youngest_child')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="last_menstrual_period">Last Menstrual Period (LMP)</label>
                            <input type="date" id="last_menstrual_period" name="last_menstrual_period" class="form-control @error('last_menstrual_period') is-invalid @enderror"
                                value="{{ old('last_menstrual_period', $activePregnancy?->last_menstrual_period ? \Carbon\Carbon::parse($activePregnancy->last_menstrual_period)->format('Y-m-d') : '') }}">
                            @error('last_menstrual_period')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="expected_date_of_delivery">Expected Date of Delivery (EDD)</label>
                            <input type="date" id="expected_date_of_delivery" name="expected_date_of_delivery" class="form-control @error('expected_date_of_delivery') is-invalid @enderror"
                                value="{{ old('expected_date_of_delivery', $activePregnancy?->expected_date_of_delivery ? \Carbon\Carbon::parse($activePregnancy->expected_date_of_delivery)->format('Y-m-d') : '') }}">
                            @error('expected_date_of_delivery')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="date_confirmed_by_US">Date Confirmed by US</label>
                            <input type="date" id="date_confirmed_by_US" name="date_confirmed_by_US" class="form-control @error('date_confirmed_by_US') is-invalid @enderror"
                                value="{{ old('date_confirmed_by_US', $activePregnancy?->date_confirmed_by_US ? \Carbon\Carbon::parse($activePregnancy->date_confirmed_by_US)->format('Y-m-d') : '') }}">
                            @error('date_confirmed_by_US')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="no_of_weeks_pregnant_at_registration">Weeks Pregnant at Registration</label>
                            <input type="number" id="no_of_weeks_pregnant_at_registration" name="no_of_weeks_pregnant_at_registration" min="0" max="45" class="form-control @error('no_of_weeks_pregnant_at_registration') is-invalid @enderror"
                                value="{{ old('no_of_weeks_pregnant_at_registration', $activePregnancy?->no_of_weeks_pregnant_at_registration ?? '') }}">
                            @error('no_of_weeks_pregnant_at_registration')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group span-2">
                            <label class="form-label" for="first_fetal_movements_date">First Fetal Movements Date</label>
                            <input type="date" id="first_fetal_movements_date" name="first_fetal_movements_date" class="form-control @error('first_fetal_movements_date') is-invalid @enderror"
                                value="{{ old('first_fetal_movements_date', $activePregnancy?->first_fetal_movements_date ? \Carbon\Carbon::parse($activePregnancy->first_fetal_movements_date)->format('Y-m-d') : '') }}">
                            @error('first_fetal_movements_date')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon" style="background:#ede7f6; color:#6d28d9;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div>
                            <div class="section-title">Previous Pregnancy Records</div>
                            <div style="font-size:12px; color:#64748b;">Preserved history of past pregnancies ({{ $mother->previousPregnancyHistories->count() }} record{{ $mother->previousPregnancyHistories->count() === 1 ? '' : 's' }})</div>
                        </div>
                    </div>

                    @if($mother->previousPregnancyHistories->count() > 0)
                        <div style="overflow-x:auto;">
                            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                                <thead>
                                    <tr style="background:#f8fafc; border-bottom:1.5px solid #e2e8f0; text-align:left;">
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">#</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Pregnancy</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Date of Birth</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Weight</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Gender</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Results</th>
                                        <th style="padding:10px 12px; font-weight:700; color:#475569;">Place</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mother->previousPregnancyHistories as $i => $prev)
                                        <tr style="border-bottom:1px solid #f1f5f9;">
                                            <td style="padding:10px 12px; font-weight:700; color:#94a3b8;">{{ $i+1 }}</td>
                                            <td style="padding:10px 12px; font-weight:700; color:#1e293b;">{{ $prev->which_pregnancy ?? '—' }}</td>
                                            <td style="padding:10px 12px;">{{ $prev->date_of_birth ? $prev->date_of_birth->format('d M Y') : '—' }}</td>
                                            <td style="padding:10px 12px;">{{ $prev->birth_weight ? $prev->birth_weight . ' kg' : '—' }}</td>
                                            <td style="padding:10px 12px;">{{ $prev->gender ?? '—' }}</td>
                                            <td style="padding:10px 12px;"><span style="background:#f1f5f9; padding:2px 8px; border-radius:6px; font-size:12px; font-weight:600;">{{ $prev->results ?? 'Delivered' }}</span></td>
                                            <td style="padding:10px 12px; color:#64748b;">{{ $prev->place_of_pregnancy ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="font-size:13px; color:#94a3b8; text-align:center; padding:16px;">
                            <i class="fa-solid fa-notes-medical" style="margin-right:6px;"></i> No previous pregnancy records on file.
                        </div>
                    @endif
                </div>

                <div class="form-actions">
                    <a href="{{ route($prefix . 'mothers.show', $mother->mother_id) }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>

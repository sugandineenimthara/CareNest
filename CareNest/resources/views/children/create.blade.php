<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Child - CareNest</title>
    <meta name="description" content="Register a new newborn or child linked to mother and record BCG vaccination in CareNest.">

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
            font-family: var(--font-main);
            background: var(--bg-gradient);
            background-attachment: fixed;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226,232,240,0.8);
            display: flex; flex-direction: column;
            padding: 30px 20px;
            position: fixed; top: 0; bottom: 0; left: 0;
            z-index: 100;
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
        .sidebar-footer { margin-top: 20px; display: flex; flex-direction: column; gap: 12px; }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            background: transparent; border: none; padding: 10px 18px;
            font-family: var(--font-main); font-size: 14px; font-weight: 600;
            color: #64748b; cursor: pointer; transition: color 0.2s ease; width: 100%;
        }
        .logout-btn:hover { color: #ef4444; }

        /* Main */
        .main-wrapper { margin-left: var(--sidebar-width); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .top-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 48px; background: rgba(255,255,255,0.7); backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226,232,240,0.6); position: sticky; top: 0; z-index: 90;
        }
        .header-left {
            display: flex; align-items: center; gap: 14px;
            font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #1b4d3e;
        }
        .back-btn {
            width: 38px; height: 38px; border-radius: 50%; background: #f1f5f9;
            display: flex; align-items: center; justify-content: center;
            color: #475569; text-decoration: none; transition: all 0.2s ease;
        }
        .back-btn:hover { background: #e2e8f0; color: #1e293b; }

        .form-container { padding: 36px 48px; max-width: 1000px; margin: 0 auto; width: 100%; }

        .page-intro { margin-bottom: 28px; }
        .page-intro h1 { font-family: var(--font-heading); font-size: 28px; font-weight: 800; color: #1b4d3e; }
        .page-intro p { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

        .form-card {
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border: 1px solid rgba(226,232,240,0.9); border-radius: 24px;
            padding: 36px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            display: flex; flex-direction: column; gap: 32px;
        }

        .section-header {
            display: flex; align-items: center; gap: 12px;
            padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;
        }
        .section-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: #ecfdf5; color: #047857;
            display: flex; align-items: center; justify-content: center; font-size: 16px;
        }
        .section-title { font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #1e293b; }

        .form-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;
        }
        .form-grid-3 {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;
        }
        .form-group-full { grid-column: span 2; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label {
            font-size: 13px; font-weight: 700; color: #334155;
            display: flex; align-items: center; justify-content: space-between;
        }
        .form-label .required { color: #ef4444; }
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 12px 16px; background: #f8fafc;
            border: 1.5px solid #e2e8f0; border-radius: 14px;
            font-family: var(--font-main); font-size: 14px; color: var(--text-dark);
            transition: all 0.2s ease;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            background: white; border-color: #00e676;
            outline: none; box-shadow: 0 0 0 4px rgba(0,230,118,0.15);
        }

        /* Mother live preview box */
        .mother-preview-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
            border: 1.5px dashed #86efac; border-radius: 16px;
            padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;
            gap: 16px;
        }
        .preview-details { display: flex; align-items: center; gap: 14px; }
        .preview-avatar {
            width: 44px; height: 44px; border-radius: 50%; background: #dcfce7;
            color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .preview-name { font-size: 15px; font-weight: 800; color: #166534; }
        .preview-sub { font-size: 12px; color: #4b5563; margin-top: 2px; }

        /* Gender selector */
        .gender-options { display: flex; gap: 14px; }
        .gender-label {
            flex: 1; padding: 12px 18px; border-radius: 14px; border: 1.5px solid #e2e8f0;
            background: #f8fafc; cursor: pointer; display: flex; align-items: center; justify-content: center;
            gap: 10px; font-size: 14px; font-weight: 700; transition: all 0.2s ease;
        }
        .gender-input { display: none; }
        .gender-input:checked + .gender-label {
            border-color: #00c853; background: #ecfdf5; color: #047857;
            box-shadow: 0 4px 14px rgba(0,200,83,0.15);
        }

        /* BCG Checkbox Box */
        .bcg-card {
            background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 18px;
            padding: 20px; display: flex; flex-direction: column; gap: 16px;
        }
        .bcg-check-row {
            display: flex; align-items: center; gap: 14px; cursor: pointer;
        }
        .bcg-checkbox {
            width: 22px; height: 22px; accent-color: #00c853; cursor: pointer;
        }
        .bcg-title { font-size: 15px; font-weight: 800; color: #92400e; }
        .bcg-desc { font-size: 12px; color: #b45309; }

        .bcg-fields {
            display: none; grid-template-columns: repeat(2, 1fr); gap: 16px;
            padding-top: 14px; border-top: 1px dashed #fde68a;
        }
        .bcg-fields.show { display: grid; }

        /* Form Buttons */
        .form-actions {
            display: flex; justify-content: flex-end; align-items: center; gap: 14px;
            margin-top: 10px;
        }
        .btn-cancel {
            padding: 13px 26px; border-radius: 14px; border: 1.5px solid #cbd5e1;
            background: white; color: #475569; font-family: var(--font-heading);
            font-size: 14px; font-weight: 700; text-decoration: none; transition: all 0.2s ease;
        }
        .btn-cancel:hover { background: #f8fafc; color: #1e293b; }
        .btn-submit {
            padding: 14px 32px; border-radius: 14px; border: none;
            background: linear-gradient(135deg, #00e676 0%, #00c853 100%);
            color: white; font-family: var(--font-heading); font-size: 15px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 8px 20px rgba(0,200,83,0.3); transition: all 0.25s ease;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,200,83,0.4); }

        .error-box {
            background: #fef2f2; border: 1px solid #fecaca; border-radius: 14px;
            padding: 16px 20px; color: #991b1b; font-size: 13px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
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

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="top-header">
            <div class="header-left">
                <a href="{{ route('admin.children.index') }}" class="back-btn" title="Back to Children">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <span>Register New Child</span>
            </div>
            <div class="header-right">
                @include('partials.top-header-user')
            </div>
        </header>

        <div class="form-container">
            <div class="page-intro">
                <h1>Child Registration</h1>
                <p>Register a newborn or child, link to the biological mother, and record neonatal BCG immunization.</p>
            </div>

            @if($errors->any())
                <div class="error-box" style="margin-bottom: 24px;">
                    <div style="font-weight:700; margin-bottom:6px;"><i class="fa-solid fa-circle-exclamation"></i> Please fix the following errors:</div>
                    <ul style="margin-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('children.store') }}" method="POST" class="form-card" id="childRegistrationForm">
                @csrf

                <!-- Section 1: Mother Association -->
                <div>
                    <div class="section-header">
                        <div class="section-icon"><i class="fa-solid fa-person-breastfeeding"></i></div>
                        <div>
                            <h2 class="section-title">Mother Association</h2>
                            <p style="font-size:12px; color:var(--text-muted);">Link this child with their biological mother's unique record</p>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:16px; margin-top:18px;">
                        <div class="form-group">
                            <label class="form-label" for="mother_id">
                                <span>Select Mother (Unique ID / Name) <span class="required">*</span></span>
                                <span style="font-size:11px; color:#059669; font-weight:600;"><i class="fa-solid fa-id-card"></i> Links child to mother permanently</span>
                            </label>
                            <select name="mother_id" id="mother_id" class="form-select" required onchange="handleMotherChange()">
                                <option value="">-- Choose Mother (ID - Name - Area) --</option>
                                @foreach($mothers as $m)
                                    <option value="{{ $m->mother_id }}"
                                        data-name="{{ $m->mother_name }}"
                                        data-phone="{{ $m->phone_no ?? 'N/A' }}"
                                        data-midwife-id="{{ $m->midwife_id }}"
                                        data-midwife-name="{{ $m->midwife ? $m->midwife->midwife_name : 'N/A' }}"
                                        data-area-id="{{ $m->midwife?->area_id }}"
                                        data-area-name="{{ $m->midwife?->area ? $m->midwife->area->area_name : 'N/A' }}"
                                        {{ (old('mother_id', $selectedMotherId) == $m->mother_id) ? 'selected' : '' }}>
                                        Mother #{{ $m->mother_id }} — {{ $m->mother_name }} (Area: {{ $m->midwife?->area ? $m->midwife->area->area_name : 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Live preview box of chosen mother -->
                        <div class="mother-preview-box" id="motherPreviewBox" style="display:none;">
                            <div class="preview-details">
                                <div class="preview-avatar"><i class="fa-solid fa-user-nurse"></i></div>
                                <div>
                                    <div class="preview-name" id="previewMotherName">Mother Name</div>
                                    <div class="preview-sub" id="previewMotherDetails">ID: #0 • Phone: N/A • Midwife: N/A • Area: N/A</div>
                                </div>
                            </div>
                            <span style="font-size:12px; font-weight:700; color:#15803d; background:white; padding:4px 10px; border-radius:10px; border:1px solid #86efac;">
                                <i class="fa-solid fa-link"></i> Linked
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Child Demographics & Measurements -->
                <div>
                    <div class="section-header">
                        <div class="section-icon"><i class="fa-solid fa-baby"></i></div>
                        <div>
                            <h2 class="section-title">Child Demographics & Measurements</h2>
                            <p style="font-size:12px; color:var(--text-muted);">Birth attributes, weight, length, and clinic area</p>
                        </div>
                    </div>

                    <div class="form-grid" style="margin-top:18px;">
                        <div class="form-group">
                            <label class="form-label" for="child_name">Child Full Name (or Baby of Mother)</label>
                            <input type="text" name="child_name" id="child_name" class="form-input" placeholder="e.g. Baby of Kasuni or Amaya Perera" value="{{ old('child_name') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gender <span class="required">*</span></label>
                            <div class="gender-options">
                                <input type="radio" name="gender" id="gender_male" value="Male" class="gender-input" {{ old('gender', 'Male') === 'Male' ? 'checked' : '' }} required>
                                <label for="gender_male" class="gender-label">
                                    <i class="fa-solid fa-mars" style="color:#0284c7;"></i> Male
                                </label>

                                <input type="radio" name="gender" id="gender_female" value="Female" class="gender-input" {{ old('gender') === 'Female' ? 'checked' : '' }} required>
                                <label for="gender_female" class="gender-label">
                                    <i class="fa-solid fa-venus" style="color:#db2777;"></i> Female
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth <span class="required">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-input" max="{{ date('Y-m-d') }}" value="{{ old('date_of_birth', date('Y-m-d')) }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="birth_weight">Birth Weight (kg) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="birth_weight" id="birth_weight" class="form-input" placeholder="e.g. 3.25" value="{{ old('birth_weight') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="birth_length">Birth Length (cm)</label>
                            <input type="number" step="0.1" name="birth_length" id="birth_length" class="form-input" placeholder="e.g. 50.0" value="{{ old('birth_length') }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="area_id">Clinic Area</label>
                            <select name="area_id" id="area_id" class="form-select">
                                <option value="">-- Auto-inherit from Mother/Midwife --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->area_id }}" {{ old('area_id') == $area->area_id ? 'selected' : '' }}>
                                        {{ $area->area_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Neonatal BCG Vaccination (Checkbox Requirement) -->
                <div>
                    <div class="section-header">
                        <div class="section-icon" style="background:#fef3c7; color:#b45309;"><i class="fa-solid fa-syringe"></i></div>
                        <div>
                            <h2 class="section-title">Neonatal BCG Vaccination</h2>
                            <p style="font-size:12px; color:var(--text-muted);">Record whether BCG was vaccinated before 24 hours from birth</p>
                        </div>
                    </div>

                    <div class="bcg-card" style="margin-top:18px;">
                        <label class="bcg-check-row" for="bcg_vaccinated_at_birth">
                            <input type="checkbox" name="bcg_vaccinated_at_birth" id="bcg_vaccinated_at_birth" value="1" class="bcg-checkbox" {{ old('bcg_vaccinated_at_birth') ? 'checked' : '' }} onchange="toggleBcgFields()">
                            <div>
                                <div class="bcg-title">
                                    <i class="fa-solid fa-shield-virus"></i> BCG Vaccinated within 24 hours of birth
                                </div>
                                <div class="bcg-desc">
                                    Check this box if the newborn was administered BCG before 24hrs from delivery at the hospital or clinic.
                                </div>
                            </div>
                        </label>

                        <div class="bcg-fields {{ old('bcg_vaccinated_at_birth') ? 'show' : '' }}" id="bcgFields">
                            <div class="form-group">
                                <label class="form-label" for="bcg_batch_no">BCG Vaccine Batch Number <span class="required">*</span></label>
                                <input type="text" name="bcg_batch_no" id="bcg_batch_no" class="form-input" placeholder="e.g. BCG-2026-A109" value="{{ old('bcg_batch_no', 'BCG-' . date('Ymd')) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="bcg_vaccinated_date">Date Vaccinated</label>
                                <input type="date" name="bcg_vaccinated_date" id="bcg_vaccinated_date" class="form-input" value="{{ old('bcg_vaccinated_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Clinical Health Details -->
                <div>
                    <div class="section-header">
                        <div class="section-icon"><i class="fa-solid fa-notes-medical"></i></div>
                        <div>
                            <h2 class="section-title">Clinical Notes & Health Details</h2>
                            <p style="font-size:12px; color:var(--text-muted);">APGAR score, birth condition, complications, or special observations</p>
                        </div>
                    </div>

                    <div style="margin-top:18px;">
                        <div class="form-group">
                            <label class="form-label" for="health_details">Health Notes / Observations</label>
                            <textarea name="health_details" id="health_details" rows="3" class="form-textarea" placeholder="Normal vaginal delivery, cried immediately, no congenital anomalies, APGAR 9/10...">{{ old('health_details') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('admin.children.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check"></i>
                        <span>Register Child &amp; Link Mother</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleMotherChange() {
            const select = document.getElementById('mother_id');
            const selectedOpt = select.options[select.selectedIndex];
            const previewBox = document.getElementById('motherPreviewBox');

            if (selectedOpt && selectedOpt.value) {
                const name = selectedOpt.getAttribute('data-name');
                const phone = selectedOpt.getAttribute('data-phone');
                const midwifeName = selectedOpt.getAttribute('data-midwife-name');
                const areaName = selectedOpt.getAttribute('data-area-name');
                const areaId = selectedOpt.getAttribute('data-area-id');

                document.getElementById('previewMotherName').textContent = name;
                document.getElementById('previewMotherDetails').textContent = 
                    `ID: #${selectedOpt.value} • Phone: ${phone} • Midwife: ${midwifeName} • Area: ${areaName}`;
                
                previewBox.style.display = 'flex';

                // Auto-select area if available
                if (areaId) {
                    const areaSelect = document.getElementById('area_id');
                    if (areaSelect) {
                        areaSelect.value = areaId;
                    }
                }
            } else {
                previewBox.style.display = 'none';
            }
        }

        function toggleBcgFields() {
            const check = document.getElementById('bcg_vaccinated_at_birth');
            const fields = document.getElementById('bcgFields');
            if (check.checked) {
                fields.classList.add('show');
            } else {
                fields.classList.remove('show');
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            handleMotherChange();
            toggleBcgFields();
        });
    </script>
</body>
</html>

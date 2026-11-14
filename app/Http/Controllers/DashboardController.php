<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\FamilyHealthHistory;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\TestDone;
use App\Models\TriposhaBook;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $registeredMothersCount = Mother::count();
        $childrenEnrolledCount = Child::count();

        // Collect all high-risk mother IDs across all alert categories (deduplicated)
        $highRiskMotherIds = collect();

        // 1. High BP from mother profiles
        Mother::whereNotNull('blood_pressure')
            ->where('blood_pressure', '!=', '')
            ->get()
            ->filter(fn($m) => $m->isHighBloodPressure())
            ->each(fn($m) => $highRiskMotherIds->push($m->mother_id));

        // 2. High BP from clinic attendances (maternal type)
        Attendance::where('clinic_type', 'mother')
            ->where(function ($q) {
                $q->whereNotNull('blood_pressure')->where('blood_pressure', '!=', '')
                  ->orWhere(function ($sq) {
                      $sq->whereNotNull('remarks')->where('remarks', '!=', '');
                  });
            })
            ->get()
            ->filter(fn($att) => $att->isHighBloodPressure() && $att->mother_id)
            ->each(fn($att) => $highRiskMotherIds->push($att->mother_id));

        // 3. High BP from family health history (exclude normal/negative values)
        FamilyHealthHistory::whereNotNull('high_blood_pressure')
            ->whereNotIn('high_blood_pressure', [
                'no', 'No', 'NO', 'false', '0', '', 'none', 'None', 'NONE',
                'nil', 'Nil', 'normal', 'Normal', 'N/A', 'na',
            ])
            ->pluck('mother_id')
            ->each(fn($id) => $highRiskMotherIds->push($id));

        // 4. Low Haemoglobin (< 11.0 g/dL)
        TestDone::where('Haemoglobin', '<', 11.0)
            ->pluck('mother_id')
            ->each(fn($id) => $highRiskMotherIds->push($id));

        // 5. High Blood/Urine Sugar
        TestDone::where(function ($q) {
                $q->where('urine_sugar_level', '!=', 'Negative')
                  ->orWhere('blood_sugar', 'like', '%high%');
            })
            ->pluck('mother_id')
            ->each(fn($id) => $highRiskMotherIds->push($id));

        // Deduplicate and count unique high-risk mothers
        $highRiskCount = $highRiskMotherIds->filter()->unique()->count();

        $immunizationsDueCount = Immunization::count();
        $triposhaPacketsCount = TriposhaBook::sum('no_of_packets');

        $clinicAreas = Area::withCount(['midwives'])->get()->map(function ($area) {
            $midwifeIds = Midwife::where('area_id', $area->area_id)->pluck('midwife_id');
            $patientCount = Mother::whereIn('midwife_id', $midwifeIds)->count() + Child::whereIn('midwife_id', $midwifeIds)->count();

            $area->patient_count = $patientCount > 0 ? $patientCount : '-';
            $area->staff_on_duty = max(1, (int) $area->midwives_count);
            $area->active_alerts = '-';
            return $area;
        });

        $attendancesQuery = Attendance::with(['mother', 'child', 'midwife']);

        if ($user->role === 'mother') {
            if ($user->mother) {
                $attendancesQuery->where('mother_id', $user->mother->mother_id);
            } else {
                $attendancesQuery->where('mother_id', -1);
            }
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $attendancesQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        $recentAttendances = $attendancesQuery->latest()->take(5)->get();

        $role = $user->role;
        $prefix = ($role === 'midwife') ? 'midwife.' : ($role === 'mother' ? 'mother.' : 'admin.');

        return view('dashboard', compact(
            'user',
            'role',
            'prefix',
            'registeredMothersCount',
            'childrenEnrolledCount',
            'highRiskCount',
            'immunizationsDueCount',
            'triposhaPacketsCount',
            'clinicAreas',
            'recentAttendances'
        ));
    }
}

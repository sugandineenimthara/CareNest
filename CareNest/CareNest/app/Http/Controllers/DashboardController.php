<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Child;
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

        // Core Dashboard Statistics (Dynamic live counts from DB)
        $registeredMothersCount = Mother::count();
        $childrenEnrolledCount = Child::count();

        // High Risk Cases: Mothers with low Haemoglobin (< 11 g/dL) or positive test alerts
        $highRiskCount = TestDone::where('Haemoglobin', '<', 11.0)
            ->orWhere('urine_sugar_level', '!=', 'Negative')
            ->orWhere('blood_sugar', 'like', '%high%')
            ->distinct('mother_id')
            ->count('mother_id');

        $immunizationsDueCount = Immunization::count();
        $triposhaPacketsCount = TriposhaBook::sum('no_of_packets');

        // Clinic Areas Overview with patient counts and midwives on duty
        $clinicAreas = Area::withCount(['midwives'])->get()->map(function ($area) {
            $midwifeIds = Midwife::where('area_id', $area->area_id)->pluck('midwife_id');
            $patientCount = Mother::whereIn('midwife_id', $midwifeIds)->count() + Child::whereIn('midwife_id', $midwifeIds)->count();
            
            $area->patient_count = $patientCount > 0 ? $patientCount : '-';
            $area->staff_on_duty = $area->midwives_count > 0 ? $area->midwives_count : '-';
            $area->active_alerts = '-';
            return $area;
        });

        // Recent Attendances
        $recentAttendances = Attendance::with(['mother', 'child', 'midwife'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'user',
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

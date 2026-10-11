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

        $registeredMothersCount = Mother::count();
        $childrenEnrolledCount = Child::count();

        $highRiskCount = TestDone::where('Haemoglobin', '<', 11.0)
            ->orWhere('urine_sugar_level', '!=', 'Negative')
            ->orWhere('blood_sugar', 'like', '%high%')
            ->distinct('mother_id')
            ->count('mother_id');

        $immunizationsDueCount = Immunization::count();
        $triposhaPacketsCount = TriposhaBook::sum('no_of_packets');

        $clinicAreas = Area::withCount(['midwives'])->get()->map(function ($area) {
            $midwifeIds = Midwife::where('area_id', $area->area_id)->pluck('midwife_id');
            $patientCount = Mother::whereIn('midwife_id', $midwifeIds)->count() + Child::whereIn('midwife_id', $midwifeIds)->count();
            
            $area->patient_count = $patientCount > 0 ? $patientCount : '-';
            $area->staff_on_duty = $area->midwives_count > 0 ? $area->midwives_count : '-';
            $area->active_alerts = '-';
            return $area;
        });

        $attendancesQuery = Attendance::with(['mother', 'child', 'midwife']);
        
        if ($user->role === 'mother') {
            if ($user->mother) {
                $attendancesQuery->where('mother_id', $user->mother->mother_id);
            } else {
                // If no mother profile, return empty
                $attendancesQuery->where('mother_id', -1);
            }
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $attendancesQuery->where('midwife_id', $user->midwife->midwife_id);
        }
        
        $recentAttendances = $attendancesQuery->latest()->take(5)->get();

        // Get mothers with high blood pressure from family health history
        $highBPMothers = \App\Models\FamilyHealthHistory::with(['mother.user', 'mother.midwife.area'])
            ->whereNotNull('high_blood_pressure')
            ->where('high_blood_pressure', '!=', 'no')
            ->where('high_blood_pressure', '!=', 'No')
            ->where('high_blood_pressure', '!=', 'false')
            ->where('high_blood_pressure', '!=', '0')
            ->where('high_blood_pressure', '!=', '')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'registeredMothersCount',
            'childrenEnrolledCount',
            'highRiskCount',
            'immunizationsDueCount',
            'triposhaPacketsCount',
            'clinicAreas',
            'recentAttendances',
            'highBPMothers'
        ));
    }
}

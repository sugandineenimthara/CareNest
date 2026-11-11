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

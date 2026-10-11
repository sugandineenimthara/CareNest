<?php

namespace App\Http\Controllers;

use App\Models\FamilyHealthHistory;
use App\Models\TestDone;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. High Blood Pressure Alerts (from Family Health History)
        $highBPQuery = FamilyHealthHistory::with(['mother.user', 'mother.midwife.area'])
            ->whereNotNull('high_blood_pressure')
            ->whereNotIn('high_blood_pressure', ['no', 'No', 'false', '0', '']);

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $highBPQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $highBPQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        $highBPMothers = $highBPQuery->get()
            ->map(function ($history) {
                return (object)[
                    'type' => 'High Blood Pressure',
                    'severity' => 'danger',
                    'icon' => 'fa-heart-pulse',
                    'mother_name' => $history->mother->user->name ?? 'Unknown',
                    'contact_no' => $history->mother->telephone ?? '-',
                    'condition_text' => $history->high_blood_pressure,
                    'area' => $history->mother->midwife->area->area_name ?? '-',
                    'midwife' => $history->mother->midwife->user->name ?? '-',
                    'created_at' => $history->created_at
                ];
            });

        // 2. Low Haemoglobin Alerts (from Tests Done)
        $lowHbQuery = TestDone::with(['mother.user', 'mother.midwife.area'])
            ->where('Haemoglobin', '<', 11.0);

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $lowHbQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $lowHbQuery->whereHas('mother', fn($q) => $q->where('midwife_id', $user->midwife->midwife_id));
        }

        $lowHbMothers = $lowHbQuery->get()
            ->map(function ($test) {
                return (object)[
                    'type' => 'Low Haemoglobin',
                    'severity' => 'warning',
                    'icon' => 'fa-droplet',
                    'mother_name' => $test->mother->user->name ?? 'Unknown',
                    'contact_no' => $test->mother->telephone ?? '-',
                    'condition_text' => 'Hb Level: ' . $test->Haemoglobin,
                    'area' => $test->mother->midwife->area->area_name ?? '-',
                    'midwife' => $test->mother->midwife->user->name ?? '-',
                    'created_at' => $test->created_at
                ];
            });

        // 3. High Blood Sugar Alerts (from Tests Done)
        $highSugarQuery = TestDone::with(['mother.user', 'mother.midwife.area'])
            ->where(function($q) {
                $q->where('urine_sugar_level', '!=', 'Negative')
                  ->orWhere('blood_sugar', 'like', '%high%');
            });

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $highSugarQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $highSugarQuery->whereHas('mother', fn($q) => $q->where('midwife_id', $user->midwife->midwife_id));
        }

        $highSugarMothers = $highSugarQuery->get()
            ->map(function ($test) {
                return (object)[
                    'type' => 'High Blood/Urine Sugar',
                    'severity' => 'danger',
                    'icon' => 'fa-cubes-stacked',
                    'mother_name' => $test->mother->user->name ?? 'Unknown',
                    'contact_no' => $test->mother->telephone ?? '-',
                    'condition_text' => 'Sugar: ' . ($test->blood_sugar ?? $test->urine_sugar_level),
                    'area' => $test->mother->midwife->area->area_name ?? '-',
                    'midwife' => $test->mother->midwife->user->name ?? '-',
                    'created_at' => $test->created_at
                ];
            });

        // Merge all alerts and sort by newest
        $allAlerts = collect()
            ->merge($highBPMothers)
            ->merge($lowHbMothers)
            ->merge($highSugarMothers)
            ->sortByDesc('created_at');

        return view('alerts.index', compact('allAlerts'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\FamilyHealthHistory;
use App\Models\Mother;
use App\Models\TestDone;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'mother') {
            if (!$user->mother) {
                abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            }

            $mother = $user->mother;
            $mother->load(['children.immunizations', 'immunizations', 'pregnancyHistories']);

            $notifications = collect();

            $receivedDosesCount = $mother->tetanusDosesCount();

            $nextDoseNum = $receivedDosesCount + 1;
            if (!$mother->isTetanusSafe() && $nextDoseNum <= 4) {
                if ($receivedDosesCount === 0) {
                    $lmp = $mother->pregnancyHistories->last()?->last_menstrual_period;
                    $mDueDate = $lmp ? Carbon::parse($lmp)->addWeeks(12) : Carbon::parse($mother->created_at)->addWeeks(2);
                } elseif ($receivedDosesCount === 1) {
                    $firstDose = $mother->immunizations->sortBy('immunization_date')->first();
                    $mDueDate = $firstDose ? Carbon::parse($firstDose->immunization_date)->addWeeks(6) : now()->addDays(7);
                } else {
                    $lastDose = $mother->immunizations->sortByDesc('immunization_date')->first();
                    $mDueDate = $lastDose ? Carbon::parse($lastDose->immunization_date)->addMonths(6) : now()->addDays(7);
                }

                $diffDays = now()->startOfDay()->diffInDays($mDueDate->copy()->startOfDay(), false);
                $isWithinWeek = $diffDays >= 0 && $diffDays <= 7;

                $notifications->push((object)[
                    'type' => 'Maternal Immunization',
                    'title' => 'Upcoming Immunization: Tetanus Toxoid (TT) Dose ' . $nextDoseNum,
                    'beneficiary' => $mother->mother_name . ' (Mother)',
                    'vaccine_name' => 'Tetanus Toxoid (TT) Dose ' . $nextDoseNum,
                    'date' => $mDueDate->format('d M Y'),
                    'time' => '09:00 AM',
                    'venue' => 'Ja-Ela District Hospital',
                    'is_within_week' => $isWithinWeek,
                    'status_label' => $isWithinWeek ? 'Upcoming within 1 week' : ($mDueDate->isPast() ? 'Due Now' : 'Upcoming'),
                    'severity' => $isWithinWeek ? 'warning' : 'info',
                    'icon' => 'fa-syringe',
                    'message' => "Upcoming immunization for {$mother->mother_name}: Tetanus Toxoid Dose {$nextDoseNum} is scheduled on {$mDueDate->format('d M Y')} at 09:00 AM. Venue: Ja-Ela District Hospital.",
                    'created_at' => $mDueDate->copy()->subDays(7),
                ]);
            }

            $standardSchedule = \App\Models\Child::getVaccinationSchedule();
            foreach ($mother->children as $child) {
                $dob = Carbon::parse($child->date_of_birth);
                foreach ($standardSchedule as $vac) {
                    $recorded = $child->immunizations->first(function ($imm) use ($vac) {
                        $prefix = explode(' ', $vac['vaccine_name'])[0];
                        return stripos($imm->vaccine_name, $prefix) !== false;
                    });

                    if (!$recorded) {
                        $cDueDate = $dob->copy()->addMonths($vac['months_due']);
                        $diffDays = now()->startOfDay()->diffInDays($cDueDate->copy()->startOfDay(), false);
                        $isWithinWeek = $diffDays >= 0 && $diffDays <= 7;

                        $notifications->push((object)[
                            'type' => 'Child Immunization',
                            'title' => 'Upcoming Immunization: ' . $vac['vaccine_name'],
                            'beneficiary' => $child->display_name . ' (Child)',
                            'vaccine_name' => $vac['vaccine_name'],
                            'date' => $cDueDate->format('d M Y'),
                            'time' => '09:00 AM',
                            'venue' => 'Ja-Ela District Hospital',
                            'is_within_week' => $isWithinWeek,
                            'status_label' => $isWithinWeek ? 'Upcoming within 1 week' : ($cDueDate->isPast() ? 'Due Now' : 'Upcoming'),
                            'severity' => $isWithinWeek ? 'warning' : 'info',
                            'icon' => 'fa-syringe',
                            'message' => "Upcoming immunization for {$child->display_name}: {$vac['vaccine_name']} is scheduled on {$cDueDate->format('d M Y')} at 09:00 AM. Venue: Ja-Ela District Hospital.",
                            'created_at' => $cDueDate->copy()->subDays(7),
                        ]);
                    }
                }
            }

            if ($mother->isHighBloodPressure()) {
                $notifications->push((object)[
                    'type' => 'Health Advisory',
                    'title' => 'Elevated Blood Pressure Notice',
                    'beneficiary' => $mother->mother_name,
                    'vaccine_name' => 'Clinical Monitoring',
                    'date' => now()->format('d M Y'),
                    'time' => '09:00 AM',
                    'venue' => 'Ja-Ela District Hospital',
                    'is_within_week' => false,
                    'status_label' => 'Medical Attention',
                    'severity' => 'danger',
                    'icon' => 'fa-heart-pulse',
                    'message' => "Recorded Blood Pressure ({$mother->blood_pressure} mmHg) is elevated. Please visit Ja-Ela District Hospital or contact your area midwife.",
                    'created_at' => $mother->updated_at ?? now(),
                ]);
            }

            $motherNotifications = $notifications->sortBy(function ($item) {
                return $item->is_within_week ? 0 : 1;
            });

            return view('alerts.index', compact('motherNotifications', 'user'));
        }

        $mothersQuery = Mother::with(['user', 'midwife.area'])
            ->whereNotNull('blood_pressure')
            ->where('blood_pressure', '!=', '');

        if ($user->role === 'midwife' && $user->midwife) {
            $mothersQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        $clinicalHighBPMothers = $mothersQuery->get()
            ->filter(fn($mother) => $mother->isHighBloodPressure())
            ->map(function ($mother) {
                $bp = trim($mother->blood_pressure);
                $measure = str_contains(strtolower($bp), 'mmhg') ? $bp : $bp . ' mmHg';
                return (object)[
                    'type' => 'High Risk Blood Pressure (> 140/90)',
                    'severity' => 'danger',
                    'icon' => 'fa-heart-pulse',
                    'mother_name' => $mother->mother_name ?? 'Unknown',
                    'contact_no' => $mother->phone_no ?? '-',
                    'condition_text' => $measure,
                    'area' => $mother->midwife->area->area_name ?? '-',
                    'midwife' => $mother->midwife->midwife_name ?? '-',
                    'created_at' => $mother->updated_at ?? $mother->created_at,
                ];
            });

        $attendancesQuery = \App\Models\Attendance::with(['mother.user', 'mother.midwife.area'])
            ->where('clinic_type', 'mother')
            ->where(function ($q) {
                $q->whereNotNull('blood_pressure')->where('blood_pressure', '!=', '')
                  ->orWhere(function ($sq) {
                      $sq->whereNotNull('remarks')->where('remarks', '!=', '');
                  });
            });

        if ($user->role === 'midwife' && $user->midwife) {
            $attendancesQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        $attendanceHighBPMothers = $attendancesQuery->get()
            ->filter(fn($att) => $att->isHighBloodPressure() && $att->mother)
            ->map(function ($att) {
                $mother = $att->mother;
                $rawBp = trim($att->blood_pressure ?: $att->remarks);
                $measure = str_contains(strtolower($rawBp), 'mmhg') ? $rawBp : $rawBp . ' mmHg';
                return (object)[
                    'type' => 'High Risk Blood Pressure (> 140/90)',
                    'severity' => 'danger',
                    'icon' => 'fa-heart-pulse',
                    'mother_name' => $mother->mother_name ?? 'Unknown',
                    'contact_no' => $mother->phone_no ?? '-',
                    'condition_text' => $measure,
                    'area' => $mother->midwife->area->area_name ?? '-',
                    'midwife' => $mother->midwife->midwife_name ?? '-',
                    'created_at' => $att->created_at,
                ];
            });

        $clinicalAlerts = $clinicalHighBPMothers->values();
        $seenMothers = $clinicalAlerts->pluck('mother_name')->all();
        foreach ($attendanceHighBPMothers as $attAlert) {
            if (!in_array($attAlert->mother_name, $seenMothers, true)) {
                $clinicalAlerts->push($attAlert);
                $seenMothers[] = $attAlert->mother_name;
            }
        }

        // 2. High BP from family health history (exclude negative/normal answers such as 'None', 'no', 'nil')
        $highBPQuery = FamilyHealthHistory::with(['mother.user', 'mother.midwife.area'])
            ->whereNotNull('high_blood_pressure')
            ->whereNotIn('high_blood_pressure', ['no', 'No', 'NO', 'false', '0', '', 'none', 'None', 'NONE', 'nil', 'Nil', 'normal', 'Normal', 'N/A', 'na']);

        if ($user->role === 'mother') {
            if (!$user->mother) abort(403, 'Your mother profile is not linked or incomplete. Please contact the administrator.');
            $highBPQuery->where('mother_id', $user->mother->mother_id);
        } elseif ($user->role === 'midwife' && $user->midwife) {
            $highBPQuery->where('midwife_id', $user->midwife->midwife_id);
        }

        $highBPMothers = $highBPQuery->get()
            ->map(function ($history) {
                return (object)[
                    'type' => 'High Blood Pressure (History)',
                    'severity' => 'danger',
                    'icon' => 'fa-heart-pulse',
                    'mother_name' => $history->mother->mother_name ?? 'Unknown',
                    'contact_no' => $history->mother->phone_no ?? '-',
                    'condition_text' => $history->high_blood_pressure,
                    'area' => $history->mother->midwife->area->area_name ?? '-',
                    'midwife' => $history->mother->midwife->midwife_name ?? '-',
                    'created_at' => $history->created_at
                ];
            });

        // 3. Low Haemoglobin Alerts (< 11.0 g/dL)
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
                    'mother_name' => $test->mother->mother_name ?? 'Unknown',
                    'contact_no' => $test->mother->phone_no ?? '-',
                    'condition_text' => 'Hb Level: ' . $test->Haemoglobin,
                    'area' => $test->mother->midwife->area->area_name ?? '-',
                    'midwife' => $test->mother->midwife->midwife_name ?? '-',
                    'created_at' => $test->created_at
                ];
            });

        // 4. High Blood / Urine Sugar Alerts
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
                    'mother_name' => $test->mother->mother_name ?? 'Unknown',
                    'contact_no' => $test->mother->phone_no ?? '-',
                    'condition_text' => 'Sugar: ' . ($test->blood_sugar ?? $test->urine_sugar_level),
                    'area' => $test->mother->midwife->area->area_name ?? '-',
                    'midwife' => $test->mother->midwife->midwife_name ?? '-',
                    'created_at' => $test->created_at
                ];
            });

        // Merge all alerts
        $allAlerts = collect()
            ->merge($clinicalAlerts)
            ->merge($highBPMothers)
            ->merge($lowHbMothers)
            ->merge($highSugarMothers)
            ->sortByDesc('created_at');

        return view('alerts.index', compact('allAlerts'));
    }
}

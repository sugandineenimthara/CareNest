<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    

    public function index(Request $request)
    {
        $user       = Auth::user();
        $search     = $request->query('search');
        $clinicType = $request->query('type');
        $dateFrom   = $request->query('date_from');
        $dateTo     = $request->query('date_to');

        $query = Attendance::with([
            'mother.midwife.area',
            'child.mother',
            'child.midwife.area',
            'midwife.area',
        ]);

        if ($clinicType && in_array($clinicType, ['mother', 'pediatric'])) {
            $query->where('clinic_type', $clinicType);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('clinic_name', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhere('blood_pressure', 'like', "%{$search}%")
                  ->orWhereHas('mother', function ($mq) use ($search) {
                      $mq->where('mother_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('child', function ($cq) use ($search) {
                      $cq->where('child_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom) {
            $query->whereDate('clinic_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('clinic_date', '<=', $dateTo);
        }

        $allRecords = $query->orderBy('clinic_date', 'desc')
                            ->orderBy('clinic_name')
                            ->orderBy('id', 'desc')
                            ->get();

        
        $clinicSessions = $allRecords->groupBy(function ($r) {
            return $r->clinic_date->format('Y-m-d') . '||' . $r->clinic_type . '||' . $r->clinic_name;
        })->map(function ($records, $key) {
            [$date, $type, $name] = explode('||', $key);
            $first = $records->first();

            return [
                'clinic_date'      => Carbon::parse($date),
                'clinic_type'      => $type, 
                'clinic_name'      => $name,
                'midwife'          => $first?->midwife,
                'total_attendance' => $records->count(), 
                'records'          => $records,
            ];
        })->values();

        
        $totalAttendancesEver      = Attendance::count();
        $totalMotherAttendances    = Attendance::where('clinic_type', 'mother')->count();
        $totalPediatricAttendances = Attendance::where('clinic_type', 'pediatric')->count();
        $totalClinicsEver          = Attendance::select('clinic_date', 'clinic_name', 'clinic_type')
                                              ->distinct()->count();

        
        $mothers = Mother::with('midwife.area')->orderBy('mother_name')->get();
        $children = Child::with('mother', 'midwife.area')->orderBy('child_id')->get();
        $midwives = Midwife::with('area')->get();

        $mothersList = $mothers->map(function ($m) {
            return [
                'id'       => $m->mother_id,
                'name'     => $m->mother_name,
                'nic'      => $m->nic ?? 'N/A',
                'area'     => $m->midwife?->area?->area_name ?? 'N/A',
                'phone'    => $m->mobile_number ?? $m->phone_number ?? 'N/A',
            ];
        })->values();

        $childrenList = $children->map(function ($c) {
            return [
                'id'          => $c->child_id,
                'name'        => $c->display_name,
                'mother_name' => $c->mother?->mother_name ?? 'N/A',
                'area'        => $c->midwife?->area?->area_name ?? 'N/A',
                'dob'         => $c->date_of_birth ? $c->date_of_birth->format('d M Y') : 'N/A',
            ];
        })->values();

        return view('attendances.index', compact(
            'user', 'clinicSessions', 'search', 'clinicType', 'dateFrom', 'dateTo',
            'totalAttendancesEver', 'totalMotherAttendances', 'totalPediatricAttendances', 'totalClinicsEver',
            'mothersList', 'childrenList', 'midwives'
        ));
    }

    

    public function storeBatch(Request $request)
    {
        $request->validate([
            'clinic_date'                => 'required|date',
            'clinic_type'                => 'required|in:mother,pediatric',
            'clinic_name'                => 'required|string|max:255',
            'midwife_id'                 => 'nullable|exists:midwives,midwife_id',
            'attendees'                  => 'required|array|min:1',
            'attendees.*.mother_id'      => 'nullable|exists:mothers,mother_id',
            'attendees.*.child_id'       => 'nullable|exists:children,child_id',
            'attendees.*.blood_pressure' => 'nullable|string|max:50',
            'attendees.*.height'         => 'nullable|numeric|min:20|max:250',
            'attendees.*.weight'         => 'nullable|numeric|min:0.5|max:150',
            'attendees.*.remarks'        => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $midwifeId = $request->midwife_id;

        
        if (!$midwifeId) {
            if ($user->role === 'midwife') {
                $mw = Midwife::where('user_id', $user->id)->first();
                $midwifeId = $mw?->midwife_id;
            }
            if (!$midwifeId) {
                $firstMw = Midwife::first();
                $midwifeId = $firstMw?->midwife_id;
            }
        }

        DB::beginTransaction();
        try {
            $count = 0;
            $highRiskAlerts = [];

            foreach ($request->attendees as $item) {
                $motherId = null;
                $childId  = null;

                if ($request->clinic_type === 'mother') {
                    if (empty($item['mother_id'])) continue;
                    $motherId = $item['mother_id'];
                    $bpRate = trim($item['blood_pressure'] ?? ($item['remarks'] ?? ''));

                    
                    Attendance::create([
                        'mother_id'      => $motherId,
                        'child_id'       => null,
                        'midwife_id'     => $midwifeId,
                        'clinic_name'    => $request->clinic_name,
                        'clinic_type'    => 'mother',
                        'clinic_date'    => $request->clinic_date,
                        'blood_pressure' => $bpRate ?: null,
                        'remarks'        => $bpRate ?: null,
                    ]);

                    
                    if (!empty($bpRate)) {
                        $mother = Mother::find($motherId);
                        if ($mother) {
                            $mother->update(['blood_pressure' => $bpRate]);

                            
                            if (preg_match('/(\d+)\s*[\/\-]\s*(\d+)/', $bpRate, $m)) {
                                $systolic = (int)$m[1];
                                $diastolic = (int)$m[2];
                                if ($systolic >= 140 || $diastolic >= 90) {
                                    $highRiskAlerts[] = $mother->mother_name . " ({$bpRate} mmHg)";
                                }
                            }
                        }
                    }

                    $count++;
                } else {
                    if (empty($item['child_id'])) continue;
                    $childId = $item['child_id'];
                    $child = Child::find($childId);
                    $motherId = $child?->mother_id;

                    $height = !empty($item['height']) ? (float)$item['height'] : null;
                    $weight = !empty($item['weight']) ? (float)$item['weight'] : null;
                    $bmi = null;

                    if ($height && $weight && $height > 0) {
                        $heightInM = $height > 3 ? ($height / 100) : $height;
                        $bmi = round($weight / ($heightInM * $heightInM), 2);
                    }

                    
                    Attendance::create([
                        'mother_id'      => $motherId,
                        'child_id'       => $childId,
                        'midwife_id'     => $midwifeId,
                        'clinic_name'    => $request->clinic_name,
                        'clinic_type'    => 'pediatric',
                        'clinic_date'    => $request->clinic_date,
                        'height'         => $height,
                        'weight'         => $weight,
                        'bmi'            => $bmi,
                        'remarks'        => ($height && $weight) ? "Ht: {$height}cm, Wt: {$weight}kg, BMI: {$bmi}" : ($item['remarks'] ?? null),
                    ]);

                    
                    if ($child) {
                        $childUpdates = [];
                        if ($height !== null) $childUpdates['height'] = $height;
                        if ($weight !== null) $childUpdates['weight'] = $weight;
                        if ($bmi !== null)    $childUpdates['bmi'] = $bmi;
                        if (!empty($childUpdates)) {
                            $child->update($childUpdates);
                        }
                    }

                    $count++;
                }
            }

            if ($count === 0) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Please select at least one valid attendee.');
            }

            DB::commit();

            $typeLabel = $request->clinic_type === 'mother' ? 'Mother Clinic' : 'Pediatric Clinic';
            $dateLabel = Carbon::parse($request->clinic_date)->format('d M Y');

            $routePrefix = ($user->role === 'midwife') ? 'midwife.' : 'admin.';

            if (!empty($highRiskAlerts)) {
                session()->flash('high_risk_alert', '🚨 HIGH RISK ALERT: Mother(s) ' . implode(', ', $highRiskAlerts) . ' recorded blood pressure exceeding 140/90 mmHg! High risk alert flagged in the system and profile updated.');
            }

            return redirect()->route($routePrefix . 'attendances.index')
                ->with('success', "✅ Clinic attendance recorded! {$count} attendees added for {$request->clinic_name} ({$typeLabel}) on {$dateLabel}. Profile metrics successfully updated.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    

    public function destroy(int $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }
}

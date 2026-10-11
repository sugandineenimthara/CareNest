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
    /**
     * Display attendance records grouped by clinic sessions with summary counters.
     */
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
                  ->orWhereHas('mother', function ($mq) use ($search) {
                      $mq->where('mother_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('child', function ($cq) use ($search) {
                      $cq->where('child_name', 'like', "%{$search}%")
                         ->orWhere('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
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

        // Group attendances into clinic sessions: date + clinic_type + clinic_name
        $clinicSessions = $allRecords->groupBy(function ($r) {
            return $r->clinic_date->format('Y-m-d') . '||' . $r->clinic_type . '||' . $r->clinic_name;
        })->map(function ($records, $key) {
            [$date, $type, $name] = explode('||', $key);
            $first = $records->first();

            return [
                'clinic_date'      => Carbon::parse($date),
                'clinic_type'      => $type, // 'mother' or 'pediatric'
                'clinic_name'      => $name,
                'midwife'          => $first?->midwife,
                'total_attendance' => $records->count(), // Count of total attendances for this clinic
                'records'          => $records,
            ];
        })->values();

        // Overall summary statistics
        $totalAttendancesEver      = Attendance::count();
        $totalMotherAttendances    = Attendance::where('clinic_type', 'mother')->count();
        $totalPediatricAttendances = Attendance::where('clinic_type', 'pediatric')->count();
        $totalClinicsEver          = Attendance::select('clinic_date', 'clinic_name', 'clinic_type')
                                              ->distinct()->count();

        // Data for interactive recording form
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

    /**
     * Store batch attendance for a clinic session.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'clinic_date'         => 'required|date',
            'clinic_type'         => 'required|in:mother,pediatric',
            'clinic_name'         => 'required|string|max:255',
            'midwife_id'          => 'nullable|exists:midwives,midwife_id',
            'attendees'           => 'required|array|min:1',
            'attendees.*.mother_id' => 'nullable|exists:mothers,mother_id',
            'attendees.*.child_id'  => 'nullable|exists:children,child_id',
            'attendees.*.remarks'   => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $midwifeId = $request->midwife_id;

        // Auto-assign midwife if logged in as midwife
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
            foreach ($request->attendees as $item) {
                $motherId = null;
                $childId  = null;

                if ($request->clinic_type === 'mother') {
                    if (empty($item['mother_id'])) continue;
                    $motherId = $item['mother_id'];
                } else {
                    if (empty($item['child_id'])) continue;
                    $childId = $item['child_id'];
                    $child = Child::find($childId);
                    $motherId = $child?->mother_id;
                }

                Attendance::create([
                    'mother_id'   => $motherId,
                    'child_id'    => $childId,
                    'midwife_id'  => $midwifeId,
                    'clinic_name' => $request->clinic_name,
                    'clinic_type' => $request->clinic_type,
                    'clinic_date' => $request->clinic_date,
                    'remarks'     => $item['remarks'] ?? null,
                ]);

                $count++;
            }

            if ($count === 0) {
                DB::rollBack();
                return back()->withInput()->with('error', 'Please select at least one valid attendee.');
            }

            DB::commit();

            $typeLabel = $request->clinic_type === 'mother' ? 'Mother Clinic' : 'Pediatric Clinic';
            $dateLabel = Carbon::parse($request->clinic_date)->format('d M Y');

            return redirect()->route('attendances.index')
                ->with('success', "✅ Clinic attendance recorded! {$count} attendees added for {$request->clinic_name} ({$typeLabel}) on {$dateLabel}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    /**
     * Delete an individual attendance record.
     */
    public function destroy(int $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }
}

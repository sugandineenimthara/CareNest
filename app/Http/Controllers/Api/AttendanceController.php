<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Attendance::with(['mother', 'child', 'midwife'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_id'   => 'required|exists:mothers,mother_id',
            'child_id'    => 'nullable|exists:children,child_id',
            'midwife_id'  => 'required|exists:midwives,midwife_id',
            'clinic_name' => 'required|string|max:255',
            'clinic_type' => 'nullable|in:mother,pediatric',
            'clinic_date' => 'required|date',
        ]);

        $clinicDate = Carbon::parse($validated['clinic_date'])->toDateString();
        $clinicType = $validated['clinic_type'] ?? (!empty($validated['child_id']) ? 'pediatric' : 'mother');
        $validated['clinic_type'] = $clinicType;

        // One clinic of this type per day
        $existingClinic = Attendance::whereDate('clinic_date', $clinicDate)
            ->where('clinic_type', $clinicType)
            ->first();

        if ($existingClinic && trim(strtolower($existingClinic->clinic_name)) !== trim(strtolower($validated['clinic_name']))) {
            return response()->json([
                'message' => "Only one {$clinicType} clinic is allowed per day. A {$clinicType} clinic already exists on this date.",
            ], 422);
        }

        // Single attendance per participant per clinic
        if ($clinicType === 'mother') {
            $already = Attendance::whereDate('clinic_date', $clinicDate)
                ->where('clinic_type', 'mother')
                ->where('mother_id', $validated['mother_id'])
                ->exists();
            if ($already) {
                return response()->json([
                    'message' => 'This mother has already attended the maternal clinic on this date.',
                ], 422);
            }
        } else {
            $already = Attendance::whereDate('clinic_date', $clinicDate)
                ->where('clinic_type', 'pediatric')
                ->where('child_id', $validated['child_id'])
                ->exists();
            if ($already) {
                return response()->json([
                    'message' => 'This child has already attended the pediatric clinic on this date.',
                ], 422);
            }
        }

        $attendance = Attendance::create($validated);
        return response()->json($attendance->load(['mother', 'child', 'midwife']), 201);
    }

    public function show(int $id): JsonResponse
    {
        $attendance = Attendance::with(['mother', 'child', 'midwife'])->findOrFail($id);
        return response()->json($attendance);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);
        $validated = $request->validate([
            'mother_id'   => 'sometimes|required|exists:mothers,mother_id',
            'child_id'    => 'nullable|exists:children,child_id',
            'midwife_id'  => 'sometimes|required|exists:midwives,midwife_id',
            'clinic_name' => 'sometimes|required|string|max:255',
            'clinic_type' => 'nullable|in:mother,pediatric',
            'clinic_date' => 'sometimes|required|date',
        ]);

        $clinicDate = Carbon::parse($validated['clinic_date'] ?? $attendance->clinic_date)->toDateString();
        $childId = array_key_exists('child_id', $validated) ? $validated['child_id'] : $attendance->child_id;
        $clinicType = $validated['clinic_type'] ?? ($attendance->clinic_type ?: (!empty($childId) ? 'pediatric' : 'mother'));
        $clinicName = $validated['clinic_name'] ?? $attendance->clinic_name;
        $motherId = $validated['mother_id'] ?? $attendance->mother_id;

        // One clinic of this type per day
        $existingClinic = Attendance::whereDate('clinic_date', $clinicDate)
            ->where('clinic_type', $clinicType)
            ->where('id', '!=', $id)
            ->first();

        if ($existingClinic && trim(strtolower($existingClinic->clinic_name)) !== trim(strtolower($clinicName))) {
            return response()->json([
                'message' => "Only one {$clinicType} clinic is allowed per day. A {$clinicType} clinic already exists on this date.",
            ], 422);
        }

        // Single attendance per participant per clinic
        if ($clinicType === 'mother') {
            $already = Attendance::whereDate('clinic_date', $clinicDate)
                ->where('clinic_type', 'mother')
                ->where('mother_id', $motherId)
                ->where('id', '!=', $id)
                ->exists();
            if ($already) {
                return response()->json([
                    'message' => 'This mother has already attended the maternal clinic on this date.',
                ], 422);
            }
        } else {
            $already = Attendance::whereDate('clinic_date', $clinicDate)
                ->where('clinic_type', 'pediatric')
                ->where('child_id', $childId)
                ->where('id', '!=', $id)
                ->exists();
            if ($already) {
                return response()->json([
                    'message' => 'This child has already attended the pediatric clinic on this date.',
                ], 422);
            }
        }

        $attendance->update($validated);
        return response()->json($attendance->load(['mother', 'child', 'midwife']));
    }

    public function destroy(int $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();
        return response()->json(['message' => 'Attendance record deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
            'mother_id' => 'required|exists:mothers,mother_id',
            'child_id' => 'nullable|exists:children,child_id',
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'clinic_name' => 'required|string|max:255',
            'clinic_date' => 'required|date',
        ]);

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
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'child_id' => 'nullable|exists:children,child_id',
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'clinic_name' => 'sometimes|required|string|max:255',
            'clinic_date' => 'sometimes|required|date',
        ]);

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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilyHealthHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FamilyHealthHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FamilyHealthHistory::with(['mother', 'midwife'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'midwife_id' => 'nullable|exists:midwives,midwife_id',
            'diabetes' => 'nullable|string|max:255',
            'high_blood_pressure' => 'nullable|string|max:255',
            'blood_related_diseases' => 'nullable|string|max:255',
            'other' => 'nullable|string',
        ]);

        $record = FamilyHealthHistory::create($validated);
        return response()->json($record->load(['mother', 'midwife']), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(FamilyHealthHistory::with(['mother', 'midwife'])->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $record = FamilyHealthHistory::findOrFail($id);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'midwife_id' => 'nullable|exists:midwives,midwife_id',
            'diabetes' => 'nullable|string|max:255',
            'high_blood_pressure' => 'nullable|string|max:255',
            'blood_related_diseases' => 'nullable|string|max:255',
            'other' => 'nullable|string',
        ]);

        $record->update($validated);
        return response()->json($record->load(['mother', 'midwife']));
    }

    public function destroy(int $id): JsonResponse
    {
        FamilyHealthHistory::findOrFail($id)->delete();
        return response()->json(['message' => 'Family health history deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PregnancyHistory;
use App\Services\MaternalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PregnancyHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PregnancyHistory::with('mother')->get());
    }

    public function store(Request $request, MaternalService $maternalService): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'no_living_children' => 'nullable|integer|min:0',
            'age_of_youngest_child' => 'nullable|integer|min:0',
            'last_menstrual_period' => 'nullable|date',
            'expected_date_of_delivery' => 'nullable|date',
            'date_confirmed_by_US' => 'nullable|date',
            'no_of_weeks_pregnant_at_registration' => 'nullable|integer|min:0|max:45',
            'first_fetal_movements_date' => 'nullable|date',
        ]);

        if (!empty($validated['last_menstrual_period']) && empty($validated['expected_date_of_delivery'])) {
            $validated['expected_date_of_delivery'] = $maternalService->calculateEDD($validated['last_menstrual_period']);
        }

        $history = PregnancyHistory::create($validated);
        return response()->json($history->load('mother'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(PregnancyHistory::with('mother')->findOrFail($id));
    }

    public function update(Request $request, int $id, MaternalService $maternalService): JsonResponse
    {
        $history = PregnancyHistory::findOrFail($id);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'no_living_children' => 'nullable|integer|min:0',
            'age_of_youngest_child' => 'nullable|integer|min:0',
            'last_menstrual_period' => 'nullable|date',
            'expected_date_of_delivery' => 'nullable|date',
            'date_confirmed_by_US' => 'nullable|date',
            'no_of_weeks_pregnant_at_registration' => 'nullable|integer|min:0|max:45',
            'first_fetal_movements_date' => 'nullable|date',
        ]);

        if (!empty($validated['last_menstrual_period']) && empty($validated['expected_date_of_delivery'])) {
            $validated['expected_date_of_delivery'] = $maternalService->calculateEDD($validated['last_menstrual_period']);
        }

        $history->update($validated);
        return response()->json($history->load('mother'));
    }

    public function destroy(int $id): JsonResponse
    {
        PregnancyHistory::findOrFail($id)->delete();
        return response()->json(['message' => 'Pregnancy history record deleted successfully']);
    }
}

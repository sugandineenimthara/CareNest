<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestDone;
use App\Services\MaternalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestDoneController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TestDone::with('mother')->get());
    }

    public function store(Request $request, MaternalService $maternalService): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'blood_group' => 'nullable|string|max:10',
            'blood_sugar' => 'nullable|string|max:100',
            'Haemoglobin' => 'nullable|string|max:100',
            'Albumin_test' => 'nullable|string|max:100',
            'urine_sugar_level' => 'nullable|string|max:100',
            'VDRL' => 'nullable|string|max:100',
            'HIV' => 'nullable|string|max:100',
        ]);

        $test = TestDone::create($validated);
        $riskAssessment = $maternalService->evaluateHealthRisk($validated);

        return response()->json([
            'data' => $test->load('mother'),
            'risk_assessment' => $riskAssessment,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(TestDone::with('mother')->findOrFail($id));
    }

    public function update(Request $request, int $id, MaternalService $maternalService): JsonResponse
    {
        $test = TestDone::findOrFail($id);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'blood_group' => 'nullable|string|max:10',
            'blood_sugar' => 'nullable|string|max:100',
            'Haemoglobin' => 'nullable|string|max:100',
            'Albumin_test' => 'nullable|string|max:100',
            'urine_sugar_level' => 'nullable|string|max:100',
            'VDRL' => 'nullable|string|max:100',
            'HIV' => 'nullable|string|max:100',
        ]);

        $test->update($validated);
        $riskAssessment = $maternalService->evaluateHealthRisk($test->toArray());

        return response()->json([
            'data' => $test->load('mother'),
            'risk_assessment' => $riskAssessment,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        TestDone::findOrFail($id)->delete();
        return response()->json(['message' => 'Test record deleted successfully']);
    }
}

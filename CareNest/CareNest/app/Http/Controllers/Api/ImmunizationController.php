<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Immunization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImmunizationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Immunization::with(['child', 'midwife'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,child_id',
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'batch_no' => 'required|string|max:100',
            'vaccine_name' => 'required|string|max:255',
            'dose' => 'nullable|string|max:100',
            'age' => 'nullable|string|max:100',
            'immunization_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        $immunization = Immunization::create($validated);
        return response()->json($immunization->load(['child', 'midwife']), 201);
    }

    public function show(int $id): JsonResponse
    {
        $immunization = Immunization::with(['child', 'midwife'])->findOrFail($id);
        return response()->json($immunization);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $immunization = Immunization::findOrFail($id);
        $validated = $request->validate([
            'child_id' => 'sometimes|required|exists:children,child_id',
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'batch_no' => 'sometimes|required|string|max:100',
            'vaccine_name' => 'sometimes|required|string|max:255',
            'dose' => 'nullable|string|max:100',
            'age' => 'nullable|string|max:100',
            'immunization_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
        ]);

        $immunization->update($validated);
        return response()->json($immunization->load(['child', 'midwife']));
    }

    public function destroy(int $id): JsonResponse
    {
        $immunization = Immunization::findOrFail($id);
        $immunization->delete();
        return response()->json(['message' => 'Immunization deleted successfully']);
    }
}

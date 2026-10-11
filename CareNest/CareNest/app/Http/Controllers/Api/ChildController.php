<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChildController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Child::with(['mother', 'midwife'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'date_of_birth' => 'required|date',
            'birth_weight' => 'nullable|numeric|min:0.5|max:10',
            'health_details' => 'nullable|string',
        ]);

        $child = Child::create($validated);
        return response()->json($child->load(['mother', 'midwife']), 201);
    }

    public function show(int $id): JsonResponse
    {
        $child = Child::with(['mother', 'midwife', 'immunizations', 'attendances', 'triposhaBooks'])->findOrFail($id);
        return response()->json($child);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $child = Child::findOrFail($id);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'date_of_birth' => 'sometimes|required|date',
            'birth_weight' => 'nullable|numeric|min:0.5|max:10',
            'health_details' => 'nullable|string',
        ]);

        $child->update($validated);
        return response()->json($child->load(['mother', 'midwife']));
    }

    public function destroy(int $id): JsonResponse
    {
        $child = Child::findOrFail($id);
        $child->delete();
        return response()->json(['message' => 'Child deleted successfully']);
    }
}

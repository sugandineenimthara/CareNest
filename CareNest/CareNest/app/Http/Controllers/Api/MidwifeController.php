<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Midwife;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidwifeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Midwife::with('area')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'midwife_name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,area_id',
        ]);

        $midwife = Midwife::create($validated);
        return response()->json($midwife->load('area'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $midwife = Midwife::with(['area', 'mothers', 'children'])->findOrFail($id);
        return response()->json($midwife);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $midwife = Midwife::findOrFail($id);
        $validated = $request->validate([
            'midwife_name' => 'sometimes|required|string|max:255',
            'area_id' => 'sometimes|required|exists:areas,area_id',
        ]);

        $midwife->update($validated);
        return response()->json($midwife->load('area'));
    }

    public function destroy(int $id): JsonResponse
    {
        $midwife = Midwife::findOrFail($id);
        $midwife->delete();
        return response()->json(['message' => 'Midwife deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Area::with('midwives')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_name' => 'required|string|max:255',
        ]);

        $area = Area::create($validated);
        return response()->json($area, 201);
    }

    public function show(int $id): JsonResponse
    {
        $area = Area::with(['midwives', 'dailyClinicSummaries'])->findOrFail($id);
        return response()->json($area);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $validated = $request->validate([
            'area_name' => 'sometimes|required|string|max:255',
        ]);

        $area->update($validated);
        return response()->json($area);
    }

    public function destroy(int $id): JsonResponse
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json(['message' => 'Area deleted successfully']);
    }
}

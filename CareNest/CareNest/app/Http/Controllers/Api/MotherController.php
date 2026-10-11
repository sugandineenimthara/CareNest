<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mother;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MotherController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Mother::with(['midwife.area', 'children'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_name' => 'required|string|max:255',
            'phone_no' => 'nullable|string|max:20',
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'height' => 'nullable|numeric|min:30|max:250',
            'weight' => 'nullable|numeric|min:20|max:300',
            'husband_name' => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $mother = Mother::create($validated);
        return response()->json($mother->load('midwife'), 201);
    }

    public function show(int $id): JsonResponse
    {
        $mother = Mother::with([
            'midwife.area',
            'children',
            'attendances',
            'pregnancyHistories',
            'previousPregnancyHistories',
            'familyHealthHistory',
            'testsDone',
            'triposhaBooks',
        ])->findOrFail($id);

        return response()->json($mother);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $mother = Mother::findOrFail($id);
        $validated = $request->validate([
            'mother_name' => 'sometimes|required|string|max:255',
            'phone_no' => 'nullable|string|max:20',
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'height' => 'nullable|numeric|min:30|max:250',
            'weight' => 'nullable|numeric|min:20|max:300',
            'husband_name' => 'nullable|string|max:255',
            'husband_occupation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
        ]);

        $mother->update($validated);
        return response()->json($mother->load('midwife'));
    }

    public function destroy(int $id): JsonResponse
    {
        $mother = Mother::findOrFail($id);
        $mother->delete();
        return response()->json(['message' => 'Mother deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreviousPregnancyHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviousPregnancyHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(PreviousPregnancyHistory::with('mother')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'date_of_birth' => 'nullable|date',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'gender' => 'nullable|in:Male,Female',
            'which_pregnancy' => 'nullable|string|max:100',
            'results' => 'nullable|string|max:255',
            'place_of_pregnancy' => 'nullable|string|max:255',
            'rubella_vaccinated' => 'nullable|boolean',
            'folic_acid_vaccinated' => 'nullable|boolean',
            'infertility' => 'nullable|boolean',
            'blood_relation_marriage' => 'nullable|boolean',
        ]);

        $record = PreviousPregnancyHistory::create($validated);
        return response()->json($record->load('mother'), 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(PreviousPregnancyHistory::with('mother')->findOrFail($id));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $record = PreviousPregnancyHistory::findOrFail($id);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'date_of_birth' => 'nullable|date',
            'birth_weight' => 'nullable|numeric|min:0.1|max:10',
            'gender' => 'nullable|in:Male,Female',
            'which_pregnancy' => 'nullable|string|max:100',
            'results' => 'nullable|string|max:255',
            'place_of_pregnancy' => 'nullable|string|max:255',
            'rubella_vaccinated' => 'nullable|boolean',
            'folic_acid_vaccinated' => 'nullable|boolean',
            'infertility' => 'nullable|boolean',
            'blood_relation_marriage' => 'nullable|boolean',
        ]);

        $record->update($validated);
        return response()->json($record->load('mother'));
    }

    public function destroy(int $id): JsonResponse
    {
        PreviousPregnancyHistory::findOrFail($id)->delete();
        return response()->json(['message' => 'Previous pregnancy record deleted successfully']);
    }
}

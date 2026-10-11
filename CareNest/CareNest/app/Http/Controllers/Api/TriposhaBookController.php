<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TriposhaBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TriposhaBookController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TriposhaBook::with(['mother', 'child', 'midwife'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mother_id' => 'required|exists:mothers,mother_id',
            'child_id' => 'nullable|exists:children,child_id',
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'no_of_packets' => 'required|integer|min:1|max:10',
            'issuing_date' => 'required|date',
            'recipient_type' => 'required|in:Mother,Child',
            'name_of_mother' => 'nullable|string|max:255',
            'name_of_child' => 'nullable|string|max:255',
        ]);

        $book = TriposhaBook::create($validated);
        return response()->json($book->load(['mother', 'child', 'midwife']), 201);
    }

    public function show(int $serialNo): JsonResponse
    {
        return response()->json(TriposhaBook::with(['mother', 'child', 'midwife'])->findOrFail($serialNo));
    }

    public function update(Request $request, int $serialNo): JsonResponse
    {
        $book = TriposhaBook::findOrFail($serialNo);
        $validated = $request->validate([
            'mother_id' => 'sometimes|required|exists:mothers,mother_id',
            'child_id' => 'nullable|exists:children,child_id',
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'no_of_packets' => 'sometimes|required|integer|min:1|max:10',
            'issuing_date' => 'sometimes|required|date',
            'recipient_type' => 'sometimes|required|in:Mother,Child',
            'name_of_mother' => 'nullable|string|max:255',
            'name_of_child' => 'nullable|string|max:255',
        ]);

        $book->update($validated);
        return response()->json($book->load(['mother', 'child', 'midwife']));
    }

    public function destroy(int $serialNo): JsonResponse
    {
        TriposhaBook::findOrFail($serialNo)->delete();
        return response()->json(['message' => 'Triposha book record deleted successfully']);
    }
}

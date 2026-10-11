<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyClinicSummary;
use Illuminate\Http\JsonResponse;

class DailyClinicSummaryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(DailyClinicSummary::with(['report', 'midwife', 'area'])->get());
    }

    public function show(int $reportId): JsonResponse
    {
        return response()->json(DailyClinicSummary::with(['report', 'midwife', 'area'])->findOrFail($reportId));
    }
}

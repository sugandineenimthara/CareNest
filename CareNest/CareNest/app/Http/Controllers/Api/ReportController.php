<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ClinicReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Report::with(['midwife', 'dailyClinicSummary'])->get());
    }

    public function store(Request $request, ClinicReportService $reportService): JsonResponse
    {
        $validated = $request->validate([
            'midwife_id' => 'required|exists:midwives,midwife_id',
            'area_id' => 'required|exists:areas,area_id',
            'batch_no' => 'required|string|max:100',
            'date' => 'required|date',
            'vaccine_used' => 'required|string|max:255',
            'dose' => 'nullable|string|max:100',
            'no_of_vaccine_performed' => 'required|integer|min:0',
            'items_received' => 'nullable|integer|min:0',
            'items_returned' => 'nullable|integer|min:0',
            'opening_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $areaId = $validated['area_id'];
        unset($validated['area_id']);

        $report = Report::create($validated);
        $summary = $reportService->generateDailySummary($report, $areaId);

        return response()->json([
            'report' => $report->load(['midwife', 'dailyClinicSummary']),
            'daily_summary' => $summary,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(Report::with(['midwife', 'dailyClinicSummary'])->findOrFail($id));
    }

    public function update(Request $request, int $id, ClinicReportService $reportService): JsonResponse
    {
        $report = Report::findOrFail($id);
        $validated = $request->validate([
            'midwife_id' => 'sometimes|required|exists:midwives,midwife_id',
            'area_id' => 'nullable|exists:areas,area_id',
            'batch_no' => 'sometimes|required|string|max:100',
            'date' => 'sometimes|required|date',
            'vaccine_used' => 'sometimes|required|string|max:255',
            'dose' => 'nullable|string|max:100',
            'no_of_vaccine_performed' => 'sometimes|required|integer|min:0',
            'items_received' => 'nullable|integer|min:0',
            'items_returned' => 'nullable|integer|min:0',
            'opening_stock' => 'sometimes|required|integer|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $areaId = $validated['area_id'] ?? ($report->midwife ? $report->midwife->area_id : null);
        unset($validated['area_id']);

        $report->update($validated);
        $summary = $areaId ? $reportService->generateDailySummary($report, $areaId) : null;

        return response()->json([
            'report' => $report->load(['midwife', 'dailyClinicSummary']),
            'daily_summary' => $summary,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        Report::findOrFail($id)->delete();
        return response()->json(['message' => 'Report deleted successfully']);
    }
}

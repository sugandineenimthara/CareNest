<?php

namespace App\Services;

use App\Models\DailyClinicSummary;
use App\Models\Report;

class ClinicReportService
{
    public function generateDailySummary(Report $report, int $areaId): DailyClinicSummary
    {
        $closingStock = $report->closing_stock ?? (
            ($report->opening_stock ?? 0)
            + ($report->items_received ?? 0)
            - ($report->items_returned ?? 0)
            - ($report->no_of_vaccine_performed ?? 0)
        );

        return DailyClinicSummary::updateOrCreate(
            ['report_id' => $report->report_id],
            [
                'midwife_id' => $report->midwife_id,
                'area_id' => $areaId,
                'batch_no' => $report->batch_no,
                'date' => $report->date,
                'vaccine_used' => $report->vaccine_used,
                'opening_stock' => $report->opening_stock ?? 0,
                'closing_stock' => $closingStock,
            ]
        );
    }
}

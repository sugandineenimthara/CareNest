<?php

namespace App\Services;

use Carbon\Carbon;

class MaternalService
{
    /**
     * Calculate Expected Date of Delivery (EDD) using Naegele's Rule (LMP + 280 days)
     */
    public function calculateEDD(string|\DateTimeInterface $lmp): string
    {
        return Carbon::parse($lmp)->addDays(280)->toDateString();
    }

    /**
     * Calculate Gestational Age in weeks based on LMP and reference date.
     */
    public function calculateGestationalWeeks(string|\DateTimeInterface $lmp, string|\DateTimeInterface $referenceDate = 'now'): int
    {
        $lmpCarbon = Carbon::parse($lmp);
        $refCarbon = Carbon::parse($referenceDate);
        return (int) floor($lmpCarbon->diffInDays($refCarbon) / 7);
    }

    /**
     * Evaluate Maternal Health Risk indicators based on tests and family history.
     */
    public function evaluateHealthRisk(array $testData, array $familyHistory = []): array
    {
        $risks = [];

        if (isset($testData['Haemoglobin']) && is_numeric($testData['Haemoglobin']) && (float)$testData['Haemoglobin'] < 11.0) {
            $risks[] = 'Anemia Warning: Low Haemoglobin (< 11.0 g/dL)';
        }

        if (isset($testData['blood_sugar']) && strtolower($testData['blood_sugar']) !== 'normal') {
            $risks[] = 'Elevated Blood Sugar / Gestational Diabetes Risk';
        }

        if (isset($testData['urine_sugar_level']) && in_array(strtolower($testData['urine_sugar_level']), ['positive', '+', '++', '+++'])) {
            $risks[] = 'Urine Sugar Positive';
        }

        if (isset($familyHistory['high_blood_pressure']) && $familyHistory['high_blood_pressure']) {
            $risks[] = 'Family History of Hypertension';
        }

        return [
            'is_high_risk' => count($risks) > 0,
            'risk_factors' => $risks,
        ];
    }
}

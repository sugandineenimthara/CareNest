<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\Report;
use App\Models\TestDone;
use App\Models\TriposhaBook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareNestBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_area_and_midwife(): void
    {
        $area = Area::create(['area_name' => 'Colombo District North']);
        $this->assertDatabaseHas('areas', ['area_name' => 'Colombo District North']);

        $midwife = Midwife::create([
            'midwife_name' => 'Sita Perera',
            'area_id' => $area->area_id,
        ]);

        $this->assertDatabaseHas('midwives', [
            'midwife_name' => 'Sita Perera',
            'area_id' => $area->area_id,
        ]);
    }

    public function test_mother_derived_age_and_bmi(): void
    {
        $area = Area::create(['area_name' => 'Kandy']);
        $midwife = Midwife::create([
            'midwife_name' => 'Nimali Fernando',
            'area_id' => $area->area_id,
        ]);

        $mother = Mother::create([
            'mother_name' => 'Kamala Silva',
            'phone_no' => '0771234567',
            'midwife_id' => $midwife->midwife_id,
            'height' => 160.00, // 1.6m
            'weight' => 64.00,  // BMI = 64 / (1.6^2) = 25.00
            'date_of_birth' => '1995-05-15',
        ]);

        $this->assertEquals(25.00, $mother->bmi);
        $this->assertNotNull($mother->age);
    }

    public function test_pregnancy_history_edd_calculation_api(): void
    {
        $area = Area::create(['area_name' => 'Galle']);
        $midwife = Midwife::create(['midwife_name' => 'Anoma Jayasinghe', 'area_id' => $area->area_id]);
        $mother = Mother::create(['mother_name' => 'Sunethra', 'midwife_id' => $midwife->midwife_id]);

        $response = $this->postJson('/api/v1/pregnancy-histories', [
            'mother_id' => $mother->mother_id,
            'no_living_children' => 1,
            'last_menstrual_period' => '2026-01-01',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('expected_date_of_delivery', '2026-10-08');
    }

    public function test_report_and_daily_summary_stock_calculation(): void
    {
        $area = Area::create(['area_name' => 'Kurunegala']);
        $midwife = Midwife::create(['midwife_name' => 'Malkanthi', 'area_id' => $area->area_id]);

        $response = $this->postJson('/api/v1/reports', [
            'midwife_id' => $midwife->midwife_id,
            'area_id' => $area->area_id,
            'batch_no' => 'BCG-2026-09',
            'date' => '2026-09-17',
            'vaccine_used' => 'BCG',
            'opening_stock' => 100,
            'items_received' => 50,
            'items_returned' => 10,
            'no_of_vaccine_performed' => 20,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('report.closing_stock', 120);
        $this->assertDatabaseHas('daily_clinic_summaries', [
            'batch_no' => 'BCG-2026-09',
            'closing_stock' => 120,
        ]);
    }
}

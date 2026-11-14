<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\PreviousPregnancyHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotherTetanusVaccinationTest extends TestCase
{
    use RefreshDatabase;

    protected User $midwifeUser;
    protected Midwife $midwife;
    protected Area $area;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Gampaha']);

        $this->midwifeUser = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);

        $this->midwife = Midwife::create([
            'midwife_name' => 'Soma Edirisinghe',
            'email' => $this->midwifeUser->email,
            'user_id' => $this->midwifeUser->id,
            'area_id' => $this->area->area_id,
            'status' => 'approved',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);
    }

    public function test_first_pregnancy_tt_dose_recording(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Malini Fonseka',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => false,
            'no_living_children' => 0,
        ]);

        // Pregnancy count is 1, TT doses count is 0
        $this->assertEquals(1, $mother->pregnancy_number);
        $this->assertEquals(0, $mother->tetanusDosesCount());
        $this->assertFalse($mother->isTetanusSafe());

        // Midwife records first TT dose
        $response = $this->actingAs($this->midwifeUser)->post('/midwife/immunizations/mother', [
            'mother_id' => $mother->mother_id,
            'batch_no' => 'TT-P1-001',
            'immunization_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'dose' => 'Pregnancy #1 Tetanus Dose',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(1, $mother->fresh()->tetanusDosesCount());
        $this->assertFalse($mother->fresh()->isTetanusSafe());
    }

    public function test_when_second_pregnancy_happens_another_tt_vaccination_record_is_required_and_added(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Geetha Kumarasinghe',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        // First pregnancy delivered
        PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => true,
            'delivery_date' => '2024-05-10',
            'no_living_children' => 1,
        ]);

        PreviousPregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2024-05-10',
            'which_pregnancy' => '1st Pregnancy',
            'gender' => 'Female',
        ]);

        // Mother received 1st TT dose during first pregnancy
        Immunization::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'vaccine_name' => 'Tetanus Toxoid (TT)',
            'batch_no' => 'TT-DOSE1-BATCH',
            'immunization_date' => '2024-02-15',
            'dose' => 'Pregnancy #1 Tetanus Dose',
            'status' => 'Completed',
        ]);

        // Second pregnancy happens
        PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => false,
            'no_living_children' => 1,
            'last_menstrual_period' => '2026-03-01',
        ]);

        // Mother is now in 2nd pregnancy
        $this->assertEquals(2, $mother->fresh()->pregnancy_number);
        $this->assertEquals(1, $mother->fresh()->tetanusDosesCount());
        $this->assertFalse($mother->fresh()->isTetanusSafe());

        // Check immunization page: mother should be listed as Due for TT dose
        $viewResponse = $this->actingAs($this->midwifeUser)->get('/midwife/immunizations?tab=mothers');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Tetanus Dose Required');
        $viewResponse->assertSee('Dose 1');
        $viewResponse->assertSee('TT-DOSE1-BATCH');
        $viewResponse->assertSee('Dose required for Pregnancy #2');

        // Midwife records second pregnancy TT vaccination
        $storeResponse = $this->actingAs($this->midwifeUser)->post('/midwife/immunizations/mother', [
            'mother_id' => $mother->mother_id,
            'batch_no' => 'TT-DOSE2-BATCH',
            'immunization_date' => '2026-05-20',
            'dose' => 'Pregnancy #2 Tetanus Dose',
        ]);

        $storeResponse->assertSessionHasNoErrors();

        // There are now 2 distinct vaccination records in immunization
        $this->assertEquals(2, $mother->fresh()->tetanusDosesCount());
        $this->assertDatabaseHas('immunizations', [
            'mother_id' => $mother->mother_id,
            'batch_no' => 'TT-DOSE1-BATCH',
        ]);
        $this->assertDatabaseHas('immunizations', [
            'mother_id' => $mother->mother_id,
            'batch_no' => 'TT-DOSE2-BATCH',
        ]);

        // Viewing immunization registry now displays both vaccination records
        $viewResponse2 = $this->actingAs($this->midwifeUser)->get('/midwife/immunizations?tab=mothers');
        $viewResponse2->assertStatus(200);
        $viewResponse2->assertSee('TT-DOSE1-BATCH');
        $viewResponse2->assertSee('TT-DOSE2-BATCH');
        $viewResponse2->assertSee('Vaccinated');
    }

    public function test_after_fourth_pregnancy_dose_mother_is_counted_and_recorded_as_safe(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Swarna Mallawarachchi',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        // Simulate 3 previous pregnancies
        for ($i = 1; $i <= 3; $i++) {
            PreviousPregnancyHistory::create([
                'mother_id' => $mother->mother_id,
                'date_of_birth' => Carbon::now()->subYears(4 - $i)->format('Y-m-d'),
                'which_pregnancy' => "{$i}th Pregnancy",
                'gender' => 'Male',
            ]);

            Immunization::create([
                'mother_id' => $mother->mother_id,
                'midwife_id' => $this->midwife->midwife_id,
                'vaccine_name' => 'Tetanus Toxoid (TT)',
                'batch_no' => "TT-BATCH-00{$i}",
                'immunization_date' => Carbon::now()->subYears(4 - $i)->format('Y-m-d'),
                'dose' => "Pregnancy #{$i} Tetanus Dose",
                'status' => 'Completed',
            ]);
        }

        // Active 4th pregnancy
        PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => false,
            'no_living_children' => 3,
        ]);

        // At 3 doses, she is not yet safe
        $this->assertEquals(4, $mother->pregnancy_number);
        $this->assertEquals(3, $mother->tetanusDosesCount());
        $this->assertFalse($mother->isTetanusSafe());

        // Administer 4th pregnancy dose
        $response = $this->actingAs($this->midwifeUser)->post('/midwife/immunizations/mother', [
            'mother_id' => $mother->mother_id,
            'batch_no' => 'TT-BATCH-004',
            'immunization_date' => Carbon::now()->format('Y-m-d'),
            'dose' => 'Pregnancy #4 Tetanus Dose',
        ]);

        $response->assertSessionHasNoErrors();

        // Now she has 4 doses
        $mother->refresh();
        $this->assertEquals(4, $mother->tetanusDosesCount());
        $this->assertTrue($mother->isTetanusSafe());

        // She should be counted as safe and recorded as safe in immunization view
        $viewResponse = $this->actingAs($this->midwifeUser)->get('/midwife/immunizations?tab=mothers');
        $viewResponse->assertStatus(200);

        // Safe from Tetanus badge and status displayed
        $viewResponse->assertSee('Safe from Tetanus');
        $viewResponse->assertSee('4 doses completed');

        // Mothers safe count should include this mother
        $safeCount = $viewResponse->viewData('mothersSafeCount');
        $this->assertGreaterThanOrEqual(1, $safeCount);
    }
}

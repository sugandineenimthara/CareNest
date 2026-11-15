<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaccineReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $midwifeUser;
    protected Midwife $midwife;
    protected Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Kala-Eliya']);

        $this->adminUser = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin_test@carenest.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'status'   => 'approved',
        ]);

        $this->midwifeUser = User::create([
            'name'     => 'Midwife User',
            'email'    => 'midwife_test@carenest.com',
            'password' => bcrypt('password'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);

        $this->midwife = Midwife::create([
            'midwife_name' => 'Sister Perera',
            'area_id'      => $this->area->area_id,
            'user_id'      => $this->midwifeUser->id,
        ]);
    }

    public function test_admin_can_view_vaccine_report_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Vaccine Reports');
        $response->assertSee('sidebar');
        $response->assertSee('Filter Vaccine Report');
        $response->assertSee('Pediatric Clinic');
        $response->assertSee('Mother Clinic');
        $response->assertSee('Vaccination Type');
        $response->assertSee('Vaccines Got from Storage');
        $response->assertSee('No. of Vaccines Used for Clinic');
        $response->assertSee('No. of Vaccines Left');
        $response->assertSee('BCG');
        $response->assertSee('Pentavalent');
        $response->assertSee('Oral Polio (OPV)');
        $response->assertSee('user-badge-wrapper');
    }

    public function test_midwife_can_view_vaccine_report_page(): void
    {
        $response = $this->actingAs($this->midwifeUser)->get(route('midwife.vaccine-reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Vaccine Reports');
        $response->assertSee('user-badge-wrapper');
    }

    public function test_store_vaccine_storage_stock(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.vaccine-reports.store'), [
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-01',
            'vaccine_name'   => 'BCG',
            'stock_received' => 50,
            'batch_no'       => 'BCG-2026-TEST',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reports', [
            'clinic_type'    => 'pediatric',
            'vaccine_used'   => 'BCG',
            'items_received' => 50,
            'batch_no'       => 'BCG-2026-TEST',
        ]);
    }

    public function test_report_calculates_storage_used_and_left_accurately(): void
    {
        Report::create([
            'midwife_id'              => $this->midwife->midwife_id,
            'clinic_type'             => 'pediatric',
            'date'                    => '2026-11-01',
            'vaccine_used'            => 'BCG',
            'items_received'          => 40,
            'opening_stock'           => 0,
            'items_returned'          => 0,
            'no_of_vaccine_performed' => 0,
            'closing_stock'           => 40,
            'batch_no'                => 'BCG-BATCH-1',
        ]);

        $mother = Mother::create([
            'mother_name'             => 'Test Mother',
            'email'                   => 'mother@example.com',
            'nic_number'              => '901234567V',
            'contact_number'          => '0771234567',
            'address'                 => '123 Test Road',
            'date_of_birth'           => '1995-01-01',
            'marriage_date'           => '2020-01-01',
            'midwife_id'              => $this->midwife->midwife_id,
            'area_id'                 => $this->area->area_id,
            'blood_group'             => 'O+',
            'rubella_immunization'    => 'Yes',
            'pre_pregnancy_screening' => 'Normal',
            'status'                  => 'active',
        ]);

        $child = Child::create([
            'child_name'              => 'Baby Test',
            'gender'                  => 'male',
            'mother_id'               => $mother->mother_id,
            'midwife_id'              => $this->midwife->midwife_id,
            'area_id'                 => $this->area->area_id,
            'date_of_birth'           => '2026-10-01',
            'birth_weight'            => 3.2,
            'birth_length'            => 50,
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Immunization::create([
                'child_id'          => $child->child_id,
                'midwife_id'        => $this->midwife->midwife_id,
                'vaccine_name'      => 'BCG',
                'batch_no'          => 'BCG-BATCH-1',
                'immunization_date' => '2026-11-01',
                'status'            => 'Completed',
            ]);
        }

        $response = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'pediatric',
            'from_date'   => '2026-11-01',
            'to_date'     => '2026-11-01',
        ]));

        $response->assertStatus(200);

        $vaccineRows = $response->viewData('vaccineRows');
        $bcgRow = $vaccineRows->firstWhere('vaccine_type', 'BCG');

        $this->assertNotNull($bcgRow);
        $this->assertEquals(40, $bcgRow['got_from_storage']);
        $this->assertEquals(3, $bcgRow['used_in_clinic']);
        $this->assertEquals(37, $bcgRow['vaccines_left']);
    }

    public function test_filter_datewise_and_clinicwise_isolates_records(): void
    {
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-01',
            'vaccine_used'   => 'Pentavalent',
            'items_received' => 25,
            'opening_stock'  => 0,
            'closing_stock'  => 25,
            'batch_no'       => 'PENTA-1',
        ]);

        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'mother',
            'date'           => '2026-11-01',
            'vaccine_used'   => 'Tetanus Toxoid (TT)',
            'items_received' => 60,
            'opening_stock'  => 0,
            'closing_stock'  => 60,
            'batch_no'       => 'TT-1',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'pediatric',
        ]));

        $pentaRow = $response->viewData('vaccineRows')->firstWhere('vaccine_type', 'Pentavalent');
        $this->assertNotNull($pentaRow);
        $this->assertEquals(25, $pentaRow['got_from_storage']);
        $ttRow = $response->viewData('vaccineRows')->firstWhere('vaccine_type', 'Tetanus Toxoid (TT)');
        $this->assertNull($ttRow);

        $response2 = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'mother',
        ]));

        $ttRow2 = $response2->viewData('vaccineRows')->firstWhere('vaccine_type', 'Tetanus Toxoid (TT)');
        $this->assertNotNull($ttRow2);
        $this->assertEquals(60, $ttRow2['got_from_storage']);
        $pentaRow2 = $response2->viewData('vaccineRows')->firstWhere('vaccine_type', 'Pentavalent');
        $this->assertNull($pentaRow2);
    }

    public function test_entering_fitv_stock_does_not_increase_opv_stock(): void
    {
        // Record 50 doses of Inactivated Polio (FITV) from storage
        $this->actingAs($this->adminUser)->post(route('admin.vaccine-reports.store'), [
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-01',
            'vaccine_name'   => 'Inactivated Polio (FITV)',
            'stock_received' => 50,
            'batch_no'       => 'FITV-BATCH-1',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'pediatric',
            'from_date'   => '2026-11-01',
            'to_date'     => '2026-11-01',
        ]));

        $response->assertStatus(200);

        $vaccineRows = $response->viewData('vaccineRows');
        $fitvRow = $vaccineRows->firstWhere('vaccine_type', 'Inactivated Polio (FITV)');
        $opvRow = $vaccineRows->firstWhere('vaccine_type', 'Oral Polio (OPV)');

        $this->assertNotNull($fitvRow);
        $this->assertEquals(50, $fitvRow['got_from_storage']);

        // OPV stock must remain 0 and NOT increase when FITV stock is entered
        $this->assertNotNull($opvRow);
        $this->assertEquals(0, $opvRow['got_from_storage']);
    }

    public function test_new_stock_entry_adds_to_previous_closing_stock(): void
    {
        // First delivery: 30 doses BCG
        $this->actingAs($this->adminUser)->post(route('admin.vaccine-reports.store'), [
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-01',
            'vaccine_name'   => 'BCG',
            'stock_received' => 30,
            'batch_no'       => 'BCG-A',
        ]);

        // Second delivery: 20 more BCG doses — should stack on previous closing (30)
        $this->actingAs($this->adminUser)->post(route('admin.vaccine-reports.store'), [
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-05',
            'vaccine_name'   => 'BCG',
            'stock_received' => 20,
            'batch_no'       => 'BCG-B',
        ]);

        $secondReport = Report::where('vaccine_used', 'BCG')->orderByDesc('report_id')->first();

        // opening_stock of second entry must be 30 (the closing of the first)
        $this->assertEquals(30, (int) $secondReport->opening_stock);
        // closing_stock of second entry must be 30 + 20 = 50
        $this->assertEquals(50, (int) $secondReport->closing_stock);
    }

    public function test_mother_immunization_not_counted_in_pediatric_vaccine_report(): void
    {
        $mother = Mother::create([
            'mother_name'             => 'TT Mother',
            'email'                   => 'ttmother@example.com',
            'nic_number'              => '987654321V',
            'contact_number'          => '0779999999',
            'address'                 => 'Test Lane',
            'date_of_birth'           => '1992-01-01',
            'marriage_date'           => '2015-01-01',
            'midwife_id'              => $this->midwife->midwife_id,
            'area_id'                 => $this->area->area_id,
            'blood_group'             => 'A+',
            'rubella_immunization'    => 'Yes',
            'pre_pregnancy_screening' => 'Normal',
            'status'                  => 'active',
        ]);

        $child = Child::create([
            'child_name'    => 'Baby BCG',
            'gender'        => 'female',
            'mother_id'     => $mother->mother_id,
            'midwife_id'    => $this->midwife->midwife_id,
            'area_id'       => $this->area->area_id,
            'date_of_birth' => '2026-10-01',
            'birth_weight'  => 3.0,
            'birth_length'  => 48,
        ]);

        // BCG for child only
        Immunization::create([
            'child_id'          => $child->child_id,
            'mother_id'         => null,
            'midwife_id'        => $this->midwife->midwife_id,
            'vaccine_name'      => 'BCG',
            'batch_no'          => 'BCG-CHILD',
            'immunization_date' => '2026-10-01',
            'status'            => 'Completed',
        ]);

        // TT for mother only
        Immunization::create([
            'child_id'          => null,
            'mother_id'         => $mother->mother_id,
            'midwife_id'        => $this->midwife->midwife_id,
            'vaccine_name'      => 'Tetanus Toxoid (TT)',
            'batch_no'          => 'TT-MOM',
            'immunization_date' => '2026-10-15',
            'status'            => 'Completed',
        ]);

        // BCG stock report
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'pediatric',
            'date'           => '2026-10-01',
            'vaccine_used'   => 'BCG',
            'items_received' => 10,
            'opening_stock'  => 0,
            'closing_stock'  => 10,
            'batch_no'       => 'BCG-REP',
        ]);

        // TT stock report
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'mother',
            'date'           => '2026-10-15',
            'vaccine_used'   => 'Tetanus Toxoid (TT)',
            'items_received' => 5,
            'opening_stock'  => 0,
            'closing_stock'  => 5,
            'batch_no'       => 'TT-REP',
        ]);

        // Pediatric report: BCG used = 1 (child only), not 2 (mother TT should NOT be counted)
        $response = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'pediatric',
        ]));
        $response->assertStatus(200);
        $bcgRow = $response->viewData('vaccineRows')->firstWhere('vaccine_type', 'BCG');
        $this->assertNotNull($bcgRow);
        $this->assertEquals(1, $bcgRow['used_in_clinic']); // Only child BCG

        // Mother report: TT used = 1 (mother only), not 2 (child BCG should NOT be counted)
        $response2 = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index', [
            'clinic_type' => 'mother',
        ]));
        $response2->assertStatus(200);
        $ttRow = $response2->viewData('vaccineRows')->firstWhere('vaccine_type', 'Tetanus Toxoid (TT)');
        $this->assertNotNull($ttRow);
        $this->assertEquals(1, $ttRow['used_in_clinic']); // Only mother TT
    }

    public function test_recording_new_storage_redirects_to_unfiltered_view_and_preserves_all_vaccines(): void
    {
        // Existing stock on 2026-11-14: 30 BCG, 30 Pentavalent, 80 FITV, 20 MMR
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-14',
            'vaccine_used'   => 'BCG',
            'items_received' => 30,
            'opening_stock'  => 0,
            'closing_stock'  => 30,
            'batch_no'       => 'BCG-1',
        ]);
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-14',
            'vaccine_used'   => 'Pentavalent',
            'items_received' => 30,
            'opening_stock'  => 0,
            'closing_stock'  => 30,
            'batch_no'       => 'PENTA-1',
        ]);
        Report::create([
            'midwife_id'     => $this->midwife->midwife_id,
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-14',
            'vaccine_used'   => 'Inactivated Polio (FITV)',
            'items_received' => 80,
            'opening_stock'  => 0,
            'closing_stock'  => 80,
            'batch_no'       => 'FITV-1',
        ]);

        // Record 10 new Pentavalent on 2026-11-15
        $response = $this->actingAs($this->adminUser)->post(route('admin.vaccine-reports.store'), [
            'clinic_type'    => 'pediatric',
            'date'           => '2026-11-15',
            'vaccine_name'   => 'Pentavalent',
            'stock_received' => 10,
            'batch_no'       => 'PENTA-2',
        ]);

        // Must redirect to unfiltered index (NOT single-day filter)
        $response->assertRedirect(route('admin.vaccine-reports.index'));

        // Follow the redirect to the index view
        $indexResponse = $this->actingAs($this->adminUser)->get(route('admin.vaccine-reports.index'));
        $indexResponse->assertStatus(200);

        $vaccineRows = $indexResponse->viewData('vaccineRows');

        // Pentavalent must be 30 + 10 = 40
        $pentaRow = $vaccineRows->firstWhere('vaccine_type', 'Pentavalent');
        $this->assertNotNull($pentaRow);
        $this->assertEquals(40, $pentaRow['got_from_storage']);
        $this->assertEquals(40, $pentaRow['vaccines_left']);

        // BCG must still be 30 (NOT dropped or zeroed out)
        $bcgRow = $vaccineRows->firstWhere('vaccine_type', 'BCG');
        $this->assertNotNull($bcgRow);
        $this->assertEquals(30, $bcgRow['got_from_storage']);

        // FITV must still be 80 (NOT dropped or zeroed out)
        $fitvRow = $vaccineRows->firstWhere('vaccine_type', 'Inactivated Polio (FITV)');
        $this->assertNotNull($fitvRow);
        $this->assertEquals(80, $fitvRow['got_from_storage']);

        // Mother vaccine Tetanus Toxoid should also remain in the overall view
        $ttRow = $vaccineRows->firstWhere('vaccine_type', 'Tetanus Toxoid (TT)');
        $this->assertNotNull($ttRow);
    }
}

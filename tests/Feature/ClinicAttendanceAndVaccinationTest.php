<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicAttendanceAndVaccinationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Area $area1;
    protected Area $area2;
    protected Midwife $midwife1;
    protected Midwife $midwife2;
    protected Mother $mother1;
    protected Mother $mother2;
    protected Child $child1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->area1 = Area::create(['area_name' => 'Colombo North']);
        $this->area2 = Area::create(['area_name' => 'Kandy Central']);

        $userMw1 = User::factory()->create(['role' => 'midwife']);
        $this->midwife1 = Midwife::create([
            'user_id' => $userMw1->id,
            'midwife_name' => 'Midwife Sunila',
            'phone_no' => '0711111111',
            'area_id' => $this->area1->area_id,
        ]);

        $userMw2 = User::factory()->create(['role' => 'midwife']);
        $this->midwife2 = Midwife::create([
            'user_id' => $userMw2->id,
            'midwife_name' => 'Midwife Kamal',
            'phone_no' => '0722222222',
            'area_id' => $this->area2->area_id,
        ]);

        $this->mother1 = Mother::create([
            'mother_name' => 'Nadeeka Perera',
            'phone_no' => '0777000001',
            'midwife_id' => $this->midwife1->midwife_id,
            'date_of_birth' => '1995-05-15',
            'height' => 160,
            'weight' => 55,
        ]);

        $this->mother2 = Mother::create([
            'mother_name' => 'Sanduni Silva',
            'phone_no' => '0777000002',
            'midwife_id' => $this->midwife2->midwife_id,
            'date_of_birth' => '1993-08-20',
            'height' => 155,
            'weight' => 60,
        ]);

        $this->child1 = Child::create([
            'mother_id' => $this->mother1->mother_id,
            'midwife_id' => $this->midwife1->midwife_id,
            'area_id' => $this->area1->area_id,
            'child_name' => 'Kasun Perera',
            'gender' => 'Male',
            'date_of_birth' => '2025-01-10',
            'birth_weight' => 3.2,
            'birth_length' => 50.0,
        ]);
    }

    public function test_mother_vaccination_areawise_filter(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.immunizations.index', [
            'tab' => 'mothers',
            'area_id' => $this->area1->area_id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Nadeeka Perera');
        $response->assertDontSee('Sanduni Silva');
    }

    public function test_mother_clinic_attendance_records_bp_and_updates_profile(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => now()->toDateString(),
            'clinic_type' => 'mother',
            'clinic_name' => 'Antenatal MOH Clinic',
            'midwife_id'  => $this->midwife1->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ]
            ],
        ]);

        $response->assertRedirect(route('admin.attendances.index'));

        
        $this->assertDatabaseHas('attendances', [
            'mother_id'      => $this->mother1->mother_id,
            'clinic_type'    => 'mother',
            'blood_pressure' => '120/80',
        ]);

        
        $this->mother1->refresh();
        $this->assertEquals('120/80', $this->mother1->blood_pressure);
        $this->assertFalse($this->mother1->isHighBloodPressure());
    }

    public function test_mother_clinic_attendance_high_risk_bp_triggers_alert(): void
    {
        
        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => now()->toDateString(),
            'clinic_type' => 'mother',
            'clinic_name' => 'Antenatal High Risk Clinic',
            'midwife_id'  => $this->midwife1->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '150/95',
                ]
            ],
        ]);

        $response->assertRedirect(route('admin.attendances.index'));
        $response->assertSessionHas('high_risk_alert');

        
        $this->mother1->refresh();
        $this->assertEquals('150/95', $this->mother1->blood_pressure);
        $this->assertTrue($this->mother1->isHighBloodPressure());

        
        $profileResponse = $this->actingAs($this->adminUser)->get(route('admin.mothers.show', $this->mother1->mother_id));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('HIGH RISK PATIENT ALERT');
        $profileResponse->assertSee('150/95 mmHg');

        
        $alertResponse = $this->actingAs($this->adminUser)->get(route('admin.alerts.index'));
        $alertResponse->assertStatus(200);
        $alertResponse->assertSee('High Risk Blood Pressure (> 140/90)');
        $alertResponse->assertSee('150/95');
    }

    public function test_pediatric_clinic_attendance_updates_child_height_weight_and_bmi(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => now()->toDateString(),
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby Clinic',
            'midwife_id'  => $this->midwife1->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 80.0,
                    'weight'   => 10.0,
                ]
            ],
        ]);

        $response->assertRedirect(route('admin.attendances.index'));

        
        $this->assertDatabaseHas('attendances', [
            'child_id'    => $this->child1->child_id,
            'clinic_type' => 'pediatric',
            'height'      => 80.0,
            'weight'      => 10.0,
            'bmi'         => 15.63,
        ]);

        
        $this->child1->refresh();
        $this->assertEquals(80.0, (float)$this->child1->height);
        $this->assertEquals(10.0, (float)$this->child1->weight);
        $this->assertEquals(15.63, (float)$this->child1->bmi);

        
        $childProfileResponse = $this->actingAs($this->adminUser)->get(route('admin.children.show', $this->child1->child_id));
        $childProfileResponse->assertStatus(200);
        $childProfileResponse->assertSee('80 cm');
        $childProfileResponse->assertSee('10 kg');
        $childProfileResponse->assertSee('Current Growth');
        $childProfileResponse->assertSee('Current Height');
        $childProfileResponse->assertSee('Current Weight');
        $childProfileResponse->assertSee('Current BMI Value');
    }

    public function test_mother_blood_pressure_higher_than_140_triggers_high_risk_alert(): void
    {
        // Set blood pressure higher than 140 (as single number 145)
        $this->mother1->update(['blood_pressure' => '145']);
        $this->mother1->refresh();

        $this->assertTrue($this->mother1->isHighBloodPressure());

        // Verify mother appears on the high risk alert page
        $alertResponse = $this->actingAs($this->adminUser)->get(route('admin.alerts.index'));
        $alertResponse->assertStatus(200);
        $alertResponse->assertSee($this->mother1->mother_name);
        $alertResponse->assertSee('145 mmHg');
    }
}

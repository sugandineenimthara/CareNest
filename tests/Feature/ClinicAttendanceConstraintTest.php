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

class ClinicAttendanceConstraintTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Area $area;
    protected Midwife $midwife;
    protected Mother $mother1;
    protected Mother $mother2;
    protected Child $child1;
    protected Child $child2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->area = Area::create(['area_name' => 'Colombo North']);

        $userMw = User::factory()->create(['role' => 'midwife']);
        $this->midwife = Midwife::create([
            'user_id' => $userMw->id,
            'midwife_name' => 'Midwife Sunila',
            'phone_no' => '0711111111',
            'area_id' => $this->area->area_id,
        ]);

        $this->mother1 = Mother::create([
            'mother_name' => 'Nadeeka Perera',
            'phone_no' => '0777000001',
            'midwife_id' => $this->midwife->midwife_id,
            'date_of_birth' => '1995-05-15',
        ]);

        $this->mother2 = Mother::create([
            'mother_name' => 'Sanduni Silva',
            'phone_no' => '0777000002',
            'midwife_id' => $this->midwife->midwife_id,
            'date_of_birth' => '1993-08-20',
        ]);

        $this->child1 = Child::create([
            'mother_id' => $this->mother1->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Kasun Perera',
            'gender' => 'Male',
            'date_of_birth' => '2025-01-10',
        ]);

        $this->child2 = Child::create([
            'mother_id' => $this->mother2->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Amal Silva',
            'gender' => 'Male',
            'date_of_birth' => '2025-02-15',
        ]);
    }

    public function test_cannot_create_two_maternal_clinics_on_the_same_day_with_different_names(): void
    {
        $clinicDate = '2026-11-20';

        // 1. Record first maternal clinic
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'First MOH Maternity Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ]
            ],
        ]);

        $res1->assertRedirect(route('admin.attendances.index'));
        $this->assertDatabaseHas('attendances', [
            'clinic_name' => 'First MOH Maternity Clinic',
            'mother_id'   => $this->mother1->mother_id,
        ]);

        // 2. Attempt to create second maternal clinic on the same date with different name
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'Second MOH Maternity Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother2->mother_id,
                    'blood_pressure' => '118/78',
                ]
            ],
        ]);

        $res2->assertSessionHas('error');
        $this->assertDatabaseMissing('attendances', [
            'clinic_name' => 'Second MOH Maternity Clinic',
        ]);
    }

    public function test_cannot_create_two_pediatric_clinics_on_the_same_day_with_different_names(): void
    {
        $clinicDate = '2026-11-20';

        // 1. Record first pediatric clinic
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 70.0,
                    'weight'   => 8.0,
                ]
            ],
        ]);

        $res1->assertRedirect(route('admin.attendances.index'));

        // 2. Attempt to create second pediatric clinic on the same date with different name
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Pediatric Immunization Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child2->child_id,
                    'height'   => 68.0,
                    'weight'   => 7.5,
                ]
            ],
        ]);

        $res2->assertSessionHas('error');
        $this->assertDatabaseMissing('attendances', [
            'clinic_name' => 'Pediatric Immunization Clinic',
        ]);
    }

    public function test_can_create_one_maternal_clinic_and_one_pediatric_clinic_on_the_same_day(): void
    {
        $clinicDate = '2026-11-20';

        // 1. Record maternal clinic
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ]
            ],
        ]);
        $res1->assertRedirect(route('admin.attendances.index'));

        // 2. Record pediatric clinic on same date
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby & Child Development Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 75.0,
                    'weight'   => 9.0,
                ]
            ],
        ]);
        $res2->assertRedirect(route('admin.attendances.index'));

        // Both exist
        $this->assertDatabaseHas('attendances', [
            'clinic_date' => '2026-11-20 00:00:00',
            'clinic_type' => 'mother',
            'mother_id'   => $this->mother1->mother_id,
        ]);
        $this->assertDatabaseHas('attendances', [
            'clinic_date' => '2026-11-20 00:00:00',
            'clinic_type' => 'pediatric',
            'child_id'    => $this->child1->child_id,
        ]);
    }

    public function test_cannot_record_same_mother_twice_in_the_same_clinic_batch(): void
    {
        $clinicDate = '2026-11-20';

        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ],
                [
                    'mother_id'      => $this->mother1->mother_id, // Duplicate
                    'blood_pressure' => '122/82',
                ]
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_cannot_record_same_child_twice_in_the_same_clinic_batch(): void
    {
        $clinicDate = '2026-11-20';

        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 70.0,
                    'weight'   => 8.0,
                ],
                [
                    'child_id' => $this->child1->child_id, // Duplicate
                    'height'   => 70.5,
                    'weight'   => 8.1,
                ]
            ],
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_cannot_record_mother_who_already_attended_maternal_clinic_on_the_same_date(): void
    {
        $clinicDate = '2026-11-20';

        // First attendance
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ]
            ],
        ]);
        $res1->assertRedirect(route('admin.attendances.index'));

        // Second attempt on same date
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '125/85',
                ]
            ],
        ]);

        $res2->assertSessionHas('error');
        $this->assertEquals(1, Attendance::where('mother_id', $this->mother1->mother_id)->count());
    }

    public function test_cannot_record_child_who_already_attended_pediatric_clinic_on_the_same_date(): void
    {
        $clinicDate = '2026-11-20';

        // First attendance
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 70.0,
                    'weight'   => 8.0,
                ]
            ],
        ]);
        $res1->assertRedirect(route('admin.attendances.index'));

        // Second attempt on same date
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'pediatric',
            'clinic_name' => 'Well Baby Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'child_id' => $this->child1->child_id,
                    'height'   => 71.0,
                    'weight'   => 8.2,
                ]
            ],
        ]);

        $res2->assertSessionHas('error');
        $this->assertEquals(1, Attendance::where('child_id', $this->child1->child_id)->count());
    }

    public function test_mother_can_attend_maternal_clinic_on_different_dates(): void
    {
        // Date 1
        $res1 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => '2026-11-20',
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '120/80',
                ]
            ],
        ]);
        $res1->assertRedirect(route('admin.attendances.index'));

        // Date 2
        $res2 = $this->actingAs($this->adminUser)->post(route('admin.attendances.store.batch'), [
            'clinic_date' => '2026-11-27',
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'attendees'   => [
                [
                    'mother_id'      => $this->mother1->mother_id,
                    'blood_pressure' => '118/78',
                ]
            ],
        ]);
        $res2->assertRedirect(route('admin.attendances.index'));

        $this->assertEquals(2, Attendance::where('mother_id', $this->mother1->mother_id)->count());
    }

    public function test_api_enforces_one_clinic_per_type_per_day_and_single_attendance(): void
    {
        $clinicDate = '2026-11-20';

        // 1. Initial valid API creation
        $res1 = $this->actingAs($this->adminUser)->postJson('/api/v1/attendances', [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'mother_id'   => $this->mother1->mother_id,
        ]);
        $res1->assertStatus(201);

        // 2. Second clinic with different name on same date should be rejected (422)
        $res2 = $this->actingAs($this->adminUser)->postJson('/api/v1/attendances', [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'Alternative Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'mother_id'   => $this->mother2->mother_id,
        ]);
        $res2->assertStatus(422);

        // 3. Same mother attempting second attendance on same date should be rejected (422)
        $res3 = $this->actingAs($this->adminUser)->postJson('/api/v1/attendances', [
            'clinic_date' => $clinicDate,
            'clinic_type' => 'mother',
            'clinic_name' => 'MOH Maternity Care Clinic',
            'midwife_id'  => $this->midwife->midwife_id,
            'mother_id'   => $this->mother1->mother_id,
        ]);
        $res3->assertStatus(422);
    }
}

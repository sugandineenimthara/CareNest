<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_and_summary_table_excludes_capacity_and_action(): void
    {
        $admin = User::factory()->create([
            'role' => 'provider',
            'status' => 'approved',
        ]);

        $area1 = Area::create(['area_name' => 'Test Area Empty']);
        $area2 = Area::create(['area_name' => 'Test Area Two Staff']);

        
        Midwife::create(['midwife_name' => 'Midwife A', 'area_id' => $area2->area_id]);
        Midwife::create(['midwife_name' => 'Midwife B', 'area_id' => $area2->area_id]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Clinic Areas Overview');
        $response->assertSee('CLINIC AREA');
        $response->assertSee('PATIENT COUNT');
        $response->assertSee('ACTIVE ALERTS');
        $response->assertSee('STAFF ON DUTY');
        
        
        $response->assertDontSee('<th>CAPACITY</th>', false);

        
        $response->assertDontSee('High-Risk Alerts <span style="font-size: 14px;', false);
        $response->assertDontSee('Mothers requiring immediate attention due to elevated blood pressure');

        
        
        $response->assertSee('Test Area Empty');
        $response->assertSee('Test Area Two Staff');
    }

    public function test_staff_on_duty_defaults_to_one_and_increases_with_more_midwives(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $areaEmpty = Area::create(['area_name' => 'Zero Midwife Area']);
        $areaMultiple = Area::create(['area_name' => 'Multi Midwife Area']);

        Midwife::create(['midwife_name' => 'Nurse 1', 'area_id' => $areaMultiple->area_id]);
        Midwife::create(['midwife_name' => 'Nurse 2', 'area_id' => $areaMultiple->area_id]);
        Midwife::create(['midwife_name' => 'Nurse 3', 'area_id' => $areaMultiple->area_id]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $clinicAreas = $response->viewData('clinicAreas');

        $emptyItem = $clinicAreas->firstWhere('area_id', $areaEmpty->area_id);
        $multiItem = $clinicAreas->firstWhere('area_id', $areaMultiple->area_id);

        $this->assertEquals(1, $emptyItem->staff_on_duty);
        $this->assertEquals(3, $multiItem->staff_on_duty);
    }

    public function test_midwife_can_view_dashboard_and_navigate(): void
    {
        $area = Area::create(['area_name' => 'Midwife Area']);
        $user = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);
        $midwife = Midwife::create([
            'midwife_name' => 'Midwife User',
            'area_id' => $area->area_id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('midwife.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Vaccine Reports');
        $response->assertSee('Triposha Book');
    }

    public function test_mother_can_view_dashboard_and_navigate(): void
    {
        $area = Area::create(['area_name' => 'Mother Area']);
        $midwife = Midwife::create(['midwife_name' => 'Midwife Care', 'area_id' => $area->area_id]);
        
        $user = User::factory()->create([
            'role' => 'mother',
            'status' => 'approved',
        ]);
        Mother::create([
            'mother_name' => 'Mother One',
            'phone_no' => '0712345678',
            'date_of_birth' => '1995-01-01',
            'address' => 'Sample Address',
            'midwife_id' => $midwife->midwife_id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('mother.dashboard'));
        $response->assertRedirect(route('mother.profile'));

        $profileResponse = $this->actingAs($user)->get(route('mother.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('My Profile');
        $profileResponse->assertSee('My Children');
        $profileResponse->assertDontSee('<span>Dashboard</span>', false);
    }
}

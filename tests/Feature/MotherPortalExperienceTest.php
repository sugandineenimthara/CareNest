<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotherPortalExperienceTest extends TestCase
{
    use RefreshDatabase;

    private User $motherUser;
    private Mother $mother;
    private Midwife $midwife;
    private Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Ja-Ela Central']);

        $midwifeUser = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);
        $this->midwife = Midwife::create([
            'midwife_name' => 'Nurse Perera',
            'area_id' => $this->area->area_id,
            'user_id' => $midwifeUser->id,
        ]);

        $this->motherUser = User::factory()->create([
            'name' => 'Nadeeka Silva',
            'email' => 'nadeeka@example.com',
            'password' => bcrypt('password123'),
            'role' => 'mother',
            'status' => 'approved',
        ]);

        $this->mother = Mother::create([
            'mother_name' => 'Nadeeka Silva',
            'phone_no' => '0771122334',
            'midwife_id' => $this->midwife->midwife_id,
            'date_of_birth' => '1994-05-10',
            'address' => '12/A, Hospital Road, Ja-Ela',
            'user_id' => $this->motherUser->id,
            'blood_pressure' => '120/80',
            'height' => 160,
            'weight' => 58,
        ]);
    }

    public function test_mother_redirected_to_profile_on_login_and_dashboard_removed(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => 'nadeeka@example.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect(route('mother.profile'));

        $dashboardResponse = $this->actingAs($this->motherUser)->get('/mother/dashboard');
        $dashboardResponse->assertRedirect(route('mother.profile'));

        $profileResponse = $this->actingAs($this->motherUser)->get(route('mother.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Nadeeka Silva');
        $profileResponse->assertSee('Personal Details');
        $profileResponse->assertSee('My Profile');
        $profileResponse->assertSee('My Children');
        $profileResponse->assertDontSee('<span>Dashboard</span>', false);
    }

    public function test_mother_can_view_her_children_in_my_children_page(): void
    {
        $child = Child::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Baby Senuri',
            'date_of_birth' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'gender' => 'Female',
            'birth_weight' => 3.2,
            'weight' => 5.1,
            'birth_length' => 50,
            'height' => 58,
            'bcg_vaccinated_at_birth' => true,
        ]);

        $response = $this->actingAs($this->motherUser)->get(route('mother.children.index'));
        $response->assertStatus(200);
        $response->assertSee('My Children');
        $response->assertSee('Baby Senuri');
        $response->assertSee('BCG Vaccinated');
        $response->assertSee('5.10 kg');
        $response->assertDontSee('Separated by Clinic Areas');
        $response->assertDontSee('Filter Area:');
        $response->assertDontSee('Register New Child');
    }

    public function test_mother_immunizations_shows_her_vaccines_and_children_details_separately(): void
    {
        Immunization::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'vaccine_name' => 'Tetanus Toxoid (TT)',
            'dose' => '1st Dose',
            'batch_no' => 'TT-BATCH-001',
            'immunization_date' => Carbon::now()->subWeeks(5)->format('Y-m-d'),
        ]);

        $child = Child::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Baby Senuri',
            'date_of_birth' => Carbon::now()->subMonths(2)->addDays(4)->format('Y-m-d'),
            'gender' => 'Female',
            'birth_weight' => 3.2,
            'bcg_vaccinated_at_birth' => true,
        ]);

        Immunization::create([
            'child_id' => $child->child_id,
            'midwife_id' => $this->midwife->midwife_id,
            'vaccine_name' => 'BCG',
            'dose' => 'Single Dose',
            'batch_no' => 'BCG-BATCH-888',
            'immunization_date' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->motherUser)->get(route('mother.immunizations.index'));
        $response->assertStatus(200);
        $response->assertSee('My Maternal Immunization Records');
        $response->assertSee('TT-BATCH-001');
        $response->assertSee('Tetanus Toxoid (TT) Dose 2');
        $response->assertSee('Ja-Ela District Hospital');
        $response->assertSee('09:00 AM');

        $response->assertSee("My Children's Immunization Details", false);
        $response->assertSee('Baby Senuri');
        $response->assertSee('BCG-BATCH-888');
        $response->assertSee('Penta 1');
    }

    public function test_upcoming_immunization_notification_before_a_week_with_date_time_and_venue(): void
    {
        $child = Child::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Baby Kasun',
            'date_of_birth' => Carbon::now()->subMonths(2)->addDays(4)->format('Y-m-d'),
            'gender' => 'Male',
            'birth_weight' => 3.5,
            'bcg_vaccinated_at_birth' => true,
        ]);

        $response = $this->actingAs($this->motherUser)->get(route('mother.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('My Notifications');
        $response->assertSee('Baby Kasun');
        $response->assertSee('Upcoming within 1 week');
        $response->assertSee('09:00 AM');
        $response->assertSee('Ja-Ela District Hospital');
    }
}

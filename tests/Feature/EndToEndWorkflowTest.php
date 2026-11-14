<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EndToEndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $midwifeUser;
    private Midwife $midwife;
    private User $motherUser;
    private Mother $mother;
    private Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create([
            'area_name' => 'Ja-Ela Central',
            'district' => 'Gampaha',
        ]);

        $this->admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@carenest.test',
            'password' => Hash::make('secret123'),
            'role' => 'admin',
            'approval_status' => 'approved',
        ]);

        $this->midwifeUser = User::create([
            'name' => 'Kanthi Perera',
            'email' => 'kanthi@carenest.test',
            'password' => Hash::make('secret123'),
            'role' => 'midwife',
            'approval_status' => 'approved',
        ]);

        $this->midwife = Midwife::create([
            'user_id' => $this->midwifeUser->id,
            'midwife_name' => 'Kanthi Perera',
            'area_id' => $this->area->area_id,
            'assigned_area' => 'Ja-Ela Central',
            'slmc_reg_no' => 'SLMC-90812',
            'phone' => '0771234567',
        ]);

        $this->motherUser = User::create([
            'name' => 'Sunethra Fernando',
            'email' => 'sunethra@carenest.test',
            'password' => Hash::make('secret123'),
            'role' => 'mother',
            'approval_status' => 'approved',
        ]);

        $this->mother = Mother::create([
            'user_id' => $this->motherUser->id,
            'mother_name' => 'Sunethra Fernando',
            'nic_no' => '199268400123',
            'area_id' => $this->area->area_id,
            'midwife_id' => $this->midwife->midwife_id,
            'phone' => '0719876543',
            'blood_group' => 'B+',
            'age' => 31,
            'date_of_birth' => '1995-05-14',
            'current_preg_no' => 1,
            'lmp_date' => Carbon::now()->subMonths(5)->format('Y-m-d'),
            'edd_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
        ]);

        \App\Models\PregnancyHistory::create([
            'mother_id' => $this->mother->mother_id,
            'is_delivered' => true,
            'delivery_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'no_living_children' => 0,
        ]);
    }

    public function test_admin_can_navigate_manage_children_and_record_vaccines(): void
    {
        $loginResponse = $this->post('/login', [
            'email' => $this->admin->email,
            'password' => 'secret123',
        ]);
        $loginResponse->assertRedirect(route('admin.dashboard'));

        $indexResponse = $this->actingAs($this->admin)->get(route('admin.children.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Children');

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.children.store'), [
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Baby Chamath',
            'gender' => 'Male',
            'date_of_birth' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'birth_weight' => 3.4,
            'birth_length' => 51,
            'bcg_vaccinated_at_birth' => 1,
            'bcg_batch_no' => 'BCG-E2E-01',
            'bcg_vaccinated_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
        ]);

        $child = Child::where('child_name', 'Baby Chamath')->first();
        $this->assertNotNull($child);
        $storeResponse->assertRedirect(route('admin.children.show', $child->child_id));

        $showResponse = $this->actingAs($this->admin)->get(route('admin.children.show', $child->child_id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Baby Chamath');

        $vaccineResponse = $this->actingAs($this->admin)->post(route('admin.immunizations.child.store'), [
            'child_id' => $child->child_id,
            'midwife_id' => $this->midwife->midwife_id,
            'vaccine_name' => 'Penta 1',
            'dose' => 'Single Dose',
            'batch_no' => 'PENTA-BATCH-99',
            'immunization_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
        ]);
        $vaccineResponse->assertRedirect();

        $this->assertDatabaseHas('immunizations', [
            'child_id' => $child->child_id,
            'vaccine_name' => 'Penta 1',
            'batch_no' => 'PENTA-BATCH-99',
        ]);
    }

    public function test_midwife_can_record_attendances_and_trigger_bp_alert(): void
    {
        $response = $this->actingAs($this->midwifeUser)->get(route('midwife.attendances.index'));
        $response->assertStatus(200);
        $response->assertSee('Clinic Attendances');

        $batchResponse = $this->actingAs($this->midwifeUser)->post(route('midwife.attendances.store.batch'), [
            'clinic_date' => Carbon::now()->format('Y-m-d'),
            'clinic_name' => 'Ja-Ela Mother Clinic',
            'clinic_type' => 'mother',
            'midwife_id' => $this->midwife->midwife_id,
            'attendees' => [
                [
                    'mother_id' => $this->mother->mother_id,
                    'blood_pressure' => '150/95',
                    'remarks' => 'High reading detected during morning visit',
                ],
            ],
        ]);
        $batchResponse->assertRedirect(route('midwife.attendances.index'));

        $this->assertDatabaseHas('attendances', [
            'mother_id' => $this->mother->mother_id,
            'blood_pressure' => '150/95',
            'clinic_type' => 'mother',
        ]);

        $alertsResponse = $this->actingAs($this->midwifeUser)->get(route('midwife.alerts.index'));
        $alertsResponse->assertStatus(200);
        $alertsResponse->assertSee('Sunethra Fernando');
        $alertsResponse->assertSee('150/95');
    }

    public function test_mother_portal_complete_workflow(): void
    {
        $child = Child::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Baby Nethmi',
            'date_of_birth' => Carbon::now()->subMonths(2)->addDays(4)->format('Y-m-d'),
            'gender' => 'Female',
            'birth_weight' => 3.1,
            'bcg_vaccinated_at_birth' => true,
        ]);

        $loginResponse = $this->post('/login', [
            'email' => $this->motherUser->email,
            'password' => 'secret123',
        ]);
        $loginResponse->assertRedirect(route('mother.profile'));

        $profileResponse = $this->actingAs($this->motherUser)->get(route('mother.profile'));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Sunethra Fernando');
        $profileResponse->assertDontSee('<span>Dashboard</span>', false);

        $childrenResponse = $this->actingAs($this->motherUser)->get(route('mother.children.index'));
        $childrenResponse->assertStatus(200);
        $childrenResponse->assertSee('Baby Nethmi');
        $childrenResponse->assertDontSee('Register New Child');

        $immunizationResponse = $this->actingAs($this->motherUser)->get(route('mother.immunizations.index'));
        $immunizationResponse->assertStatus(200);
        $immunizationResponse->assertSee('My Maternal Immunization Records');
        $immunizationResponse->assertSee('Ja-Ela District Hospital');
        $immunizationResponse->assertSee('09:00 AM');
        $immunizationResponse->assertSee("My Children's Immunization Details", false);

        $notifResponse = $this->actingAs($this->motherUser)->get(route('mother.notifications.index'));
        $notifResponse->assertStatus(200);
        $notifResponse->assertSee('Ja-Ela District Hospital');
        $notifResponse->assertSee('09:00 AM');
    }

    public function test_mother_registration_and_midwife_approval_flow(): void
    {
        $unlinkedMother = Mother::create([
            'user_id' => null,
            'mother_name' => 'Dilrukshi Perera',
            'nic_no' => '199578400456',
            'area_id' => $this->area->area_id,
            'midwife_id' => $this->midwife->midwife_id,
            'phone' => '0711122334',
            'blood_group' => 'A+',
            'age' => 29,
            'date_of_birth' => '1997-02-10',
            'current_preg_no' => 1,
            'lmp_date' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'edd_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
        ]);

        $regResponse = $this->post(route('register.mother'), [
            'mother_id' => $unlinkedMother->mother_id,
            'mother_name' => 'Dilrukshi Perera',
            'email' => 'dilrukshi.reg@carenest.test',
            'phone_no' => '0711122334',
            'password' => 'Pass1234!',
            'password_confirmation' => 'Pass1234!',
        ]);
        $regResponse->assertRedirect(route('login'));

        $newUser = User::where('email', 'dilrukshi.reg@carenest.test')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('pending', $newUser->status);

        $loginPending = $this->post('/login', [
            'email' => 'dilrukshi.reg@carenest.test',
            'password' => 'Pass1234!',
        ]);
        $loginPending->assertSessionHas('error');

        $approveResponse = $this->actingAs($this->midwifeUser)
            ->from(route('midwife.mother-requests.index'))
            ->post(route('midwife.mother-requests.approve', $newUser->id));
        $approveResponse->assertRedirect(route('midwife.mother-requests.index'));

        $newUser->refresh();
        $this->assertEquals('approved', $newUser->status);

        $loginApproved = $this->post('/login', [
            'email' => 'dilrukshi.reg@carenest.test',
            'password' => 'Pass1234!',
        ]);
        $loginApproved->assertRedirect(route('mother.profile'));
    }
}


<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MotherRegistrationAndApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected Area $area1;
    protected Area $area2;
    protected Midwife $midwife1;
    protected Midwife $midwife2;
    protected User $midwifeUser1;
    protected User $midwifeUser2;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area1 = Area::create(['area_name' => 'Kala-Eliya']);
        $this->area2 = Area::create(['area_name' => 'Wewala']);

        $this->midwifeUser1 = User::create([
            'name'     => 'Midwife Kala',
            'email'    => 'midwife1@carenest.com',
            'password' => Hash::make('secret123'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $this->midwife1 = Midwife::create([
            'midwife_name' => 'Sister Kala',
            'area_id'      => $this->area1->area_id,
            'user_id'      => $this->midwifeUser1->id,
        ]);

        $this->midwifeUser2 = User::create([
            'name'     => 'Midwife Wewala',
            'email'    => 'midwife2@carenest.com',
            'password' => Hash::make('secret123'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $this->midwife2 = Midwife::create([
            'midwife_name' => 'Sister Wewala',
            'area_id'      => $this->area2->area_id,
            'user_id'      => $this->midwifeUser2->id,
        ]);

        $this->adminUser = User::create([
            'name'     => 'Admin System',
            'email'    => 'admin@carenest.com',
            'password' => Hash::make('secret123'),
            'role'     => 'provider',
            'status'   => 'approved',
        ]);
    }

    public function test_mother_registers_with_unique_mother_id_and_awaits_midwife_approval(): void
    {
        $mother = Mother::create([
            'mother_name'   => 'Kamani Silva',
            'phone_no'      => '0712345678',
            'midwife_id'    => $this->midwife1->midwife_id,
            'date_of_birth' => '1996-04-12',
            'address'       => '12 Temple Road, Kala-Eliya',
        ]);

        $response = $this->post(route('register.mother'), [
            'mother_id'             => $mother->mother_id,
            'mother_name'           => 'Kamani Silva',
            'email'                 => 'kamani@example.com',
            'phone_no'              => '0712345678',
            'password'              => 'kamani1234',
            'password_confirmation' => 'kamani1234',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email'  => 'kamani@example.com',
            'role'   => 'mother',
            'status' => 'pending',
        ]);

        $mother->refresh();
        $this->assertNotNull($mother->user_id);

        $loginAttempt = $this->post('/login', [
            'email'    => 'kamani@example.com',
            'password' => 'kamani1234',
        ]);

        $loginAttempt->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_assigned_midwife_can_view_and_approve_mother_request(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Sunethra Perera',
            'midwife_id'  => $this->midwife1->midwife_id,
        ]);

        $motherUser = User::create([
            'name'     => 'Sunethra Perera',
            'email'    => 'sunethra@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'mother',
            'status'   => 'pending',
        ]);
        $mother->update(['user_id' => $motherUser->id]);

        $viewResponse = $this->actingAs($this->midwifeUser1)->get(route('midwife.mother-requests.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Sunethra Perera');
        $viewResponse->assertSee('#' . $mother->mother_id);

        $otherMidwifeApprove = $this->actingAs($this->midwifeUser2)
            ->post(route('midwife.mother-requests.approve', $motherUser->id));
        $otherMidwifeApprove->assertStatus(403);

        $approveResponse = $this->actingAs($this->midwifeUser1)
            ->post(route('midwife.mother-requests.approve', $motherUser->id));
        $approveResponse->assertRedirect();

        $motherUser->refresh();
        $this->assertEquals('approved', $motherUser->status);

        $this->post(route('logout'));

        $loginSuccess = $this->post('/login', [
            'email'    => 'sunethra@example.com',
            'password' => 'password123',
        ]);
        $loginSuccess->assertRedirect(route('mother.profile'));
        $this->assertAuthenticatedAs($motherUser);
    }

    public function test_sidebar_displays_mother_requests_and_manage_mothers_for_admin_and_midwife(): void
    {
        $adminResp = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $adminResp->assertStatus(200);
        $adminResp->assertSee('Mother Requests');
        $adminResp->assertSee('Manage Mothers');

        $midwifeResp = $this->actingAs($this->midwifeUser1)->get(route('midwife.dashboard'));
        $midwifeResp->assertStatus(200);
        $midwifeResp->assertSee('Mother Requests');
        $midwifeResp->assertSee('Manage Mothers');
    }
}

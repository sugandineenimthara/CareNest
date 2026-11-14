<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MidwifeApprovalAndAreaSharingTest extends TestCase
{
    use RefreshDatabase;

    protected Area $area1;
    protected Area $area2;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area1 = Area::create(['area_name' => 'Colombo Central']);
        $this->area2 = Area::create(['area_name' => 'Kandy Town']);

        $this->admin = User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@carenest.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'status'   => 'approved',
        ]);
    }

    public function test_midwife_registration_remains_pending_and_not_in_system_until_approved(): void
    {
        // 1. Midwife submits registration request
        $response = $this->post('/register/midwife', [
            'midwife_name'          => 'Sister Kamala',
            'email'                 => 'kamala@carenest.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'area_id'               => $this->area1->area_id,
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Check user is created with pending status
        $user = User::where('email', 'kamala@carenest.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pending', $user->status);

        // Crucial: Midwife model global scope hides pending midwife from general system
        $approvedMidwives = Midwife::all();
        $this->assertFalse($approvedMidwives->contains('user_id', $user->id));

        $areaMidwives = $this->area1->fresh()->midwives;
        $this->assertFalse($areaMidwives->contains('user_id', $user->id));

        // But admin can see the request in pending requests
        $adminResponse = $this->actingAs($this->admin)->get(route('admin.midwife-requests'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Sister Kamala');
        $adminResponse->assertSee('Colombo Central');
    }

    public function test_admin_rejecting_midwife_does_not_add_her_to_system_and_deletes_midwife_record(): void
    {
        // Register midwife
        $this->post('/register/midwife', [
            'midwife_name'          => 'Sister Kamala',
            'email'                 => 'kamala@carenest.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'area_id'               => $this->area1->area_id,
        ]);

        $user = User::where('email', 'kamala@carenest.com')->first();
        $this->assertNotNull($user);

        // Admin rejects her
        $rejectResponse = $this->actingAs($this->admin)
            ->post(route('admin.midwife-requests.reject', $user->id));

        $rejectResponse->assertRedirect();
        $rejectResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('rejected', $user->status);

        // Her midwife row is deleted and she is NEVER added to the system
        $this->assertDatabaseMissing('midwives', [
            'user_id' => $user->id,
        ]);

        $this->assertCount(0, Midwife::withoutGlobalScope('approved')->where('user_id', $user->id)->get());
        $this->assertCount(0, $this->area1->fresh()->midwives);
    }

    public function test_admin_approving_midwife_adds_her_to_system(): void
    {
        // Register midwife
        $this->post('/register/midwife', [
            'midwife_name'          => 'Sister Kamala',
            'email'                 => 'kamala@carenest.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'area_id'               => $this->area1->area_id,
        ]);

        $user = User::where('email', 'kamala@carenest.com')->first();

        // Admin approves her
        $approveResponse = $this->actingAs($this->admin)
            ->post(route('admin.midwife-requests.approve', $user->id));

        $approveResponse->assertRedirect();
        $approveResponse->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('approved', $user->status);

        // Now she is active in the system
        $approvedMidwives = Midwife::all();
        $this->assertTrue($approvedMidwives->contains('user_id', $user->id));

        $areaMidwives = $this->area1->fresh()->midwives;
        $this->assertTrue($areaMidwives->contains('user_id', $user->id));
    }

    public function test_two_midwives_in_same_area_appear_with_comma_and_share_single_mother_register(): void
    {
        // Create 2 approved midwives in area1 (Colombo Central)
        $user1 = User::create([
            'name'     => 'Sister Kamala',
            'email'    => 'kamala@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $midwife1 = Midwife::create([
            'midwife_name' => 'Sister Kamala',
            'area_id'      => $this->area1->area_id,
            'user_id'      => $user1->id,
        ]);

        $user2 = User::create([
            'name'     => 'Sister Hilda',
            'email'    => 'hilda@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $midwife2 = Midwife::create([
            'midwife_name' => 'Sister Hilda',
            'area_id'      => $this->area1->area_id,
            'user_id'      => $user2->id,
        ]);

        // Create a mother assigned to midwife1, and a mother assigned to midwife2
        $mother1 = Mother::create([
            'mother_name'   => 'Anula Perera',
            'phone_no'      => '0711111111',
            'midwife_id'    => $midwife1->midwife_id,
            'date_of_birth' => '1996-01-01',
        ]);
        $mother2 = Mother::create([
            'mother_name'   => 'Sunila Silva',
            'phone_no'      => '0722222222',
            'midwife_id'    => $midwife2->midwife_id,
            'date_of_birth' => '1998-02-02',
        ]);

        // 1. In mother register (/mothers)
        $response = $this->actingAs($this->admin)->get(route('admin.mothers.index'));
        $response->assertStatus(200);

        // Midwives appear comma-separated
        $response->assertSee('Sister Kamala, Sister Hilda');

        // Both mothers appear in the register under Colombo Central
        $response->assertSee('Anula Perera');
        $response->assertSee('Sunila Silva');

        // 2. Both midwives can view and access mothers in their shared area
        $this->actingAs($user1)->get(route('midwife.mothers.show', $mother2->mother_id))->assertStatus(200);
        $this->actingAs($user2)->get(route('midwife.mothers.show', $mother1->mother_id))->assertStatus(200);

        // 3. In forms, area is listed with comma-separated midwives
        $createResponse = $this->actingAs($this->admin)->get(route('admin.mothers.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Colombo Central (Sister Kamala, Sister Hilda)');

        $editResponse = $this->actingAs($this->admin)->get(route('admin.mothers.edit', $mother1->mother_id));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Colombo Central (Sister Kamala, Sister Hilda)');
    }
}

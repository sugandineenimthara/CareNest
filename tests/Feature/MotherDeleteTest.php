<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MotherDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected Area $area;
    protected Midwife $assignedMidwife;
    protected User $assignedMidwifeUser;
    protected Midwife $otherMidwife;
    protected User $otherMidwifeUser;
    protected User $adminUser;
    protected User $motherUser;
    protected Mother $mother;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Kala-Eliya']);

        $this->adminUser = User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'status'   => 'approved',
        ]);

        $this->assignedMidwifeUser = User::create([
            'name'     => 'Assigned Midwife',
            'email'    => 'midwife1@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $this->assignedMidwife = Midwife::create([
            'midwife_name' => 'Assigned Midwife',
            'area_id'      => $this->area->area_id,
            'user_id'      => $this->assignedMidwifeUser->id,
        ]);

        $this->otherMidwifeUser = User::create([
            'name'     => 'Other Midwife',
            'email'    => 'midwife2@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'midwife',
            'status'   => 'approved',
        ]);
        $this->otherArea = Area::create(['area_name' => 'Kala-Eliya-North']);
        $this->otherMidwife = Midwife::create([
            'midwife_name' => 'Other Midwife',
            'area_id'      => $this->otherArea->area_id,
            'user_id'      => $this->otherMidwifeUser->id,
        ]);

        $this->motherUser = User::create([
            'name'     => 'Mother User',
            'email'    => 'mother@carenest.com',
            'password' => Hash::make('password'),
            'role'     => 'mother',
            'status'   => 'approved',
        ]);

        $this->mother = Mother::create([
            'mother_name'   => 'Kasuni Perera',
            'phone_no'      => '0771234567',
            'midwife_id'    => $this->assignedMidwife->midwife_id,
            'date_of_birth' => '1995-05-15',
            'user_id'       => $this->motherUser->id,
        ]);

        // Add related child & pregnancy
        Child::create([
            'mother_id'     => $this->mother->mother_id,
            'midwife_id'    => $this->assignedMidwife->midwife_id,
            'child_name'    => 'Baby Perera',
            'gender'        => 'Male',
            'date_of_birth' => '2025-01-10',
            'birth_weight'  => 3.2,
        ]);

        PregnancyHistory::create([
            'mother_id'          => $this->mother->mother_id,
            'no_living_children' => 1,
        ]);
    }

    public function test_admin_can_view_delete_button_on_mother_profile()
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.mothers.show', $this->mother->mother_id));

        $response->assertStatus(200);
        $response->assertSee('Delete Mother');
    }

    public function test_assigned_midwife_can_view_delete_button_on_mother_profile()
    {
        $response = $this->actingAs($this->assignedMidwifeUser)
            ->get(route('midwife.mothers.show', $this->mother->mother_id));

        $response->assertStatus(200);
        $response->assertSee('Delete Mother');
    }

    public function test_unassigned_midwife_cannot_view_or_delete_mother()
    {
        // View is unauthorized for unassigned midwife per MotherPolicy
        $viewResponse = $this->actingAs($this->otherMidwifeUser)
            ->get(route('midwife.mothers.show', $this->mother->mother_id));
        $viewResponse->assertStatus(403);

        // Delete attempt must also be forbidden (403)
        $deleteResponse = $this->actingAs($this->otherMidwifeUser)
            ->delete(route('midwife.mothers.destroy', $this->mother->mother_id));
        $deleteResponse->assertStatus(403);

        $this->assertDatabaseHas('mothers', ['mother_id' => $this->mother->mother_id]);
    }

    public function test_mother_cannot_delete_her_own_record()
    {
        $response = $this->actingAs($this->motherUser)
            ->delete('/admin/mothers/' . $this->mother->mother_id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('mothers', ['mother_id' => $this->mother->mother_id]);
    }

    public function test_assigned_midwife_can_delete_mother()
    {
        $response = $this->actingAs($this->assignedMidwifeUser)
            ->delete(route('midwife.mothers.destroy', $this->mother->mother_id));

        $response->assertRedirect(route('midwife.mothers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('mothers', ['mother_id' => $this->mother->mother_id]);
        $this->assertDatabaseMissing('users', ['id' => $this->motherUser->id]);
        $this->assertDatabaseMissing('children', ['child_name' => 'Baby Perera']);
    }

    public function test_admin_can_delete_mother()
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('admin.mothers.destroy', $this->mother->mother_id));

        $response->assertRedirect(route('admin.mothers.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('mothers', ['mother_id' => $this->mother->mother_id]);
        $this->assertDatabaseMissing('users', ['id' => $this->motherUser->id]);
        $this->assertDatabaseMissing('children', ['child_name' => 'Baby Perera']);
    }
}

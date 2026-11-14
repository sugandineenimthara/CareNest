<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Child;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildDeleteTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $midwifeUser;
    private User $otherMidwifeUser;
    private User $motherUser;
    private Midwife $midwife;
    private Midwife $otherMidwife;
    private Mother $mother;
    private Child $child;
    private Area $area;
    private Area $otherArea;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Kala-Eliya']);
        $this->otherArea = Area::create(['area_name' => 'Ja-Ela']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $this->midwifeUser = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);

        $this->midwife = Midwife::create([
            'midwife_name' => 'Kumari Perera',
            'email' => $this->midwifeUser->email,
            'user_id' => $this->midwifeUser->id,
            'area_id' => $this->area->area_id,
            'status' => 'approved',
        ]);

        $this->otherMidwifeUser = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);

        $this->otherMidwife = Midwife::create([
            'midwife_name' => 'Sita Silva',
            'email' => $this->otherMidwifeUser->email,
            'user_id' => $this->otherMidwifeUser->id,
            'area_id' => $this->otherArea->area_id,
            'status' => 'approved',
        ]);

        $this->motherUser = User::factory()->create([
            'role' => 'mother',
            'status' => 'approved',
        ]);

        $this->mother = Mother::create([
            'mother_name' => 'Nadeesha Dilhani',
            'midwife_id' => $this->midwife->midwife_id,
            'user_id' => $this->motherUser->id,
        ]);

        $this->child = Child::create([
            'mother_id' => $this->mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Kasun Fernando',
            'gender' => 'Male',
            'date_of_birth' => '2024-05-10',
            'birth_weight' => 3.2,
        ]);
    }

    public function test_admin_can_view_delete_button_on_child_profile(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/children/{$this->child->child_id}");
        $response->assertStatus(200);
        $response->assertSee('Delete Child');
        $response->assertSee('openDeleteChildModal');
    }

    public function test_assigned_midwife_can_view_delete_button_on_child_profile(): void
    {
        $response = $this->actingAs($this->midwifeUser)->get("/midwife/children/{$this->child->child_id}");
        $response->assertStatus(200);
        $response->assertSee('Delete Child');
        $response->assertSee('openDeleteChildModal');
    }

    public function test_unassigned_midwife_from_different_area_cannot_view_or_delete_child(): void
    {
        // View should be forbidden or not contain delete
        $viewResponse = $this->actingAs($this->otherMidwifeUser)->get("/midwife/children/{$this->child->child_id}");
        $viewResponse->assertStatus(403);

        // Delete attempt should be forbidden
        $deleteResponse = $this->actingAs($this->otherMidwifeUser)->delete("/midwife/children/{$this->child->child_id}");
        $deleteResponse->assertStatus(403);

        $this->assertDatabaseHas('children', ['child_id' => $this->child->child_id]);
    }

    public function test_mother_cannot_delete_child(): void
    {
        $response = $this->actingAs($this->motherUser)->get("/mother/children/{$this->child->child_id}");
        $response->assertStatus(200);
        $response->assertDontSee('Delete Child');
        $response->assertDontSee('openDeleteChildModal');
    }

    public function test_assigned_midwife_can_delete_child(): void
    {
        $response = $this->actingAs($this->midwifeUser)->delete("/midwife/children/{$this->child->child_id}");
        $response->assertRedirect('/midwife/children');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('children', ['child_id' => $this->child->child_id]);
    }

    public function test_admin_can_delete_child(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/children/{$this->child->child_id}");
        $response->assertRedirect('/admin/children');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('children', ['child_id' => $this->child->child_id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\PreviousPregnancyHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MotherPregnancyLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $midwifeUser;
    private Midwife $midwife;
    private Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Kala-Eliya']);

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
    }

    public function test_mother_registration_creates_active_current_pregnancy(): void
    {
        $response = $this->actingAs($this->midwifeUser)->post('/midwife/mothers', [
            'mother_name' => 'Nadeesha Dilhani',
            'phone_no' => '0711234567',
            'midwife_id' => $this->midwife->midwife_id,
            'date_of_birth' => '1995-05-15',
            'last_menstrual_period' => '2026-01-01',
            'expected_date_of_delivery' => '2026-10-08',
            'no_of_weeks_pregnant_at_registration' => 8,
            'no_living_children' => 0,
        ]);

        $response->assertRedirect('/midwife/mothers');

        $mother = Mother::where('mother_name', 'Nadeesha Dilhani')->first();
        $this->assertNotNull($mother);

        $activePregnancy = $mother->activePregnancy();
        $this->assertNotNull($activePregnancy);
        $this->assertFalse((bool)$activePregnancy->is_delivered);
        $this->assertEquals('2026-01-01', $activePregnancy->last_menstrual_period->format('Y-m-d'));
        $this->assertEquals('2026-10-08', $activePregnancy->expected_date_of_delivery->format('Y-m-d'));
        $this->assertEquals(0, $mother->previousPregnancyHistories()->count());
    }

    public function test_recording_delivery_marks_pregnancy_delivered_and_adds_to_pregnancy_history(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Nadeesha Dilhani',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $pregnancy = PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'no_living_children' => 0,
            'last_menstrual_period' => '2025-01-01',
            'expected_date_of_delivery' => '2025-10-08',
            'is_delivered' => false,
        ]);

        $response = $this->actingAs($this->midwifeUser)->post("/midwife/mothers/{$mother->mother_id}/delivery", [
            'delivery_date' => '2025-10-05',
            'gender' => 'Female',
            'birth_weight' => 3.1,
            'results' => 'Live Birth',
            'place_of_pregnancy' => 'Base Hospital',
        ]);

        $response->assertRedirect(route('midwife.children.create', [
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2025-10-05',
            'gender' => 'Female',
            'birth_weight' => 3.1,
        ]));

        $pregnancy->refresh();
        $this->assertTrue((bool)$pregnancy->is_delivered);
        $this->assertEquals('2025-10-05', $pregnancy->delivery_date->format('Y-m-d'));

        // Check PreviousPregnancyHistory
        $this->assertDatabaseHas('previous_pregnancy_histories', [
            'mother_id' => $mother->mother_id,
            'gender' => 'Female',
            'results' => 'Live Birth',
            'place_of_pregnancy' => 'Base Hospital',
        ]);

        // Active pregnancy is now null
        $this->assertNull($mother->activePregnancy());
        // Current pregnancy returns the delivered one
        $this->assertNotNull($mother->currentPregnancy());
        $this->assertTrue((bool)$mother->currentPregnancy()->is_delivered);
    }

    public function test_editing_profile_with_next_pregnancy_records_new_current_pregnancy_and_preserves_history(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Nadeesha Dilhani',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        // 1st Pregnancy was delivered
        PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'no_living_children' => 0,
            'last_menstrual_period' => '2024-01-01',
            'expected_date_of_delivery' => '2024-10-08',
            'is_delivered' => true,
            'delivery_date' => '2024-10-05',
        ]);

        PreviousPregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2024-10-05',
            'birth_weight' => 3.2,
            'gender' => 'Male',
            'which_pregnancy' => '1st Pregnancy',
            'results' => 'Live Birth',
            'place_of_pregnancy' => 'Hospital',
        ]);

        // Edit profile to record 2nd pregnancy
        $response = $this->actingAs($this->midwifeUser)->put("/midwife/mothers/{$mother->mother_id}", [
            'mother_name' => 'Nadeesha Dilhani',
            'midwife_id' => $this->midwife->midwife_id,
            'last_menstrual_period' => '2026-02-01',
            'expected_date_of_delivery' => '2026-11-08',
            'no_of_weeks_pregnant_at_registration' => 10,
            'no_living_children' => 1,
            'age_of_youngest_child' => 2,
        ]);

        $response->assertRedirect(route('midwife.mothers.show', $mother->mother_id));

        // There should now be 2 PregnancyHistories: 1 delivered, 1 active
        $this->assertEquals(2, $mother->pregnancyHistories()->count());

        $newActive = $mother->activePregnancy();
        $this->assertNotNull($newActive);
        $this->assertFalse((bool)$newActive->is_delivered);
        $this->assertEquals('2026-02-01', $newActive->last_menstrual_period->format('Y-m-d'));
        $this->assertEquals('2026-11-08', $newActive->expected_date_of_delivery->format('Y-m-d'));
        $this->assertEquals(1, $newActive->no_living_children);

        // Previous pregnancy history remains preserved
        $this->assertEquals(1, $mother->previousPregnancyHistories()->count());
        $prev = $mother->previousPregnancyHistories()->first();
        $this->assertEquals('1st Pregnancy', $prev->which_pregnancy);
        $this->assertEquals('Male', $prev->gender);
    }

    public function test_child_can_only_be_registered_after_mother_delivery(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Samanthi Fernando',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $currentPregnancy = PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'no_living_children' => 0,
            'last_menstrual_period' => '2025-02-01',
            'expected_date_of_delivery' => '2025-11-08',
            'is_delivered' => false,
        ]);

        // 1. Attempt to add child while mother is actively pregnant (undelivered) -> fails validation
        $failResponse = $this->actingAs($this->midwifeUser)->post('/midwife/children', [
            'mother_id' => $mother->mother_id,
            'child_name' => 'Baby Fernando',
            'gender' => 'Male',
            'date_of_birth' => '2025-11-05',
            'birth_weight' => 3.4,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);

        $failResponse->assertSessionHasErrors(['mother_id']);
        $this->assertEquals(0, \App\Models\Child::count());

        // 2. Record delivery note for this mother
        $deliveryResponse = $this->actingAs($this->midwifeUser)->post("/midwife/mothers/{$mother->mother_id}/delivery", [
            'delivery_date' => '2025-11-05',
            'gender' => 'Male',
            'birth_weight' => 3.4,
            'results' => 'Live Birth',
            'place_of_pregnancy' => 'Hospital/Clinic',
        ]);
        $deliveryResponse->assertRedirect();

        $currentPregnancy->refresh();
        $this->assertTrue((bool)$currentPregnancy->is_delivered);
        $this->assertEquals('2025-11-05', $currentPregnancy->delivery_date->format('Y-m-d'));

        // 3. Now child registration succeeds
        $successResponse = $this->actingAs($this->midwifeUser)->post('/midwife/children', [
            'mother_id' => $mother->mother_id,
            'child_name' => 'Baby Fernando',
            'gender' => 'Male',
            'date_of_birth' => '2025-11-05',
            'birth_weight' => 3.4,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);
        $successResponse->assertSessionHasNoErrors();
        $this->assertEquals(1, \App\Models\Child::count());

        $currentPregnancy->refresh();
        $this->assertEquals(1, $currentPregnancy->no_living_children);
    }

    public function test_mother_profile_displays_her_children_profiles(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Kavindi Perera',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $child1 = \App\Models\Child::create([
            'mother_id' => $mother->mother_id,
            'child_name' => 'Amaya Fernando',
            'gender' => 'Female',
            'date_of_birth' => '2023-04-10',
            'birth_weight' => 3.1,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);

        $child2 = \App\Models\Child::create([
            'mother_id' => $mother->mother_id,
            'child_name' => 'Dineth Fernando',
            'gender' => 'Male',
            'date_of_birth' => '2025-06-15',
            'birth_weight' => 3.5,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);

        $response = $this->actingAs($this->midwifeUser)->get("/midwife/mothers/{$mother->mother_id}");
        $response->assertStatus(200);

        // Assert children section and names are present
        $response->assertSee('Children (2)');
        $response->assertSee('Amaya Fernando');
        $response->assertSee('Dineth Fernando');
        $response->assertSee(route('midwife.children.show', $child1->child_id));
        $response->assertSee(route('midwife.children.show', $child2->child_id));
    }
}


<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\Immunization;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\PregnancyHistory;
use App\Models\PreviousPregnancyHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChildRegistrationAndCascadeDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $midwifeUser;
    protected Midwife $midwife;
    protected Area $area;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['area_name' => 'Colombo Central']);

        $this->midwifeUser = User::factory()->create([
            'role' => 'midwife',
            'status' => 'approved',
        ]);

        $this->midwife = Midwife::create([
            'midwife_name' => 'Kumari Jayasinghe',
            'email' => $this->midwifeUser->email,
            'user_id' => $this->midwifeUser->id,
            'area_id' => $this->area->area_id,
            'status' => 'approved',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved',
        ]);
    }

    public function test_when_mother_first_pregnant_she_is_registered_with_active_pregnancy(): void
    {
        $response = $this->actingAs($this->midwifeUser)->post('/midwife/mothers', [
            'mother_name' => 'Anoma Perera',
            'phone_no' => '0712345678',
            'midwife_id' => $this->midwife->midwife_id,
            'expected_date_of_delivery' => '2027-01-15',
            'last_menstrual_period' => '2026-04-10',
            'no_of_weeks_pregnant_at_registration' => 12,
        ]);

        $response->assertRedirect('/midwife/mothers');

        $mother = Mother::where('mother_name', 'Anoma Perera')->first();
        $this->assertNotNull($mother);

        // Active pregnancy history must be created
        $active = $mother->activePregnancy();
        $this->assertNotNull($active);
        $this->assertFalse((bool)$active->is_delivered);
        $this->assertEquals(0, $active->no_living_children);
        $this->assertNull($active->age_of_youngest_child);
        $this->assertFalse($mother->canAddChild());
    }

    public function test_child_can_only_be_added_after_mother_delivery(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Dilini Samarakoon',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $pregnancy = PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => false,
            'no_living_children' => 0,
        ]);

        // Attempting to register child while pregnant fails
        $failResponse = $this->actingAs($this->midwifeUser)->post('/midwife/children', [
            'mother_id' => $mother->mother_id,
            'child_name' => 'Baby Samarakoon',
            'gender' => 'Female',
            'date_of_birth' => '2026-11-10',
            'birth_weight' => 3.1,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);

        $failResponse->assertSessionHasErrors(['mother_id']);
        $this->assertEquals(0, Child::count());

        // Record delivery
        $this->actingAs($this->midwifeUser)->post("/midwife/mothers/{$mother->mother_id}/delivery", [
            'delivery_date' => '2026-11-10',
            'gender' => 'Female',
            'birth_weight' => 3.1,
            'results' => 'Live Birth',
            'place_of_pregnancy' => 'Colombo Hospital',
        ]);

        $pregnancy->refresh();
        $this->assertTrue((bool)$pregnancy->is_delivered);
        $this->assertTrue($mother->fresh()->canAddChild());

        // Now child can be registered
        $successResponse = $this->actingAs($this->midwifeUser)->post('/midwife/children', [
            'mother_id' => $mother->mother_id,
            'child_name' => 'Baby Samarakoon',
            'gender' => 'Female',
            'date_of_birth' => '2026-11-10',
            'birth_weight' => 3.1,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
        ]);

        $successResponse->assertSessionHasNoErrors();
        $this->assertEquals(1, Child::count());

        $pregnancy->refresh();
        $this->assertEquals(1, $pregnancy->no_living_children);
    }

    public function test_when_mother_is_deleted_her_children_records_are_deleted_too(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Sanduni Silva',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $child1 = Child::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Child One',
            'gender' => 'Male',
            'date_of_birth' => '2024-01-01',
            'birth_weight' => 3.0,
        ]);

        $child2 = Child::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Child Two',
            'gender' => 'Female',
            'date_of_birth' => '2025-06-01',
            'birth_weight' => 2.9,
        ]);

        Immunization::create([
            'child_id' => $child1->child_id,
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'batch_no' => 'BCG-001',
            'vaccine_name' => 'BCG',
        ]);

        // Midwife deletes mother
        $response = $this->actingAs($this->midwifeUser)->delete(route('midwife.mothers.destroy', $mother->mother_id));
        $response->assertRedirect(route('midwife.mothers.index'));

        $this->assertDatabaseMissing('mothers', ['mother_id' => $mother->mother_id]);
        $this->assertDatabaseMissing('children', ['child_id' => $child1->child_id]);
        $this->assertDatabaseMissing('children', ['child_id' => $child2->child_id]);
        $this->assertDatabaseMissing('immunizations', ['child_id' => $child1->child_id]);
    }

    public function test_when_child_is_deleted_mother_profile_relevant_fields_are_deleted(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Chathuri Fernando',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        // Record delivered pregnancy
        $pregnancy = PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => true,
            'delivery_date' => '2025-05-20',
            'no_living_children' => 1,
            'age_of_youngest_child' => 1,
        ]);

        $prevPregnancy = PreviousPregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2025-05-20',
            'birth_weight' => 3.3,
            'gender' => 'Male',
            'which_pregnancy' => '1st Pregnancy',
            'results' => 'Live Birth',
        ]);

        $child = Child::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Kusal Fernando',
            'gender' => 'Male',
            'date_of_birth' => '2025-05-20',
            'birth_weight' => 3.3,
        ]);

        Immunization::create([
            'child_id' => $child->child_id,
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'batch_no' => 'BCG-002',
            'vaccine_name' => 'BCG',
        ]);

        // Delete the child
        $response = $this->actingAs($this->midwifeUser)->delete(route('midwife.children.destroy', $child->child_id));
        $response->assertRedirect(route('midwife.children.index'));

        // Child & its immunization are gone
        $this->assertDatabaseMissing('children', ['child_id' => $child->child_id]);
        $this->assertDatabaseMissing('immunizations', ['child_id' => $child->child_id]);

        // Relevant mother profile fields are cleaned up:
        // 1. Previous pregnancy history entry for that child is deleted
        $this->assertDatabaseMissing('previous_pregnancy_histories', [
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2025-05-20',
        ]);

        // 2. Mother's pregnancy history living children count reset to 0
        $pregnancy->refresh();
        $this->assertEquals(0, $pregnancy->no_living_children);
        $this->assertNull($pregnancy->age_of_youngest_child);

        // 3. Delivery date reset since no children remain for that delivery
        $this->assertFalse((bool)$pregnancy->is_delivered);
        $this->assertNull($pregnancy->delivery_date);

        // 4. Mother profile shows 0 children
        $this->assertEquals(0, $mother->fresh()->children()->count());
    }

    public function test_when_one_of_multiple_children_is_deleted_mother_fields_recalculated(): void
    {
        $mother = Mother::create([
            'mother_name' => 'Nirosha Perera',
            'midwife_id' => $this->midwife->midwife_id,
        ]);

        $pregnancy = PregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'is_delivered' => true,
            'delivery_date' => '2026-01-01',
            'no_living_children' => 2,
            'age_of_youngest_child' => 0,
        ]);

        $olderChild = Child::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Older Child',
            'gender' => 'Female',
            'date_of_birth' => '2023-01-01',
            'birth_weight' => 3.0,
        ]);

        $youngerChild = Child::create([
            'mother_id' => $mother->mother_id,
            'midwife_id' => $this->midwife->midwife_id,
            'area_id' => $this->area->area_id,
            'child_name' => 'Younger Child',
            'gender' => 'Male',
            'date_of_birth' => '2026-01-01',
            'birth_weight' => 3.2,
        ]);

        PreviousPregnancyHistory::create([
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2026-01-01',
            'birth_weight' => 3.2,
            'gender' => 'Male',
            'which_pregnancy' => '2nd Pregnancy',
            'results' => 'Live Birth',
        ]);

        // Delete the younger child
        $this->actingAs($this->midwifeUser)->delete(route('midwife.children.destroy', $youngerChild->child_id));

        $pregnancy->refresh();
        // Living children count is now 1 (the remaining older child)
        $this->assertEquals(1, $pregnancy->no_living_children);
        // Youngest child age is now the age of older child (born 2023)
        $this->assertNotNull($pregnancy->age_of_youngest_child);
        $this->assertGreaterThanOrEqual(2, $pregnancy->age_of_youngest_child);

        // Younger child's previous pregnancy history is deleted
        $this->assertDatabaseMissing('previous_pregnancy_histories', [
            'mother_id' => $mother->mother_id,
            'date_of_birth' => '2026-01-01',
        ]);
    }
}

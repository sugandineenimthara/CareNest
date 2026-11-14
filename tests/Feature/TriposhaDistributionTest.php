<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Midwife;
use App\Models\Mother;
use App\Models\Child;
use App\Models\TriposhaBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TriposhaDistributionTest extends TestCase
{
    use RefreshDatabase;

    public function test_triposha_batch_store_redirects_correctly_without_undefined_route_error(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = Area::create(['area_name' => 'Wewala']);
        $midwife = Midwife::create(['midwife_name' => 'Kamala', 'area_id' => $area->area_id]);
        $mother = Mother::create(['mother_name' => 'Sugandi', 'midwife_id' => $midwife->midwife_id]);

        $response = $this->actingAs($admin)->post(route('admin.triposha.store.batch'), [
            'issuing_date' => '2026-11-14',
            'session_label' => 'Distribution Session 01',
            'opening_stock' => 500,
            'rows' => [
                [
                    'recipient_type' => 'Mother',
                    'mother_id' => $mother->mother_id,
                    'no_of_packets' => 2,
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.triposha.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('triposha_books', [
            'name_of_mother' => 'Sugandi',
            'no_of_packets' => 2,
            'session_label' => 'Distribution Session 01',
        ]);
    }

    public function test_distribution_sessions_count_reflects_conducted_sessions_not_record_rows(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = Area::create(['area_name' => 'Wewala']);
        $midwife = Midwife::create(['midwife_name' => 'Kamala', 'area_id' => $area->area_id]);
        $mother1 = Mother::create(['mother_name' => 'Sugandi', 'midwife_id' => $midwife->midwife_id]);
        $mother2 = Mother::create(['mother_name' => 'Amali', 'midwife_id' => $midwife->midwife_id]);

        // Create 2 records under the SAME session
        TriposhaBook::create([
            'mother_id' => $mother1->mother_id,
            'midwife_id' => $midwife->midwife_id,
            'no_of_packets' => 2,
            'issuing_date' => '2026-11-14',
            'recipient_type' => 'Mother',
            'name_of_mother' => 'Sugandi',
            'session_label' => 'Distribution Session 01',
            'opening_stock' => 500,
        ]);
        TriposhaBook::create([
            'mother_id' => $mother2->mother_id,
            'midwife_id' => $midwife->midwife_id,
            'no_of_packets' => 2,
            'issuing_date' => '2026-11-14',
            'recipient_type' => 'Mother',
            'name_of_mother' => 'Amali',
            'session_label' => 'Distribution Session 01',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.triposha.index'));
        $response->assertStatus(200);

        // totalSessionsEver should be 1, not 2
        $response->assertViewHas('totalSessionsEver', 1);
        $response->assertViewHas('totalPacketsEver', 4);
    }

    public function test_after_one_distribution_saved_remaining_stock_is_added_to_opening_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $area = Area::create(['area_name' => 'Wewala']);
        $midwife = Midwife::create(['midwife_name' => 'Kamala', 'area_id' => $area->area_id]);
        $mother = Mother::create(['mother_name' => 'Sugandi', 'midwife_id' => $midwife->midwife_id]);

        // Save first distribution: opening 500, distributes 8 (4 records of 2)
        $this->actingAs($admin)->post(route('admin.triposha.store.batch'), [
            'issuing_date' => '2026-11-14',
            'session_label' => 'Distribution Session 01',
            'opening_stock' => 500,
            'rows' => [
                ['recipient_type' => 'Mother', 'mother_id' => $mother->mother_id, 'no_of_packets' => 8],
            ],
        ]);

        // Remaining stock should be 500 - 8 = 492
        // Visit index page: opening stock should be pre-filled with 492
        $response = $this->actingAs($admin)->get(route('admin.triposha.index'));
        $response->assertStatus(200);
        $response->assertViewHas('defaultOpeningStock', 492);
        $response->assertSee('value="492"', false);

        // Next batch automatically inherits 492 if opening_stock is left empty
        $this->actingAs($admin)->post(route('admin.triposha.store.batch'), [
            'issuing_date' => '2026-11-14',
            'session_label' => 'Distribution Session 02',
            'rows' => [
                ['recipient_type' => 'Mother', 'mother_id' => $mother->mother_id, 'no_of_packets' => 2],
            ],
        ]);

        $this->assertDatabaseHas('triposha_books', [
            'session_label' => 'Distribution Session 02',
            'opening_stock' => 492,
        ]);
    }
}

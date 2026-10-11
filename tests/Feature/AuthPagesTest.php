<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Welcome Back');
    }

    public function test_registration_page_renders(): void
    {
        $response = $this->get(route('register.mother'));
        $response->assertStatus(200);
        $response->assertSee('Mother Registration');
    }

    public function test_user_can_register_as_mother(): void
    {
        $area = \App\Models\Area::create([
            'area_name' => 'Colombo Central',
            'district' => 'Colombo',
            'province' => 'Western',
        ]);

        $midwifeUser = User::create([
            'name' => 'Midwife User',
            'email' => 'midwife_test@carenest.com',
            'password' => bcrypt('password123'),
            'role' => 'midwife',
            'status' => 'approved',
        ]);

        $midwife = \App\Models\Midwife::create([
            'midwife_name' => 'Midwife User',
            'user_id' => $midwifeUser->id,
            'area_id' => $area->area_id,
        ]);

        $response = $this->post(route('register.mother'), [
            'mother_name' => 'Dilhani Perera',
            'email' => 'dilhani@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'phone_no' => '0771234567',
            'date_of_birth' => '1995-05-15',
            'address' => '123 Main St, Colombo',
            'midwife_id' => $midwife->midwife_id,
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'email' => 'dilhani@example.com',
            'role' => 'mother',
        ]);
    }
}

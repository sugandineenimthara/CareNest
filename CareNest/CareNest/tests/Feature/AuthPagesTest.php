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
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Start your');
    }

    public function test_user_can_register_as_mother(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dilhani Perera',
            'email' => 'dilhani@example.com',
            'password' => 'secret123',
            'role' => 'mother',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'dilhani@example.com',
            'role' => 'mother',
        ]);
    }
}

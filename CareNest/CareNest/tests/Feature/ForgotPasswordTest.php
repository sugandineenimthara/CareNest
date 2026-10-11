<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_renders(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Forgot Password?');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create([
            'email' => 'mother@example.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'mother@example.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'mother@example.com',
        ]);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'mother@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $token = 'test-reset-token';
        DB::table('password_reset_tokens')->insert([
            'email' => 'mother@example.com',
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'mother@example.com',
            'password' => 'newsecretpassword',
            'password_confirmation' => 'newsecretpassword',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newsecretpassword', $user->fresh()->password));
    }
}

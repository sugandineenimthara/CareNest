<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_change_password()
    {
        $response = $this->get(route('password.change'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_change_password_page()
    {
        $user = User::factory()->create([
            'role' => 'provider',
            'password' => Hash::make('oldpassword123'),
        ]);

        $response = $this->actingAs($user)->get(route('password.change'));
        $response->assertStatus(200);
        $response->assertSee('Change Password');
        $response->assertSee('Admin'); // Displays 'Admin' for provider/admin role
    }

    public function test_verify_current_password_with_wrong_password_fails()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->actingAs($user)->postJson(route('password.verify_current'), [
            'current_password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_verify_current_password_with_correct_password_succeeds()
    {
        $user = User::factory()->create([
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->actingAs($user)->postJson(route('password.verify_current'), [
            'current_password' => 'correctpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_update_password_with_correct_current_password_and_confirmation()
    {
        $user = User::factory()->create([
            'password' => Hash::make('myoldpassword'),
        ]);

        $response = $this->actingAs($user)->post(route('password.update_auth'), [
            'current_password' => 'myoldpassword',
            'password' => 'mynewpassword123',
            'password_confirmation' => 'mynewpassword123',
        ]);

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('success', 'Your password has been changed successfully!');

        $user->refresh();
        $this->assertTrue(Hash::check('mynewpassword123', $user->password));
    }

    public function test_failed_current_password_in_form_submission()
    {
        $user = User::factory()->create([
            'password' => Hash::make('myoldpassword'),
        ]);

        $response = $this->actingAs($user)->post(route('password.update_auth'), [
            'current_password' => 'wrongpassword',
            'password' => 'mynewpassword123',
            'password_confirmation' => 'mynewpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $response->assertSessionHas('failed_current_password', true);
    }

    public function test_forgot_password_identity_authentication_redirects_to_change_password()
    {
        $user = User::factory()->create([
            'email' => 'admin_test@carenest.com',
            'role' => 'provider',
            'password' => Hash::make('secret123'),
        ]);

        // Request reset link
        $this->actingAs($user)->post(route('password.email'), [
            'email' => $user->email,
            'from' => 'change-password',
        ]);

        $tokenRecord = DB::table('password_reset_tokens')->where('email', $user->email)->first();
        $this->assertNotNull($tokenRecord);

        // Click reset link (which authenticates identity)
        // Note: the raw token in link matches hash in DB
        // Let's set a known token:
        $rawToken = 'test-token-123456';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($rawToken), 'created_at' => now()]
        );

        $response = $this->actingAs($user)->get(route('password.reset', [
            'token' => $rawToken,
            'email' => $user->email,
            'from_change_password' => 1,
        ]));

        $response->assertRedirect(route('password.change'));
        $response->assertSessionHas('identity_authenticated', true);

        // Now update password without providing current_password
        $updateResponse = $this->actingAs($user)->post(route('password.update_auth'), [
            'password' => 'brandnewpassword999',
            'password_confirmation' => 'brandnewpassword999',
        ]);

        $updateResponse->assertRedirect(route('password.change'));
        $updateResponse->assertSessionHas('success', 'Your password has been changed successfully!');

        $user->refresh();
        $this->assertTrue(Hash::check('brandnewpassword999', $user->password));
    }

    public function test_all_admin_pages_render_with_admin_header()
    {
        $user = User::factory()->create([
            'role' => 'provider',
            'password' => Hash::make('secret'),
        ]);

        $routes = [
            'admin.dashboard',
            'admin.midwife-requests',
            'admin.midwives.index',
            'admin.mothers.index',
            'admin.children.index',
            'admin.immunizations.index',
            'admin.alerts.index',
            'admin.triposha.index',
            'admin.attendances.index',
            'password.change',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($user)->get(route($routeName));
            $this->assertEquals(200, $response->getStatusCode(), "Failed rendering route: {$routeName}");
            $response->assertSee('Admin');
            $response->assertSee('Change Password');
            $response->assertSee('Log Out');
        }
    }
}

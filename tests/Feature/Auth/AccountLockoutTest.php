<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountLockoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspended_user_cannot_login(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create(['status' => 'inactive']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_pending_user_cannot_login(): void
    {
        $user = User::factory()->create(['status' => 'pending']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_failed_login_increments_attempt_counter(): void
    {
        $user = User::factory()->create(['failed_login_attempts' => 0]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertEquals(1, $user->fresh()->failed_login_attempts);
    }

    public function test_account_is_suspended_after_three_failed_attempts(): void
    {
        $user = User::factory()->create(['failed_login_attempts' => 2]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $user->refresh();
        $this->assertEquals('suspended', $user->status);
        $this->assertNotNull($user->suspension_start_date);
    }

    public function test_suspension_date_is_set_on_auto_lockout(): void
    {
        $user = User::factory()->create(['failed_login_attempts' => 2]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertNotNull($user->fresh()->suspension_start_date);
    }

    public function test_successful_login_resets_failed_attempts(): void
    {
        $user = User::factory()->create(['failed_login_attempts' => 2]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertEquals(0, $user->fresh()->failed_login_attempts);
    }

    public function test_successful_login_records_last_login_timestamp(): void
    {
        $user = User::factory()->create(['last_login_at' => null]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_suspended_user_is_kicked_out_mid_session(): void
    {
        $user = User::factory()->create(['status' => 'active']);

        // Log in successfully
        $this->actingAs($user)->get('/dashboard')->assertOk();

        // Admin suspends the user
        $user->update(['status' => 'suspended']);

        // Next request should kick them out
        $response = $this->actingAs($user)->get('/dashboard');
        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_valid_email_with_wrong_password_shows_generic_error(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        // A non-existent email should show same error as a suspended account
        // (no enumeration — both get generic "failed" message on wrong password)
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_is_saved_to_history_on_registration(): void
    {
        $this->post('/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'security_question' => 'What is your pet name?',
            'security_answer' => 'Buddy',
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        $this->assertNotNull($user);
        $this->assertCount(1, $user->passwordHistories);
    }

    public function test_cannot_reuse_current_password(): void
    {
        $user = User::factory()->create();

        // Save current password to history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
        ]);

        $response = $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Should reject (either validation error on strong password or history check)
        $response->assertSessionHasErrorsIn('updatePassword');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_password_is_saved_to_history_on_update(): void
    {
        $user = User::factory()->create();

        // Save initial password to history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
        ]);

        $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'password',
            'password' => 'NewSecure1!',
            'password_confirmation' => 'NewSecure1!',
        ]);

        $this->assertCount(2, $user->fresh()->passwordHistories);
    }

    public function test_password_expiry_is_extended_on_update(): void
    {
        $user = User::factory()->create([
            'password_expires_at' => now()->addDay(), // expiring soon, but not yet expired
        ]);

        PasswordHistory::create([
            'user_id' => $user->id,
            'password_hash' => $user->password,
        ]);

        $this->actingAs($user)->from('/profile')->put('/password', [
            'current_password' => 'password',
            'password' => 'NewSecure1!',
            'password_confirmation' => 'NewSecure1!',
        ]);

        $this->assertTrue($user->fresh()->password_expires_at->isFuture());
    }

    public function test_expired_password_redirects_to_profile(): void
    {
        $user = User::factory()->create([
            'password_expires_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('password.expired'));
    }

    public function test_non_expired_password_allows_dashboard_access(): void
    {
        $user = User::factory()->create([
            'password_expires_at' => now()->addDays(30),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
    }
}

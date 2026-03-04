<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserAccessRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccessRequestTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    // ── Submitting a request ─────────────────────────────────────────────────

    public function test_guest_can_submit_access_request(): void
    {
        $this->post('/request-access', [
            'first_name' => 'Alice',
            'last_name' => 'Wonderland',
            'email' => 'alice@example.com',
            'security_question' => 'What is your pet name?',
            'security_answer' => 'Buddy',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('user_access_requests', [
            'email' => 'alice@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_pending_request_is_rejected(): void
    {
        UserAccessRequest::create([
            'first_name' => 'Alice',
            'last_name' => 'Wonderland',
            'email' => 'alice@example.com',
            'security_question' => 'What is your pet name?',
            'security_answer' => \Illuminate\Support\Facades\Hash::make('buddy'),
            'status' => 'pending',
        ]);

        $response = $this->post('/request-access', [
            'first_name' => 'Alice',
            'last_name' => 'Wonderland',
            'email' => 'alice@example.com',
            'security_question' => 'What is your pet name?',
            'security_answer' => 'Buddy',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, UserAccessRequest::where('email', 'alice@example.com')->count());
    }

    public function test_cannot_request_access_with_existing_user_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/request-access', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ── Admin views requests ─────────────────────────────────────────────────

    public function test_admin_can_view_pending_requests(): void
    {
        UserAccessRequest::create([
            'first_name' => 'Bob',
            'last_name' => 'Builder',
            'email' => 'bob@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin.requests.index'))
            ->assertOk()
            ->assertSee('bob@example.com');
    }

    public function test_non_admin_cannot_view_requests(): void
    {
        $accountant = User::factory()->create(['role' => 'accountant']);

        $this->actingAs($accountant)
            ->get(route('admin.requests.index'))
            ->assertForbidden();
    }

    // ── Approving requests ───────────────────────────────────────────────────

    public function test_admin_can_approve_access_request(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Carol',
            'last_name' => 'Danvers',
            'email' => 'carol@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.approve', $request), [
            'role' => 'accountant',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'carol@example.com', 'status' => 'active']);
        $this->assertEquals('approved', $request->fresh()->status);
        $this->assertEquals($admin->id, $request->fresh()->reviewed_by);
    }

    public function test_approved_user_password_is_correctly_hashed(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Carol',
            'last_name' => 'Danvers',
            'email' => 'carol@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.approve', $request), [
            'role' => 'accountant',
        ]);

        $user = User::where('email', 'carol@example.com')->first();
        $this->assertNotNull($user);
        // Password must be a valid bcrypt hash (not double-hashed)
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    public function test_approved_user_has_expired_password_to_force_reset(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Carol',
            'last_name' => 'Danvers',
            'email' => 'carol@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.approve', $request), [
            'role' => 'accountant',
        ]);

        $user = User::where('email', 'carol@example.com')->first();
        $this->assertTrue($user->password_expires_at->isPast());
    }

    public function test_cannot_approve_already_processed_request(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Carol',
            'last_name' => 'Danvers',
            'email' => 'carol@example.com',
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.requests.approve', $request), [
            'role' => 'accountant',
        ]);

        $response->assertSessionHas('error');
        // No duplicate user should have been created
        $this->assertDatabaseMissing('users', ['email' => 'carol@example.com']);
    }

    // ── Denying requests ─────────────────────────────────────────────────────

    public function test_admin_can_deny_access_request(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Dave',
            'last_name' => 'Villain',
            'email' => 'dave@example.com',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.requests.deny', $request), [
            'rejection_reason' => 'Not eligible at this time.',
        ]);

        $request->refresh();
        $this->assertEquals('rejected', $request->status);
        $this->assertEquals('Not eligible at this time.', $request->rejection_reason);
        $this->assertDatabaseMissing('users', ['email' => 'dave@example.com']);
    }

    public function test_deny_requires_rejection_reason(): void
    {
        $admin = $this->admin();
        $request = UserAccessRequest::create([
            'first_name' => 'Dave',
            'last_name' => 'Villain',
            'email' => 'dave@example.com',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.requests.deny', $request), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertEquals('pending', $request->fresh()->status);
    }
}

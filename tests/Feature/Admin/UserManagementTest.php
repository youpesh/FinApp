<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function accountant(): User
    {
        return User::factory()->create(['role' => 'accountant', 'status' => 'active']);
    }

    // ── Access control ───────────────────────────────────────────────────────

    public function test_non_admin_cannot_access_user_list(): void
    {
        $this->actingAs($this->accountant())
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_user_list(): void
    {
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_user_list(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    // ── Create user ──────────────────────────────────────────────────────────

    public function test_admin_can_create_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'role' => 'accountant',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }

    public function test_created_user_password_is_correctly_hashed(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'role' => 'accountant',
            'status' => 'active',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        // Password must be a valid bcrypt hash (auto-generated)
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    public function test_created_user_has_password_expiry_set(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'role' => 'accountant',
            'status' => 'active',
        ]);

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user->password_expires_at);
        // Admin-created users have expired password to force change on first login
        $this->assertTrue($user->password_expires_at->isPast());
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['role' => 'accountant']);

        $this->actingAs($admin)->patch(route('admin.users.update', $target), [
            'first_name' => $target->first_name,
            'last_name' => $target->last_name,
            'email' => $target->email,
            'role' => 'manager',
            'status' => 'active',
        ]);

        $this->assertEquals('manager', $target->fresh()->role);
    }

    public function test_admin_can_suspend_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['status' => 'active']);

        $this->actingAs($admin)->patch(route('admin.users.update', $target), [
            'first_name' => $target->first_name,
            'last_name' => $target->last_name,
            'email' => $target->email,
            'role' => $target->role,
            'status' => 'suspended',
            'suspension_start_date' => now()->toDateString(),
        ]);

        $this->assertEquals('suspended', $target->fresh()->status);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_admin_can_soft_delete_another_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $target));

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $this->actingAs($this->accountant())
            ->post(route('admin.users.store'), [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@example.com',
                'role' => 'accountant',
                'status' => 'active',
            ])
            ->assertForbidden();
    }
}

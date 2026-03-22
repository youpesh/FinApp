<?php

namespace Tests\Feature\Accounts;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountsTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    private function manager(): User
    {
        return User::factory()->create(['role' => 'manager', 'status' => 'active']);
    }

    private function accountant(): User
    {
        return User::factory()->create(['role' => 'accountant', 'status' => 'active']);
    }

    private function validAccountData(array $overrides = []): array
    {
        return array_merge([
            'account_name' => 'Cash',
            'account_number' => 1010,
            'account_description' => 'Main cash account',
            'normal_side' => 'debit',
            'account_category' => 'asset',
            'account_subcategory' => 'Current Assets',
            'initial_balance' => 5000.00,
            'debit' => 0,
            'credit' => 0,
            'balance' => 5000.00,
            'order' => 1,
            'statement' => 'BS',
            'comment' => 'Primary cash account',
        ], $overrides);
    }

    // ── Access control ──────────────────────────────────────────

    public function test_guest_cannot_access_accounts(): void
    {
        $this->get(route('accounts.index'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_accounts_index(): void
    {
        $this->actingAs($this->admin())
            ->get(route('accounts.index'))
            ->assertOk();
    }

    public function test_manager_can_access_accounts_index(): void
    {
        $this->actingAs($this->manager())
            ->get(route('accounts.index'))
            ->assertOk();
    }

    public function test_accountant_can_access_accounts_index(): void
    {
        $this->actingAs($this->accountant())
            ->get(route('accounts.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_create_account(): void
    {
        $this->actingAs($this->accountant())
            ->post(route('accounts.store'), $this->validAccountData())
            ->assertForbidden();
    }

    public function test_non_admin_cannot_edit_account(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create(['created_by' => $admin->id]);

        $this->actingAs($this->accountant())
            ->put(route('accounts.update', $account), $this->validAccountData())
            ->assertForbidden();
    }

    public function test_non_admin_cannot_deactivate_account(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->zeroBalance()->create(['created_by' => $admin->id]);

        $this->actingAs($this->accountant())
            ->patch(route('accounts.deactivate', $account))
            ->assertForbidden();
    }

    // ── CRUD ────────────────────────────────────────────────────

    public function test_admin_can_create_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData());

        $this->assertDatabaseHas('accounts', ['account_name' => 'Cash', 'account_number' => 1010]);
    }

    public function test_admin_can_update_account(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create(['created_by' => $admin->id, 'account_category' => 'asset', 'account_number' => 1020]);

        $this->actingAs($admin)->put(route('accounts.update', $account), $this->validAccountData([
            'account_name' => 'Petty Cash',
            'account_number' => 1020,
        ]));

        $this->assertEquals('Petty Cash', $account->fresh()->account_name);
    }

    public function test_admin_can_deactivate_zero_balance_account(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->zeroBalance()->create(['created_by' => $admin->id]);

        $this->actingAs($admin)->patch(route('accounts.deactivate', $account));

        $this->assertFalse($account->fresh()->is_active);
    }

    public function test_admin_cannot_deactivate_account_with_balance(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create([
            'created_by' => $admin->id,
            'balance' => 1000.00,
        ]);

        $response = $this->actingAs($admin)->patch(route('accounts.deactivate', $account));

        $response->assertRedirect(route('accounts.show', $account));
        $response->assertSessionHas('error');
        $this->assertTrue($account->fresh()->is_active);
    }

    // ── Validation ──────────────────────────────────────────────

    public function test_duplicate_account_name_rejected(): void
    {
        $admin = $this->admin();
        Account::factory()->create(['created_by' => $admin->id, 'account_name' => 'Cash']);

        $response = $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData([
            'account_name' => 'Cash',
            'account_number' => 1099,
        ]));

        $response->assertSessionHasErrors('account_name');
    }

    public function test_duplicate_account_number_rejected(): void
    {
        $admin = $this->admin();
        Account::factory()->create(['created_by' => $admin->id, 'account_number' => 1010]);

        $response = $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData([
            'account_name' => 'Other Cash',
            'account_number' => 1010,
        ]));

        $response->assertSessionHasErrors('account_number');
    }

    public function test_account_number_must_be_integer(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData([
            'account_number' => 'abc',
        ]));

        $response->assertSessionHasErrors('account_number');
    }

    public function test_account_number_must_start_with_correct_digit(): void
    {
        $admin = $this->admin();

        // Asset accounts must start with 1, so 2010 should fail
        $response = $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData([
            'account_category' => 'asset',
            'account_number' => 2010,
        ]));

        $response->assertSessionHasErrors('account_number');
    }

    public function test_monetary_values_reject_negative(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData([
            'balance' => -100,
        ]));

        $response->assertSessionHasErrors('balance');
    }

    // ── Event Logging ───────────────────────────────────────────

    public function test_event_log_created_on_account_creation(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('accounts.store'), $this->validAccountData());

        $this->assertDatabaseHas('account_event_logs', [
            'event_type' => 'created',
            'user_id' => $admin->id,
        ]);
    }

    public function test_event_log_records_before_and_after_on_update(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create([
            'created_by' => $admin->id,
            'account_name' => 'Old Name',
            'account_category' => 'asset',
            'account_number' => 1030,
        ]);

        $this->actingAs($admin)->put(route('accounts.update', $account), $this->validAccountData([
            'account_name' => 'New Name',
            'account_number' => 1030,
        ]));

        $log = \App\Models\AccountEventLog::where('account_id', $account->id)
            ->where('event_type', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('Old Name', $log->before_image['account_name']);
        $this->assertEquals('New Name', $log->after_image['account_name']);
    }

    public function test_event_log_created_on_deactivation(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->zeroBalance()->create(['created_by' => $admin->id]);

        $this->actingAs($admin)->patch(route('accounts.deactivate', $account));

        $this->assertDatabaseHas('account_event_logs', [
            'account_id' => $account->id,
            'event_type' => 'deactivated',
        ]);
    }

    // ── View Access ─────────────────────────────────────────────

    public function test_all_roles_can_view_account_detail(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create(['created_by' => $admin->id]);

        foreach ([$admin, $this->manager(), $this->accountant()] as $user) {
            $this->actingAs($user)
                ->get(route('accounts.show', $account))
                ->assertOk();
        }
    }

    public function test_all_roles_can_view_event_log(): void
    {
        $admin = $this->admin();
        $account = Account::factory()->create(['created_by' => $admin->id]);

        foreach ([$admin, $this->manager(), $this->accountant()] as $user) {
            $this->actingAs($user)
                ->get(route('accounts.event-log', $account))
                ->assertOk();
        }
    }
}

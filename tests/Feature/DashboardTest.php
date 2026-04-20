<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
use App\Models\UserAccessRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $accountant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
        $this->accountant = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        // Minimum chart of accounts so the ratio service doesn't blow up.
        Account::create(['account_number' => 10100, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
        Account::create(['account_number' => 10200, 'account_name' => 'AR', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'statement' => 'BS', 'order' => 2, 'is_active' => true, 'created_by' => $this->admin->id]);
        Account::create(['account_number' => 20100, 'account_name' => 'AP', 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'statement' => 'BS', 'order' => 3, 'is_active' => true, 'created_by' => $this->admin->id]);
        Account::create(['account_number' => 30100, 'account_name' => 'Common Stock', 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => 'Equity', 'statement' => 'BS', 'order' => 4, 'is_active' => true, 'created_by' => $this->admin->id]);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_each_role_sees_dashboard_200(): void
    {
        foreach ([$this->admin, $this->manager, $this->accountant] as $user) {
            $this->actingAs($user)->get(route('dashboard'))
                ->assertOk()
                ->assertSee('Financial Ratios')
                ->assertSee('Current Ratio');
        }
    }

    public function test_manager_sees_pending_journal_entry_alert(): void
    {
        JournalEntry::create([
            'reference_id' => 'JE-TEST-1',
            'date' => now(),
            'description' => 'pending',
            'is_adjusting' => false,
            'status' => 'pending',
            'created_by' => $this->accountant->id,
        ]);

        $this->actingAs($this->manager)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Journal entries awaiting approval')
            ->assertSeeInOrder(['Action Items', 'Journal entries awaiting approval']);
    }

    public function test_manager_sees_all_clear_when_no_pending_entries(): void
    {
        $this->actingAs($this->manager)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('All clear');
    }

    public function test_admin_sees_pending_access_request_alert(): void
    {
        UserAccessRequest::create([
            'first_name' => 'Alex',
            'last_name' => 'Doe',
            'email' => 'alex@example.com',
            'address' => '1 Main St',
            'dob' => '1990-01-01',
            'security_question' => 'pet',
            'security_answer' => 'dog',
            'status' => 'pending',
        ]);

        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Pending access requests');
    }

    public function test_accountant_sees_their_rejected_entries_alert(): void
    {
        JournalEntry::create([
            'reference_id' => 'JE-REJ-1',
            'date' => now(),
            'description' => 'rejected',
            'is_adjusting' => false,
            'status' => 'rejected',
            'created_by' => $this->accountant->id,
            'rejection_reason' => 'fix line 2',
        ]);

        $this->actingAs($this->accountant)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Your rejected entries to revisit');
    }

    public function test_accountant_does_not_see_other_users_rejected_entries(): void
    {
        // Rejection for a different user; should not surface for this accountant.
        $other = User::factory()->create(['role' => 'accountant', 'status' => 'active']);
        JournalEntry::create([
            'reference_id' => 'JE-REJ-OTHER',
            'date' => now(),
            'description' => 'rejected for someone else',
            'is_adjusting' => false,
            'status' => 'rejected',
            'created_by' => $other->id,
        ]);

        $this->actingAs($this->accountant)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('All clear');
    }
}

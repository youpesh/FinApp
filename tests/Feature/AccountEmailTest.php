<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\ErrorMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AccountEmailTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $accountant;
    protected Account $cash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $this->manager = User::factory()->create(['role' => 'manager', 'status' => 'active']);
        $this->accountant = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        ErrorMessage::create(['code' => 'EMAIL_RECIPIENT_INVALID', 'message' => 'Recipient must be an active manager or administrator.', 'severity' => 'error']);

        $this->cash = Account::create(['account_number' => 101, 'account_name' => 'Cash', 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current', 'statement' => 'BS', 'order' => 1, 'is_active' => true, 'created_by' => $this->admin->id]);
    }

    public function test_accountant_can_email_manager_about_an_account(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->accountant)->post(route('accounts.email', $this->cash), [
            'recipient_email' => $this->manager->email,
            'subject' => 'Question about Cash',
            'body' => 'Need clarification on a balance.',
        ]);

        $response->assertRedirect(route('accounts.show', $this->cash));
        $this->assertDatabaseHas('email_logs', [
            'recipient' => $this->manager->email,
            'subject' => 'Question about Cash',
            'sent_by' => $this->accountant->id,
        ]);
    }

    public function test_cannot_email_a_non_manager_from_account_page(): void
    {
        $other = User::factory()->create(['role' => 'accountant', 'status' => 'active']);

        $response = $this->actingAs($this->accountant)->post(route('accounts.email', $this->cash), [
            'recipient_email' => $other->email,
            'subject' => 'test',
            'body' => 'test',
        ]);

        $response->assertSessionHasErrors('recipient_email');
        $this->assertDatabaseMissing('email_logs', ['recipient' => $other->email]);
    }
}

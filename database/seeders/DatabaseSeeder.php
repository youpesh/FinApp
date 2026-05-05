<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountEventLog;
use App\Models\User;
use App\Models\UserAccessRequest;
use App\Models\ErrorMessage;
use App\Services\PasswordService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $passwordService = new PasswordService();

        // Create admin user
        $admin = User::updateOrCreate(
            ['username' => 'admin0126'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@finapp.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'status' => 'active',
                'password_expires_at' => now()->addDays(90),
            ]
        );

        // Save password to history (only if newly created or password changed)
        $passwordService->saveToHistory($admin, $admin->password);

        // Create a manager user
        $manager = User::updateOrCreate(
            ['username' => 'msmith0126'],
            [
                'first_name' => 'Manager',
                'last_name' => 'Smith',
                'email' => 'manager@finapp.com',
                'password' => Hash::make('Manager123!'),
                'role' => 'manager',
                'status' => 'active',
                'password_expires_at' => now()->addDays(90),
                'created_by' => $admin->id,
            ]
        );

        $passwordService->saveToHistory($manager, $manager->password);

        // Create an accountant user
        $accountant = User::updateOrCreate(
            ['username' => 'ajones0126'],
            [
                'first_name' => 'Accountant',
                'last_name' => 'Jones',
                'email' => 'accountant@finapp.com',
                'password' => Hash::make('Account123!'),
                'role' => 'accountant',
                'status' => 'active',
                'password_expires_at' => now()->addDays(90),
                'created_by' => $admin->id,
            ]
        );

        $passwordService->saveToHistory($accountant, $accountant->password);

        // Seed common error messages
        $errorMessages = [
            [
                'code' => 'PASSWORD_WEAK',
                'message' => 'Password must be at least 8 characters, start with a letter, and contain at least one letter, one number, and one special character.',
                'severity' => 'error',
            ],
            [
                'code' => 'PASSWORD_REUSED',
                'message' => 'This password has been used before. Please choose a different password.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCOUNT_SUSPENDED',
                'message' => 'Your account has been suspended. Please contact an administrator.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCOUNT_INACTIVE',
                'message' => 'Your account has been deactivated.',
                'severity' => 'error',
            ],
            [
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'The provided credentials are incorrect.',
                'severity' => 'error',
            ],
            [
                'code' => 'DEBIT_CREDIT_MISMATCH',
                'message' => 'Total debits must equal total credits.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCOUNT_HAS_BALANCE',
                'message' => 'Cannot deactivate an account with a non-zero balance.',
                'severity' => 'error',
            ],
            [
                'code' => 'DUPLICATE_ACCOUNT',
                'message' => 'An account with this number or name already exists.',
                'severity' => 'error',
            ],
            [
                'code' => 'INVALID_ACCOUNT_NUMBER',
                'message' => 'Account number is invalid. Please check the format.',
                'severity' => 'error',
            ],
            [
                'code' => 'PENDING_APPROVAL',
                'message' => 'This entry is pending approval and cannot be modified.',
                'severity' => 'warning',
            ],
            // Sprint 2 – Chart of Accounts error messages
            [
                'code' => 'ACCT_NAME_DUP',
                'message' => 'An account with this name already exists. Duplicate account names are not allowed.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCT_NUM_DUP',
                'message' => 'An account with this number already exists. Duplicate account numbers are not allowed.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCT_NUM_INVALID',
                'message' => 'Account number must be a whole number. Decimal spaces and alphanumeric values are not allowed.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCT_NUM_RANGE',
                'message' => 'Account number must start with the correct digit for its category (1=Asset, 2=Liability, 3=Equity, 4=Revenue, 5=Expense).',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCT_DEACTIVATE_BALANCE',
                'message' => 'Accounts with a balance greater than zero cannot be deactivated.',
                'severity' => 'error',
            ],
            [
                'code' => 'ACCT_MONETARY_FORMAT',
                'message' => 'All monetary values must have exactly two decimal places.',
                'severity' => 'error',
            ],
            // Sprint 3 - Journalizing Error Messages
            [
                'code' => 'DEBITS_BEFORE_CREDITS',
                'message' => 'Debits must be entered before credits.',
                'severity' => 'error',
            ],
            [
                'code' => 'MIN_DEBIT_CREDIT',
                'message' => 'Each transaction must have at least one debit and one credit.',
                'severity' => 'error',
            ],
            [
                'code' => 'INVALID_ACCOUNTS',
                'message' => 'Journal entries can only use accounts found in the Chart of Accounts.',
                'severity' => 'error',
            ],
            // Sprint 4 – Adjusting Entries & Financial Reports
            [
                'code' => 'REJECTION_REASON_REQUIRED',
                'message' => 'A rejection reason is required when rejecting an adjusting journal entry.',
                'severity' => 'error',
            ],
            [
                'code' => 'REPORT_DATE_RANGE_INVALID',
                'message' => 'The end date of a report must be on or after the start date.',
                'severity' => 'error',
            ],
            [
                'code' => 'EMAIL_RECIPIENT_INVALID',
                'message' => 'Recipient must be an active manager or administrator.',
                'severity' => 'error',
            ],
            [
                'code' => 'AJE_PENDING_ONLY',
                'message' => 'Only pending adjusting entries can be approved or rejected.',
                'severity' => 'warning',
            ],
        ];

        foreach ($errorMessages as $error) {
            ErrorMessage::updateOrCreate(['code' => $error['code']], $error);
        }

        // ── Chart of Accounts ─────────────────────────────────────
        // Numbers and balances mirror the "Lee Cage Plumbing Supplies" solved problem
        // (Balance Sheet as 30 June 2012) shipped with the course materials.
        // 1xxxx = Asset, 2xxxx = Liability, 3xxxx = Equity, 4xxxx = Revenue, 5xxxx = Expense
        $accounts = [
            // Current Assets
            ['account_name' => 'Cash at bank', 'account_number' => 10100, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 6500.00, 'balance' => 6500.00, 'statement' => 'BS', 'order' => 1],
            ['account_name' => 'Cash on hand', 'account_number' => 10200, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 1200.00, 'balance' => 1200.00, 'statement' => 'BS', 'order' => 2],
            ['account_name' => 'Debtors', 'account_number' => 10300, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 4000.00, 'balance' => 4000.00, 'statement' => 'BS', 'order' => 3],
            ['account_name' => 'Inventory', 'account_number' => 10400, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 7600.00, 'balance' => 7600.00, 'statement' => 'BS', 'order' => 4],
            // Non-Current Assets
            ['account_name' => 'Delivery vehicle', 'account_number' => 15100, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 22000.00, 'balance' => 22000.00, 'statement' => 'BS', 'order' => 5],
            ['account_name' => 'Fixtures and fittings', 'account_number' => 15200, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 7800.00, 'balance' => 7800.00, 'statement' => 'BS', 'order' => 6],
            ['account_name' => 'Office equipment', 'account_number' => 15300, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 4000.00, 'balance' => 4000.00, 'statement' => 'BS', 'order' => 7],
            ['account_name' => 'Premises', 'account_number' => 15400, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 56000.00, 'balance' => 56000.00, 'statement' => 'BS', 'order' => 8],
            ['account_name' => 'Furniture', 'account_number' => 15500, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 6000.00, 'balance' => 6000.00, 'statement' => 'BS', 'order' => 9],
            ['account_name' => 'Investment - Telstra shares', 'account_number' => 15600, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Non-Current Assets', 'initial_balance' => 3500.00, 'balance' => 3500.00, 'statement' => 'BS', 'order' => 10],
            // Current Liabilities
            ['account_name' => 'Creditors', 'account_number' => 20100, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'initial_balance' => 12000.00, 'balance' => 12000.00, 'statement' => 'BS', 'order' => 20],
            ['account_name' => 'Loan (due 31/1/13)', 'account_number' => 20200, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'initial_balance' => 8500.00, 'balance' => 8500.00, 'statement' => 'BS', 'order' => 21],
            // Non-Current Liabilities
            ['account_name' => 'Mortgage on premises', 'account_number' => 25100, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Non-Current Liabilities', 'initial_balance' => 40000.00, 'balance' => 40000.00, 'statement' => 'BS', 'order' => 25],
            // Owner's Equity (Drawings is a contra-equity, debit-normal)
            ['account_name' => 'Capital - Lee Cage', 'account_number' => 30100, 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => "Owner's Equity", 'initial_balance' => 58750.00, 'balance' => 58750.00, 'statement' => 'BS', 'order' => 30],
            ['account_name' => 'Drawings - Lee Cage', 'account_number' => 30200, 'normal_side' => 'debit', 'account_category' => 'equity', 'account_subcategory' => "Owner's Equity", 'initial_balance' => 650.00, 'balance' => 650.00, 'statement' => 'BS', 'order' => 31],
            ['account_name' => 'Retained Earnings', 'account_number' => 30900, 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => "Owner's Equity", 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'RE', 'order' => 39],
            // Revenue / Expense scaffolding (kept so journal entries still have valid targets)
            ['account_name' => 'Sales Revenue', 'account_number' => 40100, 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 40],
            ['account_name' => 'Service Revenue', 'account_number' => 40200, 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 41],
            ['account_name' => 'Wages Expense', 'account_number' => 50100, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 50],
            ['account_name' => 'Rent Expense', 'account_number' => 50200, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 51],
            ['account_name' => 'Utilities Expense', 'account_number' => 50300, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 52],
            ['account_name' => 'Insurance Expense', 'account_number' => 50400, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 53],
        ];

        $accountModels = [];
        foreach ($accounts as $acctData) {
            $acct = Account::updateOrCreate(
                ['account_number' => $acctData['account_number']],
                array_merge($acctData, [
                    'is_active' => true,
                    'created_by' => $admin->id,
                ])
            );

            AccountEventLog::updateOrCreate(
                ['account_id' => $acct->id, 'event_type' => 'created'],
                [
                    'user_id' => $admin->id,
                    'before_image' => null,
                    'after_image' => $acct->toSnapshot(),
                ]
            );

            $accountModels[$acct->account_number] = $acct;
        }

        // No demo journal entries — the Lee Cage solved problem only specifies
        // opening balances. Reports should match the textbook balance sheet
        // exactly, so transactions are left for the user to enter.

        // Pending access request so the admin demo has something to approve.
        UserAccessRequest::updateOrCreate(
            ['email' => 'sarah.patel@example.com'],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Patel',
                'address' => '482 Maple Street, Atlanta, GA 30301',
                'dob' => '1994-07-18',
                'security_question' => "What was the name of your first pet?",
                'security_answer' => 'Biscuit',
                'status' => 'pending',
            ]
        );

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin0126 / Admin123!');
        $this->command->info('Manager credentials: msmith0126 / Manager123!');
        $this->command->info('Accountant credentials: ajones0126 / Account123!');
        $this->command->info('Seeded: ' . count($accounts) . ' accounts (Lee Cage Plumbing Supplies opening balances)');
        $this->command->info('Seeded: 1 pending access request (Sarah Patel) for admin demo');
    }
}

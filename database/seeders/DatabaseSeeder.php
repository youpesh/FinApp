<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountEventLog;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\User;
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
        // 1xxxx = Asset, 2xxxx = Liability, 3xxxx = Equity, 4xxxx = Revenue, 5xxxx = Expense
        $accounts = [
            // Assets
            ['account_name' => 'Cash', 'account_number' => 10100, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 50000.00, 'balance' => 50000.00, 'statement' => 'BS', 'order' => 1],
            ['account_name' => 'Accounts Receivable', 'account_number' => 10200, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 8000.00, 'balance' => 8000.00, 'statement' => 'BS', 'order' => 2],
            ['account_name' => 'Office Supplies', 'account_number' => 10300, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 1200.00, 'balance' => 1200.00, 'statement' => 'BS', 'order' => 3],
            ['account_name' => 'Prepaid Insurance', 'account_number' => 10400, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Current Assets', 'initial_balance' => 2400.00, 'balance' => 2400.00, 'statement' => 'BS', 'order' => 4],
            ['account_name' => 'Equipment', 'account_number' => 15100, 'normal_side' => 'debit', 'account_category' => 'asset', 'account_subcategory' => 'Fixed Assets', 'initial_balance' => 25000.00, 'balance' => 25000.00, 'statement' => 'BS', 'order' => 5],
            ['account_name' => 'Accumulated Depreciation - Equipment', 'account_number' => 15200, 'normal_side' => 'credit', 'account_category' => 'asset', 'account_subcategory' => 'Fixed Assets', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'BS', 'order' => 6],
            // Liabilities
            ['account_name' => 'Accounts Payable', 'account_number' => 20100, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'initial_balance' => 5000.00, 'balance' => 5000.00, 'statement' => 'BS', 'order' => 10],
            ['account_name' => 'Wages Payable', 'account_number' => 20200, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'BS', 'order' => 11],
            ['account_name' => 'Unearned Revenue', 'account_number' => 20300, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Current Liabilities', 'initial_balance' => 3000.00, 'balance' => 3000.00, 'statement' => 'BS', 'order' => 12],
            ['account_name' => 'Notes Payable', 'account_number' => 20400, 'normal_side' => 'credit', 'account_category' => 'liability', 'account_subcategory' => 'Long-Term Liabilities', 'initial_balance' => 10000.00, 'balance' => 10000.00, 'statement' => 'BS', 'order' => 13],
            // Equity
            ['account_name' => 'Common Stock', 'account_number' => 30100, 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => 'Equity', 'initial_balance' => 50000.00, 'balance' => 50000.00, 'statement' => 'BS', 'order' => 20],
            ['account_name' => 'Retained Earnings', 'account_number' => 30200, 'normal_side' => 'credit', 'account_category' => 'equity', 'account_subcategory' => 'Equity', 'initial_balance' => 18600.00, 'balance' => 18600.00, 'statement' => 'RE', 'order' => 21],
            // Revenue
            ['account_name' => 'Service Revenue', 'account_number' => 40100, 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Operating Revenue', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 30],
            ['account_name' => 'Interest Revenue', 'account_number' => 40200, 'normal_side' => 'credit', 'account_category' => 'revenue', 'account_subcategory' => 'Other Revenue', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 31],
            // Expenses
            ['account_name' => 'Wages Expense', 'account_number' => 50100, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 40],
            ['account_name' => 'Rent Expense', 'account_number' => 50200, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 41],
            ['account_name' => 'Utilities Expense', 'account_number' => 50300, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 42],
            ['account_name' => 'Office Supplies Expense', 'account_number' => 50400, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 43],
            ['account_name' => 'Insurance Expense', 'account_number' => 50500, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 44],
            ['account_name' => 'Depreciation Expense', 'account_number' => 50600, 'normal_side' => 'debit', 'account_category' => 'expense', 'account_subcategory' => 'Operating Expenses', 'initial_balance' => 0.00, 'balance' => 0.00, 'statement' => 'IS', 'order' => 45],
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

        // ── Approved Journal Entries (gives reports real data) ────
        $journalData = [
            [
                'ref' => 'JE-2026-0001', 'date' => '2026-01-15',
                'desc' => 'Received payment for consulting services',
                'lines' => [
                    ['acct' => 10100, 'type' => 'debit', 'amount' => 12000.00],
                    ['acct' => 40100, 'type' => 'credit', 'amount' => 12000.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0002', 'date' => '2026-01-31',
                'desc' => 'Paid monthly office rent',
                'lines' => [
                    ['acct' => 50200, 'type' => 'debit', 'amount' => 2500.00],
                    ['acct' => 10100, 'type' => 'credit', 'amount' => 2500.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0003', 'date' => '2026-02-10',
                'desc' => 'Paid employee wages for January',
                'lines' => [
                    ['acct' => 50100, 'type' => 'debit', 'amount' => 4500.00],
                    ['acct' => 10100, 'type' => 'credit', 'amount' => 4500.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0004', 'date' => '2026-02-15',
                'desc' => 'Billed client for web development project',
                'lines' => [
                    ['acct' => 10200, 'type' => 'debit', 'amount' => 8500.00],
                    ['acct' => 40100, 'type' => 'credit', 'amount' => 8500.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0005', 'date' => '2026-02-28',
                'desc' => 'Paid utilities for February',
                'lines' => [
                    ['acct' => 50300, 'type' => 'debit', 'amount' => 350.00],
                    ['acct' => 10100, 'type' => 'credit', 'amount' => 350.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0006', 'date' => '2026-03-01',
                'desc' => 'Collected on accounts receivable',
                'lines' => [
                    ['acct' => 10100, 'type' => 'debit', 'amount' => 5000.00],
                    ['acct' => 10200, 'type' => 'credit', 'amount' => 5000.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0007', 'date' => '2026-03-15',
                'desc' => 'Earned interest on business savings account',
                'lines' => [
                    ['acct' => 10100, 'type' => 'debit', 'amount' => 150.00],
                    ['acct' => 40200, 'type' => 'credit', 'amount' => 150.00],
                ],
            ],
            [
                'ref' => 'JE-2026-0008', 'date' => '2026-03-31',
                'desc' => 'Paid monthly office rent for March',
                'lines' => [
                    ['acct' => 50200, 'type' => 'debit', 'amount' => 2500.00],
                    ['acct' => 10100, 'type' => 'credit', 'amount' => 2500.00],
                ],
            ],
        ];

        foreach ($journalData as $jd) {
            $entry = JournalEntry::updateOrCreate(
                ['reference_id' => $jd['ref']],
                [
                    'date' => $jd['date'],
                    'description' => $jd['desc'],
                    'is_adjusting' => false,
                    'status' => 'approved',
                    'created_by' => $accountant->id,
                    'approved_by' => $manager->id,
                ]
            );

            foreach ($jd['lines'] as $line) {
                JournalEntryLine::updateOrCreate(
                    ['journal_entry_id' => $entry->id, 'account_id' => $accountModels[$line['acct']]->id, 'type' => $line['type']],
                    ['amount' => $line['amount']]
                );
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin0126 / Admin123!');
        $this->command->info('Manager credentials: msmith0126 / Manager123!');
        $this->command->info('Accountant credentials: ajones0126 / Account123!');
        $this->command->info('Seeded: ' . count($accounts) . ' accounts, ' . count($journalData) . ' approved journal entries');
    }
}

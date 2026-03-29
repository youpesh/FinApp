<?php

namespace Database\Seeders;

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
        $admin = User::create([
            'username' => 'admin0126',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@finapp.com',
            'password' => Hash::make('Admin123!'),
            'role' => 'admin',
            'status' => 'active',
            'password_expires_at' => now()->addDays(90),
        ]);

        // Save password to history
        $passwordService->saveToHistory($admin, $admin->password);

        // Create a manager user
        $manager = User::create([
            'username' => 'msmith0126',
            'first_name' => 'Manager',
            'last_name' => 'Smith',
            'email' => 'manager@finapp.com',
            'password' => Hash::make('Manager123!'),
            'role' => 'manager',
            'status' => 'active',
            'password_expires_at' => now()->addDays(90),
            'created_by' => $admin->id,
        ]);

        $passwordService->saveToHistory($manager, $manager->password);

        // Create an accountant user
        $accountant = User::create([
            'username' => 'ajones0126',
            'first_name' => 'Accountant',
            'last_name' => 'Jones',
            'email' => 'accountant@finapp.com',
            'password' => Hash::make('Account123!'),
            'role' => 'accountant',
            'status' => 'active',
            'password_expires_at' => now()->addDays(90),
            'created_by' => $admin->id,
        ]);

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
        ];

        foreach ($errorMessages as $error) {
            ErrorMessage::create($error);
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@finapp.com / Admin123!');
        $this->command->info('Manager credentials: manager@finapp.com / Manager123!');
        $this->command->info('Accountant credentials: accountant@finapp.com / Account123!');
    }
}

<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $categories = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $categoryDigit = [
            'asset' => 1,
            'liability' => 2,
            'equity' => 3,
            'revenue' => 4,
            'expense' => 5,
        ];
        $subcategories = [
            'asset' => ['Current Assets', 'Fixed Assets', 'Other Assets'],
            'liability' => ['Current Liabilities', 'Long-term Liabilities'],
            'equity' => ['Owner Equity', 'Retained Earnings'],
            'revenue' => ['Operating Revenue', 'Other Revenue'],
            'expense' => ['Operating Expenses', 'Administrative Expenses'],
        ];
        $statements = [
            'asset' => 'BS',
            'liability' => 'BS',
            'equity' => 'RE',
            'revenue' => 'IS',
            'expense' => 'IS',
        ];

        $category = fake()->randomElement($categories);
        $digit = $categoryDigit[$category];
        $number = (int) ($digit . fake()->unique()->numerify('###'));

        $balance = fake()->randomFloat(2, 0, 50000);

        return [
            'account_name' => fake()->unique()->words(3, true),
            'account_number' => $number,
            'account_description' => fake()->sentence(),
            'normal_side' => in_array($category, ['asset', 'expense']) ? 'debit' : 'credit',
            'account_category' => $category,
            'account_subcategory' => fake()->randomElement($subcategories[$category]),
            'initial_balance' => $balance,
            'debit' => 0,
            'credit' => 0,
            'balance' => $balance,
            'order' => fake()->numberBetween(1, 99),
            'statement' => $statements[$category],
            'comment' => fake()->optional()->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the account is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn() => [
            'is_active' => false,
            'balance' => 0,
            'initial_balance' => 0,
        ]);
    }

    /**
     * Indicate a zero balance account.
     */
    public function zeroBalance(): static
    {
        return $this->state(fn() => [
            'balance' => 0,
            'initial_balance' => 0,
            'debit' => 0,
            'credit' => 0,
        ]);
    }
}

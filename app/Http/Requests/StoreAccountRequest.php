<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Models\ErrorMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'account_name' => ['required', 'string', 'max:255', 'unique:accounts,account_name'],
            'account_number' => ['required', 'integer', 'min:1', 'unique:accounts,account_number'],
            'account_description' => ['nullable', 'string'],
            'normal_side' => ['required', Rule::in(['debit', 'credit'])],
            'account_category' => ['required', Rule::in(['asset', 'liability', 'equity', 'revenue', 'expense'])],
            'account_subcategory' => ['required', 'string', 'max:255'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'debit' => ['required', 'numeric', 'min:0'],
            'credit' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'order' => ['required', 'integer', 'min:0'],
            'statement' => ['required', Rule::in(['IS', 'BS', 'RE'])],
            'comment' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom error messages sourced from the database error_messages table.
     */
    public function messages(): array
    {
        return [
            'account_name.unique' => ErrorMessage::getByCode('ACCT_NAME_DUP') ?? 'Duplicate account name.',
            'account_number.unique' => ErrorMessage::getByCode('ACCT_NUM_DUP') ?? 'Duplicate account number.',
            'account_number.integer' => ErrorMessage::getByCode('ACCT_NUM_INVALID') ?? 'Account number must be a whole number.',
        ];
    }

    /**
     * Additional validation: account number must start with the correct digit for its category.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $category = $this->input('account_category');
            $number = $this->input('account_number');

            if ($category && $number) {
                $expectedStart = Account::categoryStartDigit()[$category] ?? null;
                $actualStart = (int) substr((string) $number, 0, 1);

                if ($expectedStart && $actualStart !== $expectedStart) {
                    $msg = ErrorMessage::getByCode('ACCT_NUM_RANGE')
                        ?? 'Account number must start with the correct digit for its category.';
                    $validator->errors()->add('account_number', $msg);
                }
            }
        });
    }
}

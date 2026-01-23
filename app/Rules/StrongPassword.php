<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Minimum 8 characters
        if (strlen($value) < 8) {
            $fail('The password must be at least 8 characters long.');
            return;
        }

        // Must start with a letter
        if (!preg_match('/^[a-zA-Z]/', $value)) {
            $fail('The password must start with a letter.');
            return;
        }

        // Must contain at least one letter
        if (!preg_match('/[a-zA-Z]/', $value)) {
            $fail('The password must contain at least one letter.');
            return;
        }

        // Must contain at least one number
        if (!preg_match('/[0-9]/', $value)) {
            $fail('The password must contain at least one number.');
            return;
        }

        // Must contain at least one special character
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            $fail('The password must contain at least one special character.');
            return;
        }
    }
}

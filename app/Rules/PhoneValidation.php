<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the phone number matches either +63 format (12 characters) or 09 format (11 characters)
        if (!preg_match('/^(\+63\d{10}|09\d{9})$/', $value)) {
            $fail('The :attribute must be a valid Philippine phone number (either 09xxxxxxxxx or +63xxxxxxxxxx format).');
        }
    }
}
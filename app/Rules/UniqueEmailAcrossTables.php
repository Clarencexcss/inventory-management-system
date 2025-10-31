<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use App\Models\Customer;

class UniqueEmailAcrossTables implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check if email exists in users table
        $userExists = User::where('email', strtolower($value))->exists();
        
        // Check if email exists in customers table
        $customerExists = Customer::where('email', strtolower($value))->exists();
        
        // Return false if email exists in either table
        return !($userExists || $customerExists);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '❌ The email address is already taken. Please use a different email.';
    }
}
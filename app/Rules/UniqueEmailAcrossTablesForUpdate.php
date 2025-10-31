<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;
use App\Models\Customer;

class UniqueEmailAcrossTablesForUpdate implements Rule
{
    protected $userId;
    protected $userType; // 'customer' or 'user'

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId, $userType)
    {
        $this->userId = $userId;
        $this->userType = $userType;
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
        $email = strtolower($value);
        
        // Check if email exists in users table (excluding current user)
        $userQuery = User::where('email', $email);
        if ($this->userType === 'user') {
            $userQuery->where('id', '!=', $this->userId);
        }
        $userExists = $userQuery->exists();
        
        // Check if email exists in customers table (excluding current user)
        $customerQuery = Customer::where('email', $email);
        if ($this->userType === 'customer') {
            $customerQuery->where('id', '!=', $this->userId);
        }
        $customerExists = $customerQuery->exists();
        
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
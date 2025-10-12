<?php

namespace App\Http\Requests\Customer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $customerId = auth()->id(); // Get the logged-in customer ID
    
        return [
            'photo' => [
                'image',
                'file',
                'max:1024'
            ],
            'name' => [
                'required',
                'string',
                'max:50'
            ],
            'username' => [
                'nullable',
                'string',
                'max:25',
                Rule::unique('customers', 'username')->ignore($customerId),
            ],
            'email' => [
                'nullable',
                'email',
                'max:50',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:25',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => [
                'required_with:password',
                'string',
                'min:8'
            ],
            'address' => [
                'nullable',
                'string',
                'max:100'
            ],
        ];
    }
}
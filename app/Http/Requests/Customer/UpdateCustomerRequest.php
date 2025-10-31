<?php

namespace App\Http\Requests\Customer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueEmailAcrossTablesForUpdate;

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
                'max:50',
                'regex:/^[a-zA-Z\s.\-\']+$/'
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
                new UniqueEmailAcrossTablesForUpdate($customerId, 'customer')
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^\+63\d{10}$/',
                'max:13',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed'
            ],
            'password_confirmation' => [
                'nullable',
                'string',
                'min:8',
                'required_with:password'
            ],
            'address' => [
                'nullable',
                'string',
                'max:100'
            ],
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'The name may only contain letters, spaces, periods, hyphens, and apostrophes.',
            'phone.regex' => 'The phone number must start with +63 and be exactly 11 digits.',
            'phone.unique' => 'This phone number is already registered.',
        ];
    }
}
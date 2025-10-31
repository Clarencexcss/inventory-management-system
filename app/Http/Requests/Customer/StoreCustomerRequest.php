<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UniqueEmailAcrossTables;

class StoreCustomerRequest extends FormRequest
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
        return [
            'photo' => 'image|file|max:1024',
            'name' => 'required|string|max:50|regex:/^[a-zA-Z\s.\-\']+$/',
            'email' => ['required', 'email', 'max:50', new UniqueEmailAcrossTables],
            'phone' => 'required|string|regex:/^\+63\d{10}$/|unique:customers,phone',
            'account_holder' => 'max:50',
            'account_number' => 'max:25',
            'bank_name' => 'max:25',
            'address' => 'required|string|max:100',
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
<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class StoreProductRequest extends FormRequest
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
            'name'              => 'required|string',
            'slug'              => 'required|unique:products',
            'category_id'       => 'required|integer',
            'unit_id'           => 'required|integer',
            'meat_cut_id'       => 'required|integer|exists:meat_cuts,id',
            'quantity'          => 'required|integer|min:0',
            'price_per_kg'      => 'required|numeric|min:0',
            'storage_location'  => 'required|string',
            'expiration_date'   => 'required|date|after:today',
            'source'            => 'required|string',
            'notes'             => 'nullable|string|max:1000',
            'buying_price'      => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'quantity_alert'    => 'required|integer|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name, '-'),
        ]);
    }

    public function messages(): array
    {
        return [
            'expiration_date.after' => 'The expiration date must be a future date.',
            'price_per_kg.min' => 'The price per kilogram must be greater than 0.',
        ];
    }
}

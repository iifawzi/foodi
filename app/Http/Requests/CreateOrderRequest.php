<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'merchantId' => [
                'integer',
                'min:1',
                Rule::exists('merchants', 'merchant_id')
            ],
            'products' => [
                'required',
                'array',
                'min:1',
            ],
            'products.*.product_id' => [
                'required',
                'integer',
                'min:1',
                Rule::exists('products', 'product_id')
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'products.array' => 'The products field must be an array.',
        ];
    }
}

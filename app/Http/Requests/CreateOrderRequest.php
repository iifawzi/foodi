<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
                'min:1'
            ],
            'products' => [
                'required',
                'array',
                'min:1',
            ],
            'products.*.product_id' => [
                'required',
                'integer',
                'min:1'
            ],
            'products.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'products.array' => 'The products field must be an array.',
        ];
    }
}

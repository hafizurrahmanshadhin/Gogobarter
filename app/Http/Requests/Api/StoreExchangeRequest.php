<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExchangeRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'requested_product_id' => 'required|exists:products,id',
            'name'                 => 'required|string|max:255',
            'product_category_id'  => 'required|exists:product_categories,id',
            'description'          => 'nullable|string',
            'condition'            => 'required|string|max:255',
            'images'               => 'required|array|min:1',
            'images.*'             => 'file|image|mimes:jpg,jpeg,png,gif|max:20480',
            'message'              => 'nullable|string|max:1000',
        ];
    }
}

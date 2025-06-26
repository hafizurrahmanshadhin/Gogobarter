<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest {
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
            'name'                => 'sometimes|required|string|max:255',
            'product_category_id' => 'sometimes|required|exists:product_categories,id',
            'description'         => 'nullable|string',
            'condition'           => 'sometimes|required|string|max:255',
            'images'              => 'nullable|array|min:1',
            'images.*'            => 'file|image|mimes:jpg,jpeg,png,gif|max:20480',
        ];
    }
}

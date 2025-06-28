<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest {
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
            'name'          => 'sometimes|required|string|max:255',
            'phone_number'  => 'sometimes|required|string|max:25',
            'address'       => 'sometimes|required|string|max:255',
            'avatar'        => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:20480',
        ];
    }
}

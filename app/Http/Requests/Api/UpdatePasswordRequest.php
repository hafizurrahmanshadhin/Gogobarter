<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array {
        return [
            'current_password.required' => 'Current password is required.',
            'new_password.required'     => 'New password is required.',
            'new_password.min'          => 'New password must be at least 8 characters long.',
            'new_password.confirmed'    => 'New password confirmation does not match.',
        ];
    }

    /**
     * Validate that current_password matches the authenticated user's password.
     */
    protected function prepareForValidation() {
        $hashedPassword = (string) (auth()->user()->password ?? '');
        if (!Hash::check($this->current_password, $hashedPassword)) {
            $this->merge([
                'current_password' => null,
            ]);
        }
    }
}

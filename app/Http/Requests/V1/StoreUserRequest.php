<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            // 'nickname' => ['nullable', 'string', 'max:50'],
            'birthdate' => ['nullable', 'date'],
            'contact_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'address' => ['required', 'string', 'max:250'],
            'role' => ['sometimes', 'filled', Rule::in(['user', 'admin'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccommodationTypeRequest extends FormRequest
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
        $method = $this->method();
        if ($method === 'PUT') {
            return [
                'accommodation_type_name' => [
                    'required',
                    'string',
                    Rule::unique('accommodation_types', 'accommodation_type_name')->ignore($this->accommodation_type),
                ],
            ];
        } else {
            return [
                'accommodation_type_name' => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique('accommodation_types', 'accommodation_type_name')->ignore($this->accommodation_type),
                ],
            ];
        }
    }
}

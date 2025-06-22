<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInclusionRequest extends FormRequest
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
                'inclusion_name' => [
                    'required',
                    'string',
                    Rule::unique('inclusions', 'inclusion_name')->ignore($this->inclusion),
                ],
            ];
        }else {
              return [
                'inclusion_name' => [
                    'sometimes',
                    'required',
                    'string',
                    Rule::unique('inclusions', 'inclusion_name')->ignore($this->inclusion),
                ],
            ];
        }
    }
}

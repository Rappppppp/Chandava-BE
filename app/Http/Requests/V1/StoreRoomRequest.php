<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
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
        if ($method === "POST") {
            return [
                'room_name' => [
                    'required',
                    'string',
                    Rule::unique('rooms', 'room_name')
                        ->ignore($this->room) // ignore current room if updating
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
                'accommodation_type_id' => ['exists:accommodation_types,id'],
                'description' => ['nullable', 'string'],
                'day_night_tour_price' => ['nullable', 'numeric'],
                'overnight_price' => ['nullable', 'numeric'],
                'notes' => ['nullable', 'string'],
                'is_already_check_in' => ['boolean'],
                'inclusion_ids' => ['nullable', 'array'],
                'images' => ['required', 'array'],
                'inclusion_ids.*' => ['exists:inclusions,id'],
                'images.*' => ['exists:fileponds,name'],
            ];
        } else {
            return [
                'room_name' => [
                    'required',
                    'string',
                    Rule::unique('rooms', 'room_name')
                        ->ignore($this->room) // ignore current room if updating
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        }),
                ],
                'accommodation_type_id' => ['exists:accommodation_types,id'],
                'description' => ['nullable', 'string'],
                'day_night_tour_price' => ['nullable', 'numeric'],
                'overnight_price' => ['nullable', 'numeric'],
                'notes' => ['nullable', 'string'],
                'is_already_check_in' => ['boolean'],
                'inclusion_ids' => ['nullable', 'array'],
                'images' => ['array'],
                'inclusion_ids.*' => ['exists:inclusions,id'],
                'images.*' => ['exists:fileponds,name'],
            ];
        }
    }
}

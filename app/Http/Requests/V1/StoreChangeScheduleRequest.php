<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreChangeScheduleRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'booking_id' => ['required', 'exists:my_bookings,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'], // ✅ must be today or future
            'check_out' => ['nullable', 'date', 'after_or_equal:check_in'], // ✅ same day or future relative to check_in
        ];
    }
}

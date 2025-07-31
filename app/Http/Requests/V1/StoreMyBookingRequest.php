<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreMyBookingRequest extends FormRequest
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
            'room_id' => ['required', 'exists:rooms,id'],
            'no_guests' => ['required', 'min:1'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'total_price' => ['required'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'tour_type' => ['required', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:pending,confirmed,cancelled,completed'],
            'receipt' => ['required', 'string', 'max:255'],
            'admin_note' => ['nullable', 'string'],
        ];
    }
}

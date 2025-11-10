<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyBookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'room_id' => $this->room_id,
            // 'no_guests' => $this->no_guests,
            'check_in' => $this->check_in->toDateString(),
            'check_out' => $this->check_out->toDateString(),
            'tour_type' => $this->tour_type,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'receipt' => $this->receipt,
            'admin_note' => $this->admin_note,
            // Optional: include related models
            'user' => new UserResource($this->whenLoaded('user')),
            'room' => new RoomResource($this->whenLoaded('room')),

            'feedback' => $this->feedback,
        ];
    }
}

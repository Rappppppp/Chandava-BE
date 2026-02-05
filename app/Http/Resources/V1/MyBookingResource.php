<?php

namespace App\Http\Resources\V1;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function App\Helpers\normalizeDate;

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
            'check_in' => Carbon::parse($this->check_in)->format('F j, Y'), # normalizeDate($this->getRawOriginal('check_in')),
            'check_out' => Carbon::parse($this->check_out)->format('F j, Y'), # normalizeDate($this->getRawOriginal('check_out')),
            'tour_type' => $this->tour_type,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'receipt' => $this->receipt,
            'admin_note' => $this->admin_note,
            // Optional: include related models
            'user' => new UserResource($this->whenLoaded('user')),
            'room' => new RoomResource($this->whenLoaded('room')),
            // 'rate' => $this->rate,
            'feedback' => $this->feedback,
        ];
    }
}

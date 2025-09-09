<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChangeScheduleRequestResource extends JsonResource
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
            'user_full_name' => "{$this->user->first_name} {$this->user->last_name}",
            'user_id' => $this->user_id,
            'booking_id' => $this->booking_id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'status' => $this->status,

        ];
    }
}

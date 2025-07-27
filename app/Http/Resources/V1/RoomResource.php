<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "room_name" => $this->room_name,
            "description" => $this->description,
            "day_night_tour_price" => $this->day_night_tour_price,
            "overnight_price" => $this->overnight_price,
            "notes" => $this->notes,
            "is_already_check_in" => $this->is_already_check_in,
            "accommodation_type" => new AccommodationTypeResource($this->accommodationType),
            'room_inclusions' => RoomInclusionResource::collection($this->inclusions),
            "room_images" => RoomImageResource::collection($this->images),
        ];
    }
}

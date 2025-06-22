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
            "id"=> $this->id,
            "room_name"=> $this->room_name,
            "accommodation_type" => new AccommodationTypeResource($this->accommodation_type),
            "room_inclusions" => new RoomInclusionResource($this->room_inclusions),
        ];
    }
}

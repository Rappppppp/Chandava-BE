<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'nickname'       => $this->nickname,
            'birthdate'      => $this->birthdate,
            'contact_number' => $this->contact_number,
            'email'          => $this->email,
            'address'        => $this->address,
            'role'           => $this->role,
  
        ];
    }
}

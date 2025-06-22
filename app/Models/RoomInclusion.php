<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomInclusion extends Model
{
    use HasFactory;
    protected $fillable = [
        "room_id",
        "inclusion_id",
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }
}

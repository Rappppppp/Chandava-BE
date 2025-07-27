<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    use HasFactory;
    protected $fillable = [
        "file",
        "is_main_image",
        "room_id",
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function filepond()
    {
        return $this->hasMany(Filepond::class);
    }
}

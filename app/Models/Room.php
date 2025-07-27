<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;


    protected $fillable = [
        "room_name",
        "accommodation_type_id",
        "description",
        "day_night_tour_price",
        "overnight_price",
        "notes",
        "is_already_check_in",
    ];

    public function accommodationType()
    {
        return $this->belongsTo(AccommodationType::class);
    }

    public function inclusions()
    {
        return $this->belongsToMany(Inclusion::class, "room_inclusions");
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(RoomImage::class)->where('is_main_image', true);
    }
}

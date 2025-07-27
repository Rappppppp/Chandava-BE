<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filepond extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
    ];

    public function roomImage() {
        return $this->belongsTo(RoomImage::class);
    }
}

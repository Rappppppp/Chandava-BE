<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'no_guests',
        'total_price',
        'check_in',
        'check_out',
        'tour_type',
        'status',
        'receipt',
        'admin_note',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'no_guests' => 'integer',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}

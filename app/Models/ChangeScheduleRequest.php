<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeScheduleRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'booking_id',
        'check_in',
        'check_out',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(MyBooking::class, 'booking_id');
    }
}

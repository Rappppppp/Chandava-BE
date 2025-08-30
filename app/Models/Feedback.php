<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'room_id',
        'rate',
        'comment',
    ];

    public function avgRating()
    {
        return self::avg('rate');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images(){
        return $this->hasMany(FeedbackImage::class, 'feedback_id');
    }
}

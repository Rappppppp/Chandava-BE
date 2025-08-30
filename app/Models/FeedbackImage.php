<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackImage extends Model
{
    use HasFactory;

    protected $table = 'feedback_images';
    protected $fillable = [
        'feedback_id',
        'image',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackResponse extends Model
{
    use HasFactory;

    protected $table = 'feedback_responses';

    protected $fillable = [
        'feedback_id',
        'response',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class);
    }
}

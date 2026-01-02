<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizTheory extends Model
{
    use HasFactory;

    protected $table = 'quiz_theories';

    protected $fillable = [
        'topic_id',
        'title',
        'slug',
        'content',
        'sort_order'
    ];

    public function topic()
    {
        return $this->belongsTo(QuizTopic::class, 'topic_id');
    }
}

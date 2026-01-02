<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizTopic extends Model
{
    use HasFactory;

    protected $table = 'quiz_topics';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order'
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'topic_id');
    }

    public function theories()
    {
        return $this->hasMany(QuizTheory::class, 'topic_id');
    }
}

<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $table = 'quiz_questions';

    protected $fillable = [
        'exam_id',
        'topic_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'sort_order'
    ];

    public function exam()
    {
        return $this->belongsTo(QuizExam::class, 'exam_id');
    }

    public function topic()
    {
        return $this->belongsTo(QuizTopic::class, 'topic_id');
    }

    public function getOptionsAttribute()
    {
        $options = [];
        if ($this->option_a) $options['a'] = $this->option_a;
        if ($this->option_b) $options['b'] = $this->option_b;
        if ($this->option_c) $options['c'] = $this->option_c;
        if ($this->option_d) $options['d'] = $this->option_d;
        return $options;
    }
}

<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'questions';

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
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function getOptionsAttribute()
    {
        return [
            'a' => $this->option_a,
            'b' => $this->option_b,
            'c' => $this->option_c,
            'd' => $this->option_d,
        ];
    }
}

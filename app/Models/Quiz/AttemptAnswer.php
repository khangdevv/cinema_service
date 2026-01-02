<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class AttemptAnswer extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'attempt_answers';

    public $timestamps = false;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'user_answer',
        'correct_answer',
        'is_correct',
        'created_at'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(Attempt::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'attempts';

    protected $fillable = [
        'user_id',
        'exam_id',
        'exam_name',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'score',
        'time_spent',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'score' => 'decimal:2',
        'time_spent' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function isCompleted()
    {
        return $this->completed_at !== null;
    }
}

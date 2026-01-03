<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $connection = 'tracnghiem';
    protected $table = 'quiz_attempts';

    protected $fillable = [
        'account_id',
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
        'score' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function exam()
    {
        return $this->belongsTo(QuizExam::class, 'exam_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }

    public function getFormattedTimeAttribute()
    {
        if (!$this->time_spent) return '00:00';
        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}

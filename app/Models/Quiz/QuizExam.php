<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizExam extends Model
{
    use HasFactory;

    protected $connection = 'tracnghiem';
    protected $table = 'quiz_exams';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'time_limit',
        'total_questions',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'exam_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'exam_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

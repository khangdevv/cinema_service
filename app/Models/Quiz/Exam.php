<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'exams';

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
        'time_limit' => 'integer',
        'total_questions' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

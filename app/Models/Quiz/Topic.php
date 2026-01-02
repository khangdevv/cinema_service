<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'topics';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function theories()
    {
        return $this->hasMany(Theory::class);
    }
}

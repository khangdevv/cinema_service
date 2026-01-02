<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Model;

class Theory extends Model
{
    protected $connection = 'tracnghiem';
    protected $table = 'theories';

    protected $fillable = [
        'topic_id',
        'title',
        'slug',
        'content',
        'order'
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}

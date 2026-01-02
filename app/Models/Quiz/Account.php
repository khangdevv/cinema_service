<?php

namespace App\Models\Quiz;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'accounts';

    protected $fillable = [
        'email',
        'google_id',
        'phone',
        'password',
        'full_name',
        'avatar',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'account_id');
    }

    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }
}

<?php

namespace App\Models\Quiz;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $connection = 'tracnghiem';
    protected $table = 'users';

    protected $fillable = [
        'google_id',
        'email',
        'name',
        'avatar',
        'role',
        'is_active'
    ];

    protected $hidden = [];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }

    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }
}

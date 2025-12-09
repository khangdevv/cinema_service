<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Account
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $google_id
 * @property string|null $phone
 * @property string|null $password_hash
 * @property string|null $full_name
 * @property string $role
 * @property bool $is_active
 *
 * @property Collection|Order[] $orders
 * @property Collection|SeatLock[] $seat_locks
 *
 * @package App\Models
 */
class Account extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

	protected $table = 'account';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'bool'
	];

    protected $hidden = [
        'password_hash',
    ];

	protected $fillable = [
		'email',
		'google_id',
		'phone',
		'password_hash',
		'full_name',
		'role',
		'is_active'
	];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function seat_locks()
	{
		return $this->hasMany(SeatLock::class);
	}
}

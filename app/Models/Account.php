<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 * 
 * @property int $id
 * @property string|null $email
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
class Account extends Model
{
	protected $table = 'account';
	public $timestamps = false;

	protected $casts = [
		'is_active' => 'bool'
	];

	protected $fillable = [
		'email',
		'phone',
		'password_hash',
		'full_name',
		'role',
		'is_active'
	];

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function seat_locks()
	{
		return $this->hasMany(SeatLock::class);
	}
}

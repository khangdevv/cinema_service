<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SeatLock
 * 
 * @property int $id
 * @property int $showtime_id
 * @property int $seat_id
 * @property int|null $account_id
 * @property Carbon $expires_at
 * 
 * @property Seat $seat
 * @property Showtime $showtime
 * @property Account|null $account
 *
 * @package App\Models
 */
class SeatLock extends Model
{
	protected $table = 'seat_lock';
	public $timestamps = false;

	protected $casts = [
		'showtime_id' => 'int',
		'seat_id' => 'int',
		'account_id' => 'int',
		'expires_at' => 'datetime'
	];

	protected $fillable = [
		'showtime_id',
		'seat_id',
		'account_id',
		'expires_at'
	];

	public function seat()
	{
		return $this->belongsTo(Seat::class);
	}

	public function showtime()
	{
		return $this->belongsTo(Showtime::class);
	}

	public function account()
	{
		return $this->belongsTo(Account::class);
	}
}

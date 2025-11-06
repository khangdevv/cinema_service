<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Showtime
 * 
 * @property int $id
 * @property int $movie_id
 * @property int $screen_id
 * @property Carbon $start_at
 * @property Carbon $end_at
 * @property int $base_price
 * @property string $status
 * 
 * @property Movie $movie
 * @property Screen $screen
 * @property Collection|Order[] $orders
 * @property Collection|SeatLock[] $seat_locks
 *
 * @package App\Models
 */
class Showtime extends Model
{
	protected $table = 'showtime';
	public $timestamps = false;

	protected $casts = [
		'movie_id' => 'int',
		'screen_id' => 'int',
		'start_at' => 'datetime',
		'end_at' => 'datetime',
		'base_price' => 'int'
	];

	protected $fillable = [
		'movie_id',
		'screen_id',
		'start_at',
		'end_at',
		'base_price',
		'status'
	];

	public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

	public function screen()
	{
		return $this->belongsTo(Screen::class);
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

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Seat
 * 
 * @property int $id
 * @property int $screen_id
 * @property string $row_label
 * @property int $seat_number
 * @property string $seat_type
 * @property bool $is_aisle
 * @property bool $is_blocked
 * 
 * @property Screen $screen
 * @property Collection|OrderLine[] $order_lines
 * @property Collection|SeatLock[] $seat_locks
 *
 * @package App\Models
 */
class Seat extends Model
{
	protected $table = 'seat';
	public $timestamps = false;

	protected $casts = [
		'screen_id' => 'int',
		'seat_number' => 'int',
		'is_aisle' => 'bool',
		'is_blocked' => 'bool'
	];

	protected $fillable = [
		'screen_id',
		'row_label',
		'seat_number',
		'seat_type',
		'is_aisle',
		'is_blocked'
	];

	public function screen()
	{
		return $this->belongsTo(Screen::class);
	}

	public function order_lines()
	{
		return $this->hasMany(OrderLine::class);
	}

	public function seat_locks()
	{
		return $this->hasMany(SeatLock::class);
	}
}

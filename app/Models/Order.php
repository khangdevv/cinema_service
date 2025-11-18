<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 *
 * @property int $id
 * @property string $channel
 * @property int|null $account_id
 * @property int|null $cashier_id
 * @property int $showtime_id
 * @property string $status
 * @property string|null $payment_method
 * @property int $total_amount
 *
 * @property Account|null $account
 * @property Showtime $showtime
 * @property Collection|OrderLine[] $order_lines
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';
	public $timestamps = false;

	protected $casts = [
		'account_id' => 'int',
		'cashier_id' => 'int',
		'showtime_id' => 'int',
		'total_amount' => 'int'
	];

	protected $fillable = [
		'channel',
		'account_id',
		'showtime_id',
		'status',
		'payment_method',
		'total_amount'
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function showtime()
	{
		return $this->belongsTo(Showtime::class);
	}

	public function order_lines()
	{
		return $this->hasMany(OrderLine::class);
	}
}
